<div class="details bg-white col-black" id="details-<?php echo $session->hashID; ?>">
	<a class="details__close toggle-details" data-for="details-<?php echo $session->hashID; ?>">CLOSE</a>
	<div class="details__content pad-med">
		<h2 class="fontsize-4 col-dark-blue mb-sm font-accent"><?php echo $session->the_name(); ?></h2>
		<p class="mb-sm">
		<?php echo date("g:i A", strtotime($session->time->start)) . " - " . date("g:i A", strtotime($session->time->end)); ?> | <?php echo $session->get_duration(); ?> minutes<?php if($session->speakerCount > 0) { echo " | " . $session->speakerCount; ?> speaker<?php echo $session->speakerCount > 1 ? "s" : ""; } ?>
		</p>
		<?php if($session->speakerCount > 0) { ?>
		<p class="mb-med">
			<?php echo $session->formatted_description(); ?>
		</p>
		<div class="flex flex--center">
			<?php
				foreach($session->speakers as $speaker) {
			?>
			<div class="speaker column">
				<img src="<?php echo $speaker->image; ?>" class="block mb-sm mx-auto speaker-image"/>
				<h4 class="fontsize-2 font-accent txt-center"><?php print $speaker->the_name(); ?></h4>
				<p class="pad-sm txt-center">
					<?php echo $speaker->company->title . ", " . $speaker->company->name; ?>
				</p>
			</div>
			<?php
				}
			?>
		</div>
		<?php } ?>
	</div>
</div>