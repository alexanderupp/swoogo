<?php
	spl_autoload_register(function($_className) {
		include_once __DIR__ . "/includes/" . $_className . ".php";
	});