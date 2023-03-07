<header id="event__info" class="pad-med col-cream">
	<h1 class="col-white font-accent"><?php echo $event->name; ?></h1>
	<p class="mb-1">
		<?php echo $event->the_description()?>
	</p>
	<p class="mb-2"><?php echo $event->venueType; ?>, <?php echo date("l, F jS Y", strtotime($event->time->date->start)); ?></p>
	<p class="flex flex--wrap">
		<a href="<?php echo $event->virtualLocation; ?>" target="_blank" class="button">Attend Event</a><a href="#sessions" class="button button--ghost btn-explore">Explore Sessions</a>
	</p>
</header>