<?php

	class API {
		private $api_url = "https://api.swoogo.com/api/v1";
		private $eventID;
		private $key;
		private $secret;
		private $token;

		public function __construct() {
			$configFilename = realpath(__DIR__ . "/../config.json");

			if(!file_exists($configFilename)) {
				die("Missing config.json.");
			}

			$config = @json_decode(file_get_contents($configFilename));

			if(empty($config->key) || empty($config->secret)|| empty($config->event_id)) {
				die("Please add the consumer key &amp; secret, and event ID to the config.json file.");
			}

			$this->key = urlencode($config->key);
			$this->secret = urlencode($config->secret);
			$this->eventID = $config->event_id;
		}

		// PUBLIC METHODS

		/**
		 * Obtains a bearer token from the Swoogo API
		 * 
		 * @return bool
		 */
		public function authenticate() : bool {
			// url encode the key and secret
			$_key = urlencode($this->key);
			$_secret = urlencode($this->secret);

			// All of this session stuff is just to cut down in API calls during dev.
			// It's a little quicker and I don't feel like I'm hammering Swoogo's API
			if(empty(session_id())) {
				session_start();
			}

			if(!empty($_SESSION["token_expires"])) {
				if($_SESSION["token_expires"] > date("Y-m-d H:i:s")) {
					$this->token = (object)(array(
						"access_token" => $_SESSION["access_token"],
						"expires_at" => $_SESSION["token_expires"]
					));
					return $_SESSION["access_token"];
				}
			}

			$result = $this->send_request("/oauth2/token.json", [
				CURLOPT_USERPWD => $_key . ":" . $_secret,
				CURLOPT_POSTFIELDS => "grant_type=client_credentials",
				CURLOPT_CUSTOMREQUEST => "POST"
			]);

			if(!$result) {
				return false;
			}

			$this->token = $result;

			// covert UTC to local timezone
			$expires = new DateTime($this->token->expires_at, new DateTimeZone("UTC"));
			$expires->setTimezone(new DateTimeZone(date_default_timezone_get()));

			$_SESSION["token_expires"] = $expires->format("Y-m-d H:i:s");
			$_SESSION["access_token"] = $this->token->access_token;

			//print_r($_SESSION);

			return true;
		}

		/**
		 * Fetches the event details and returns them as an
		 * Event object
		 * 
		 * @return mixed		Event object on success, or false on error
		 */
		public function get_event() {
			$event = $this->send_request("/events/" . $this->eventID . ".json?expand=location,event_venue_type");
			
			$event = $_SESSION["event"];

			return $event ? new Event($event) : false;
		}

		/**
		 * Returns an array of sessions
		 * 
		 * @param int $_page			Page number
		 * @return array				Sessions array
		 */
		public function get_sessions(int $_page = 1) : array {
			$fields = array(
				"id",
				"description",
				"name",
				"date",
				"start_time",
				"end_time"
			);

			$request = $this->send_request("/sessions.json?event_id=" . $this->eventID . "&expand=speakers&fields=" . implode(",", $fields));

			if(empty($request->items)) {
				return [];
			}

			$sessionIDs = [];
			$speakers = [];
			$return = [];

			foreach($request->items as $index => $session) {
				$return[$index] = new Session($session);

				foreach($session->speakers as $speaker) {
					if(!isset($speakers[$speaker->id])) {
						$speakers[$speaker->id] = new Speaker($speaker);
					}

					$return[$index]->add_speaker($speakers[$speaker->id]);
				}
			}

			return $return;
		}

		/**
		 * Returns the access token
		 * 
		 * @return array		Access token if authenticated. Empty string otherwise
		 */
		public function get_token() : string {
			return $this->token->access_token ?? "";
		}

		// PRIVATE METHODS

		/**
		 * Sends a cURL request to the api and returns the response array
		 * 
		 * @param string $_endpoint		API endpoint
		 * @param array $_options		cURL options
		 * @return mixed				Response array, or false on error
		 */
		private function send_request(string $_endpoint, array $_options = []) {
			$curl = curl_init();
			$args = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => $this->api_url . $_endpoint,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => "GET",
			);

			if($this->token) {
				$args[CURLOPT_HTTPHEADER] = ["Authorization:  Bearer " . $this->token->access_token];
			}

			if(!empty($_options)) {
				$args = $_options + $args;
			}

			curl_setopt_array($curl, $args);

			$result = curl_exec($curl);
			curl_close($curl);

			return $result ? @json_decode($result) : false;
		}

		/**
		 * Fetches multiple API requests to a single endpoint
		 * Uses a cURL multi handle for simultaneous requests
		 * 
		 * @param string $_resourceType		API resource type (sessions, speakers, etc)
		 * @param array $_IDs				Array of IDs return from Swoogo's API
		 * @return
		 */
		private function send_multi_request(string $_resourceType, array $_IDs) : array {
			$curlMulti = curl_multi_init();
			$curl = [];
			$results = [];

			for($i = 0, $total = count($_IDs); $i < $total; $i++) {
				$curl[$i] = curl_init();

				curl_setopt_array($curl[$i], [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_URL => $this->api_url . "/" . $_resourceType . "/" . $_IDs[$i] . ".json",
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => ["Authorization:  Bearer " . $this->token->access_token]
				]);
				curl_multi_add_handle($curlMulti, $curl[$i]);
			}

			$running = null;

			do {
				curl_multi_exec($curlMulti, $running);
			} while($running);

			foreach($curl as $handle) {
				curl_multi_remove_handle($curlMulti, $handle);
			}

			curl_multi_close($curlMulti);

			foreach($curl as $result) {
				$results[] = @json_decode(curl_multi_getcontent($result));
			}

			return $results;
		}
	}