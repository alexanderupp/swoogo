<?php

	class SessionManager {
		public $sessions = [];

		public function __construct(array $_sessions) {
			$this->sessions = $_sessions;
		}

		public function sort() {
			$newSessions = [];

			foreach($this->sessions as $session) {
				$hour = (int)date("G", strtotime($session->time->start));
				
				if(($hour >= 0) && ($hour <= 11)) {
					$label = "Morning";
				} else if(($hour >= 12) && ($hour <= 15)) {
					$label = "Afternoon";
				} else if(($hour >= 16) && ($hour <= 19)) {
					$label = "Evening";
				} else if(($hour >= 20) && ($hour <= 24)) {
					$label = "Night";
				} else {
					$label = "Another Dimension";
				}

				$newSessions[$label][] = $session;
			}

			$this->sessions = $newSessions;
		}
	}