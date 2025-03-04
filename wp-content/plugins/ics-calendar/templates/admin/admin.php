<?php
global $R34ICS2;
?>

<div class="wrap r34ics">

	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
	
	<div class="metabox-holder columns-2">
	
		<div class="column-1">
		
			<div class="postbox">
		
				<nav class="r34ics-menu"><ul>
					<li><a href="#getting-started"><?php esc_html_e('Getting Started', 'ics-calendar'); ?></a></li>
					<li><a href="#utilities"><?php esc_html_e('Utilities', 'ics-calendar'); ?></a></li>
					<li><a href="#system-report"><?php esc_html_e('System Report', 'ics-calendar'); ?></a></li>
					<li><a href="#settings"><?php esc_html_e('Settings', 'ics-calendar'); ?></a></li>
				</ul></nav>
			
				<?php include_once(plugin_dir_path(__FILE__) . 'getting-started.php'); ?>
	
				<?php include_once(plugin_dir_path(__FILE__) . 'utilities.php'); ?>
	
				<?php
				if (current_user_can('manage_options')) {
					?>
					<div class="inside" id="settings">
	
						<h2><?php esc_html_e('Settings', 'ics-calendar'); ?></h2>
	
						<form id="r34ics-settings" method="post" action="#settings">
							<?php
							wp_nonce_field('r34ics', 'r34ics-settings-nonce');
						
							include_once(plugin_dir_path(__FILE__) . 'settings.php');
							?>
	
							<p><input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'ics-calendar'); ?>" /></p>
						</form>
	
					</div>
					<?php
				}
				?>
			
			</div>
	
		</div>
	
		<div class="column-2">

			<?php include_once(plugin_dir_path(__FILE__) . 'sidebar.php'); ?>
	
		</div>
	
	</div>

</div>