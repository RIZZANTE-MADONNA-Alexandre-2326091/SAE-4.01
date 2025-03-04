<div class="inside" id="getting-started" data-current="current">

	<h2><?php esc_html_e('Getting Started', 'ics-calendar'); ?></h2>

	<p><?php
	/* translators: 1. HTML tag 2. HTML tag */
	printf(esc_html__('An extensive %1$sUser Guide%2$s is available on our website. Below are some quick links to key resources in the documentation.', 'ics-calendar'), '<strong><a href="https://icscalendar.com/user-guide/">', '</a></strong>');
	?></p>
	
	<ul>
		<?php do_action('r34ics_admin_getting_started_links_before'); ?>
		<li><a href="https://icscalendar.com/getting-started/" target="_blank"><?php esc_html_e('Getting Started', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/general-wordpress-settings/" target="_blank"><?php esc_html_e('General WordPress Settings', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/shortcode-overview/" target="_blank"><?php esc_html_e('Shortcode Overview', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/shortcode-builder/" target="_blank"><?php esc_html_e('Shortcode Builder', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/icsdocs/" target="_blank"><?php esc_html_e('Shortcode Parameters Reference', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/faqs-and-tips/" target="_blank"><?php esc_html_e('FAQs and Tips', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/css-guide/" target="_blank"><?php esc_html_e('CSS Guide', 'ics-calendar'); ?></a></li>
		<li><a href="https://icscalendar.com/developer/" target="_blank"><?php esc_html_e('Developer Resources', 'ics-calendar'); ?></a></li>
		<?php do_action('r34ics_admin_getting_started_links_after'); ?>
	</ul>

</div>
