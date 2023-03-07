<?php
	/********************************************************************************
	 * For simplicity's sake let's do a lot of stuff here in the index file.
	 * Normally business logic and pages should be separated out much more
	 * thoroughly, including API keys. Which should be stored somewhere more secure.
	 ********************************************************************************/

	if(isset($_GET["flush"])) {
		session_start();
		session_destroy();
	}

	include "autoload.php";

	$page = $_GET["page"] ?? 1;
	$api = new API();

	$api->authenticate("m0P5kmS2TuVskHol2gqR9", "s__NIsJD6dEu7Ver1qRLO79RiUdid8C_ijs7j6ziej");

	$event = $api->get_event();
	$allSessions = $api->get_sessions($page);

	header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta name="viewport" content="width=device-width; initial-scale=1.0"/>
		<title><?php echo $event->the_name() ; ?> - Swoogo Technical Exercise -  Alex Rupp</title>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;700&family=VT323&display=swap" rel="stylesheet">

		<link rel="stylesheet" type="text/css" href="css/styles.css"/>
	</head>
	<body id="event" class="col-cream font-main">
		<?php include "templates/event-info.php"; ?>
		<?php include "templates/sessions.php"; ?>
		<?php
			/*$sessionManager = new SessionManager($allSessions);
			$sessionManager->sort();

			foreach($sessionManager->sessions as $time => $sessions) {
		?>
			<section class="container">
				<h5 class="header header--level2"><?php echo $time; ?></h5>
				<div class="sessions-container">
			<?php 
				foreach($sessions as $session) {
					include "templates/session-card.php";
				}
			?>
				</div>
			</section>
		<?php
			}*/
		?>
		<script src="js/event.js"></script>
	</body>
</html>