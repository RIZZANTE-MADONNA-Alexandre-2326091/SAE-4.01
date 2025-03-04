<div class="inside" id="utilities">

	<h2><?php esc_html_e('Utilities', 'ics-calendar'); ?></h2>

	<div id="data-cache">
	
		<h3><?php
		/* translators: 1: Plugin name (do not translate) */
		printf(esc_html__('%1$s Data Cache', 'ics-calendar'), 'ICS Calendar');
		?></h3>
	
		<form id="r34ics-purge-calendar-transients" method="post" action="#utilities">
			<?php
			wp_nonce_field('r34ics', 'r34ics-purge-calendar-transients-nonce');
			?>
			<p><input type="submit" class="button button-primary" value="<?php esc_attr_e('Clear Cached Calendar Data', 'ics-calendar'); ?>" /></p>
			<p><?php esc_html_e('This will immediately clear all existing cached calendar data (purge transients), forcing WordPress to reload all calendars the next time they are viewed. Caching will then resume as before.', 'ics-calendar'); ?></p>
		</form>
	
	</div>
		
	<div id="ics-feed-url-tester">
	
		<h3><?php esc_html_e('ICS Feed URL Tester', 'ics-calendar'); ?></h3>
	
		<p><?php esc_html_e('If you are concerned that the plugin is not properly retrieving your feed, you can test the URL here.', 'ics-calendar'); ?></p>
	
		<form id="r34ics-url-tester" method="post" action="#utilities">
			<?php
			wp_nonce_field('r34ics', 'r34ics-url-tester-nonce');
			?>
			<div class="r34ics-input">
				<label for="r34ics-url-tester-url_to_test"><input type="text" name="url_to_test" id="r34ics-url-tester-url_to_test" value="<?php if (!empty($url_tester_result['url'])) { echo esc_url($url_tester_result['url']); } ?>" placeholder="<?php esc_attr_e('Enter feed URL...', 'ics-calendar'); ?>" style="width: 50%;" /></label> <input type="submit" class="button button-primary" value="<?php esc_attr_e('Test URL', 'ics-calendar'); ?>" />
			</div>
		</form>
		
		<?php
		if (!empty($url_tester_result)) {
			?>
			<div class="r34ics-inner-box">
				<h3><?php esc_html_e('Results:', 'ics-calendar'); ?></h3>
				<?php
				if (!empty($url_tester_result['size'])) {
					?>
					<p><mark class="success"><?php
					/* translators: 1: Dynamic value */
					printf(esc_html__('%1$s received.', 'ics-calendar'), wp_kses_post($url_tester_result['size'] ?: ''));
					?></mark></p>
					<?php
				}
				switch ($url_tester_result['status']) {
					case 'valid':
						?>
						<p><mark class="success"><?php esc_html_e('This appears to be a valid ICS feed URL.', 'ics-calendar'); ?></mark></p>
						<?php
						break;
					case 'invalid':
						?>
						<p><mark class="error"><?php esc_html_e('This does not appear to be a valid ICS feed URL.', 'ics-calendar'); ?></mark></p>
						<?php
						break;
					case 'failed':
						?>
						<p><mark class="error"><?php esc_html_e('Could not retrieve data from the requested URL.', 'ics-calendar'); ?></mark></p>
						<?php
						break;
					case 'unknown':
						?>
						<p><mark class="error"><?php esc_html_e('An unknown error occurred while attempting to retrieve the requested URL.', 'ics-calendar'); ?></mark></p>
						<?php
						break;
					default: break;
				}
				if (!empty($url_tester_result['special'])) {
					foreach ((array)$url_tester_result['special'] as $item) {
						?>
						<p><mark class="alert"><?php echo wp_kses_post($item ?: ''); ?></mark></p>
						<?php
					}
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
	
	<?php do_action('r34ics_admin_utilities_more'); ?>

</div>

<?php
// Restrict System Report to admins / super admins
if	(
			(is_multisite() && current_user_can('setup_network')) ||
			(!is_multisite() && current_user_can('manage_options'))
		)
{
	?>	
	<div class="inside" id="system-report">

		<h2><?php esc_html_e('System Report', 'ics-calendar'); ?></h2>

		<p><mark class="alert"><?php esc_html_e('Please copy the following text and include it in your message when emailing support.', 'ics-calendar'); ?><br />
		<?php
		/* translators: 1: Plugin name (do not translate) */
		printf(esc_html__('Also please include the %1$s shortcode exactly as you have it entered on the affected page.', 'ics-calendar'), 'ICS Calendar');
		?></mark><br /><mark class="error"><?php esc_html_e('For your site security please do NOT post the System Report in the support forums.', 'ics-calendar'); ?></mark></p>

		<textarea class="diagnostics-window" readonly="readonly" style="cursor: copy;" onclick="this.select(); document.execCommand('copy');"><?php r34ics_system_report(); ?></textarea>

	</div>
	<?php
}
?>