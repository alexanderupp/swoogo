<?php
	
	class Speaker {
		public $bio;
		public $company;
		public $image;
		public $link;
		public $name;

		private $id;

		public function __construct(stdClass $_data) {
			$this->id = $_data->id;
			$this->bio = $_data->bio;
			$this->image = $_data->profile_picture;
			$this->link = $_data->direct_link;

			$this->company = (object)(array(
				"name" => $_data->company,
				"title" => $_data->job_title
			));

			$this->name = (object)(array(
				"first" => $_data->first_name,
				"middle" => $_data->middle_name,
				"last" => $_data->last_name
			));
		}

		/**
		 * Returns an HTML safe Speaker name
		 */
		public function the_name() : string {
			return htmlspecialchars(implode(" ", (array)($this->name)));
		}
	}