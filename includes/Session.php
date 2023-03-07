<?php

	class Session {
		private $id;

		public $description;
		public $hashID;
		public $isDescriptionShortened = false;
		public $link;
		public $name;
		public $speakers = [];
		public $speakerCount = 0;
		public $time;

		public function __construct(stdClass &$_data) {
			$this->id = $_data->id;

			$this->description = strip_tags($_data->description);
			$this->hashID = hash("md5", $_data->name);
			$this->name = $_data->name;

			$this->time = (object)(array(
				"date" => $_data->date,
				"start" => $_data->start_time,
				"end" => $_data->end_time,
				//"timezone" => $_data->timezone
			));
		}

		public function add_speaker(Speaker &$_speaker) {
			$this->speakers[] = $_speaker;
			$this->speakerCount++;
		}

		public function formatted_description(int $_length = 180) : string{
			return trim(strip_tags($this->description));

			/*if(strlen($description) > $_length) {
				$this->isDescriptionShortened = true;
				$break = $_length;

				for($i = $_length - 1; $i > 0; $i--) {
					if($description[$i] == " ") {
						$break = $i;
						break;
					}
				}

				return substr($description, 0, $break) . "...";
			} else {
				return $description;
			}*/
		}

		public function get_duration() : string {
			$start = strtotime($this->time->start);
			$end = strtotime($this->time->end);

			return ($end - $start) / 60;
		}

		public function the_name() : string {
			return htmlspecialchars($this->name);
		}
	}