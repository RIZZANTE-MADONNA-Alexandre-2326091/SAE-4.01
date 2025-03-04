<div class="postbox"><div class="inside">

	<a href="https://icscalendar.com/" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/ics-calendar-logo-2023.svg', dirname(dirname(__FILE__)))); ?>" alt="ICS Calendar" style="display: block; height: auto; margin: 0 auto 1.5em auto; width: 180px;" width="180" height="62" /></a>
	
	<h3><?php esc_html_e('User Guide', 'ics-calendar'); ?></h3>
	
	<p><?php esc_html_e('Our complete user guide is available with full translation into dozens of languages on our website:', 'ics-calendar'); ?><br />
	<strong><a href="https://icscalendar.com/user-guide/">icscalendar.com/user-guide</a></strong></p>
	
	<h3><?php esc_html_e('Support', 'ics-calendar'); ?></h3>
	
	<p><?php
	/* translators: 1. HTML tag 2. HTML tag */
	printf(esc_html__('For assistance, please use our %1$ssupport request form%2$s.', 'ics-calendar'), '<strong><a href="https://icscalendar.com/support/" target="_blank" style="white-space: nowrap;">', '</a></strong>');
	?></p>
	
	<?php
	// Restrict System Report to admins / super admins
	if	(
				(is_multisite() && current_user_can('setup_network')) ||
				(!is_multisite() && current_user_can('manage_options'))
			)
	{
		?>
		<p><mark class="alert"><?php
		/* translators: 1. HTML tag 2. HTML tag */
		printf(esc_html__('When contacting support, please include the %1$sSystem Report%2$s from this page.', 'ics-calendar'), '<a href="#system-report" style="white-space: nowrap;">', '</a>');
		?></mark></p>
		<?php
	}
	?>

</div></div>

<div class="postbox"><div class="inside">

	<h3 style="text-align: center;"><?php esc_html_e('Do even more with...', 'ics-calendar'); ?></h3>

	<a href="https://icscalendar.com/pro" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/ics-calendar-pro-logo-2023.svg', dirname(dirname(__FILE__)))); ?>" alt="ICS Calendar Pro" style="display: block; height: auto; margin: 1.5em auto; width: 180px;" width="180" height="62" /></a>
	
	<h4 style="margin-bottom: 0;"><?php esc_html_e('ICS Events', 'ics-calendar'); ?></h4>
	<p style="margin-top: 0;"><?php
	/* translators: 1: HTML tag 2: HTML tag 3: Plugin name (do not translate) */
	printf(esc_html__('%1$sNew!%2$s Turns %3$s into a full calendar system. Create and manage events directly in WordPress and even integrate them seamlessly with your existing feeds.', 'ics-calendar'), '<strong style="color: crimson;">', '</strong>', 'ICS Calendar Pro');
	?></p>
	<h4 style="margin-bottom: 0;"><?php esc_html_e('Additional Views', 'ics-calendar'); ?></h4>
	<p style="margin-top: 0;"><?php esc_html_e('Full, Up Next, Masonry, Month with Sidebar, Widget, and more.', 'ics-calendar'); ?></p>
	<h4 style="margin-bottom: 0;"><?php esc_html_e('Calendar Builder', 'ics-calendar'); ?></h4>
	<p style="margin-top: 0;"><?php esc_html_e('Configure all calendar settings with an easy visual interface.', 'ics-calendar'); ?></p>
	<h4 style="margin-bottom: 0;"><?php esc_html_e('Customizer', 'ics-calendar'); ?></h4>
	<p style="margin-top: 0;"><?php esc_html_e('Easily modify your calendar color palettes, fonts, and more, site-wide.', 'ics-calendar'); ?></p>
	<h4 style="margin-bottom: 0;"><?php esc_html_e('Enhanced Features', 'ics-calendar'); ?></h4>
	<p style="margin-top: 0;"><?php esc_html_e('Additional capabilities are added to the core Month, Basic, List and Week views.', 'ics-calendar'); ?></p>
	<p><?php
	/* translators: 1. Link (do not translate) */
	printf(esc_html__('Visit %1$s to learn more.', 'ics-calendar'), '<strong><a href="https://icscalendar.com/pro/" target="_blank">icscalendar.com/pro</a></strong>');
	?></p>
	
	<p style="text-align: center;"><a href="https://icscalendar.com/pro/" target="_blank" class="button button-primary"><?php esc_html_e('Go PRO!', 'ics-calendar'); ?></a></p>

</div></div>

<div class="postbox"><div class="inside">

	<a href="https://room34.com/" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/room34-logo-on-white.svg', dirname(dirname(__FILE__)))); ?>" alt="Room 34 Creative Services" style="display: block; height: auto; margin: 1.5em auto; width: 180px;" width="180" height="55" /></a> 
			
	<p style="text-align: center;">ICS Calendar v. <?php echo esc_html(get_option('r34ics_version')); ?></p>

</div></div>
