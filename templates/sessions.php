<main id="event__sessions" class="pad-med">
	<h3 class="fontsize-4 font-accent mb-sm">Sessions</h3>
	<?php
		
		$sessionManager = new SessionManager($allSessions);
		$sessionManager->sort();

		foreach($sessionManager->sessions as $time => $sessions) {
	?>
	<div class="mb-lg row row-2">
		<h4 class="fontsize-3 column span-2 font-accent"><?php echo $time; ?></h4>
		<?php
			foreach($sessions as $session) {
		?>
			<a href="javascript:void(0)" class="block pad-sm bg-black session toggle-details" data-for="details-<?php echo $session->hashID; ?>">
				<p class="bold mb-sm"><?php echo $session->the_name(); ?></p>
				<p class="mb-sm">
				<?php echo date("g:i A", strtotime($session->time->start)) . " - " . date("g:i A", strtotime($session->time->end)); ?> |  <?php echo $session->get_duration(); ?> minutes
				</p>
				<p><?php if($session->speakerCount > 0) { echo $session->speakerCount; ?> speaker<?php echo $session->speakerCount > 1 ? "s" : ""; } ?></p>
			</a>
		<?php 
				include __DIR__ . "/details.php";
			}
		?>
	</div>
	<?php
		}
	?>
</main>