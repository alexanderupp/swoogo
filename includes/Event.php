<?php

	class Event {
		private $id;

		public $description;
		public $location;
		public $name;
		public $time;
		public $link;
		public $virtualLocation;
		public $venueType;

		public function __construct(stdClass $_data) {
			$this->id = $_data->id;

			$this->description = $_data->description;
			$this->location = $_data->location;
			$this->name = $_data->name;
			$this->virtualLocation = $_data->swoogo_virtual_location;
			$this->venueType = $_data->event_venue_type;

			$this->time = (object)(array(
				"date" => (object)[
					"start" => $_data->start_date,
					"end" => $_data->end_date
				],
				"time" => (object)[
					"start" => $_data->start_time,
					"end" => $_data->end_time
				]
			));
		}

		public function is_virtual() : bool {
			switch($this->venueType) {
				case "Hybrid":
				case "Virtual":
					return true;
				default:
					return false;
			}
		}

		public function the_description() : string {
			if(empty($this->description)) {
				ob_start();
			?>
				This is where the description would go. If there was one!
			</p>
			<p class="mb-1">
				The description for this event was left blank.
			</p>
			<p>
				So instead, I'm going to ramble a bit so this section about the event isn't empty. Did you know PHP orginally stood for "Personal Home Page"?
			<?php
				$this->description = ob_get_clean();
			}

			return $this->description;
		}

		public function the_name() : string {
			return htmlspecialchars($this->name);
		}
	}
?>