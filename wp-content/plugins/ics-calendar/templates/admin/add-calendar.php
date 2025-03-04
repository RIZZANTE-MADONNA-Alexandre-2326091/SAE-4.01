<div id="insert_r34ics">
	<div id="insert_r34ics_overlay"></div>
	<div id="insert_r34ics_window">

			<div id="insert_r34ics_header">
				<strong><?php
				/* translators: 1: Plugin name (do not translate) */
				printf(esc_html__('Add %1$s', 'ics-calendar'), 'ICS Calendar');
				?></strong>
				<div id="insert_r34ics_close" title="<?php esc_attr_e('Close', 'ics-calendar'); ?>">&times;</div>
			</div>

			<div id="insert_r34ics_content">
				<form action="#" method="get" id="insert_r34ics_form">
				
					<?php do_action('r34ics_admin_add_calendar_settings_html'); ?>
					
					<p class="field-block">
						<label for="insert_r34ics_url"><?php esc_html_e('ICS Feed URL:', 'ics-calendar'); ?></label><br />
						<input id="insert_r34ics_url" name="insert_r34ics_url" type="text" style="width: 100%;" /><br />
						<em><small><?php
						/* translators: 1. HTML tag 2. HTML tag 3. HTML tag 4. HTML tag 5. HTML tag 6. Do not translate */
						printf(esc_html__('Be sure you are using a %1$ssubscribe%2$s URL, not a %3$sweb view%4$s URL.%5$s (Entering the URL directly in your web browser should download an %6$s file.)', 'ics-calendar'), '<strong>', '</strong>', '<strong>', '</strong>', '<br />', '<code>.ics</code>');
						?></small></em>
					</p>
					
					<p class="field-block">
						<label for="insert_r34ics_title"><?php esc_html_e('Calendar Title:', 'ics-calendar'); ?></label><br />
						<input id="insert_r34ics_title" name="insert_r34ics_title" type="text" style="width: 100%;" /><br />
						<em><small><?php
						/* translators: 1. Do not translate */
						printf(esc_html__('Leave empty to use calendar&rsquo;s default title. Enter %1$s to omit title altogether.', 'ics-calendar'), '<code>none</code>');
						?></small></em>
					</p>
					
					<p class="field-block">
						<label for="insert_r34ics_description"><?php esc_html_e('Calendar Description:', 'ics-calendar'); ?></label><br />
						<input id="insert_r34ics_description" name="insert_r34ics_description" type="text" style="width: 100%;" /><br />
						<em><small><?php
						/* translators: 1. Do not translate */
						printf(esc_html__('Leave empty to use calendar&rsquo;s default description. Enter %1$s to omit description altogether.', 'ics-calendar'), '<code>none</code>');
						?></small></em>
					</p>
					
					<p class="field-block">
						<label for="insert_r34ics_view"><?php esc_html_e('View:', 'ics-calendar'); ?></label><br />
						<select id="insert_r34ics_view" name="insert_r34ics_view" onchange="if (jQuery(this).val() == 'list') { jQuery('#r34ics_list_view_options').show(); } else { jQuery('#r34ics_list_view_options').hide(); }">
							<option value="month"><?php esc_html_e('month', 'ics-calendar'); ?></option>
							<option value="list"><?php esc_html_e('list', 'ics-calendar'); ?></option>
							<option value="week"><?php esc_html_e('week', 'ics-calendar'); ?></option>
						</select><br />
					</p>
					
					<p class="field-block" id="r34ics_list_view_options" style="display: none;">
						<label for="insert_r34ics_count"><?php esc_html_e('Count:', 'ics-calendar'); ?></label>
						<input id="insert_r34ics_count" name="insert_r34ics_count" type="number" min="1" step="1" />
						&nbsp;&nbsp;
						<label for="insert_r34ics_format"><?php esc_html_e('Format:', 'ics-calendar'); ?></label>
						<input id="insert_r34ics_format" name="insert_r34ics_format" type="text" value="l, F j" /><br />
						<em><small><?php
						/* translators: 1. Additional translation string and HTML tags 2. Additional translation string and HTML tags 3. HTML tag 4. HTML tag */
						printf(esc_html__('Leave %1$s blank to include all upcoming events. %2$s must be a standard %3$sPHP date format string%4$s.', 'ics-calendar'), '<strong>' . esc_html__('Count:', 'ics-calendar') . '</strong>', '<strong>' . esc_html__('Format:', 'ics-calendar') . '</strong>', '<a href="https://secure.php.net/manual/en/function.date.php" target="_blank">', '</a>');
						?></small></em>
					</p>
					
					<p class="field-block">
						<input id="insert_r34ics_eventdesc" name="insert_r34ics_eventdesc" type="checkbox" onchange="if (this.checked) { jQuery('#insert_r34ics_toggle_wrapper').show(); } else if (!this.checked && !jQuery('#insert_r34ics_organizer').prop('checked') && !jQuery('#insert_r34ics_location').prop('checked')) { jQuery('#insert_r34ics_toggle_wrapper').hide(); }" />
						<label for="insert_r34ics_eventdesc"><?php
						/* translators: 1. HTML tag 2. HTML tag */
						printf(esc_html__('Show event descriptions %1$s(change to a number in inserted shortcode to set word limit)%2$s', 'ics-calendar'), '<em><small>', '</small></em>');
						?></label>
					</p>
				
					<p class="field-block">
						<input id="insert_r34ics_location" name="insert_r34ics_location" type="checkbox" onchange="if (this.checked) { jQuery('#insert_r34ics_toggle_wrapper').show(); } else if (!this.checked && !jQuery('#insert_r34ics_organizer').prop('checked') && !jQuery('#insert_r34ics_eventdesc').prop('checked')) { jQuery('#insert_r34ics_toggle_wrapper').hide(); }" />
						<label for="insert_r34ics_location"><?php
						/* translators: 1. HTML tag 2. HTML tag */
						printf(esc_html__('Show event locations %1$s(if available)%2$s', 'ics-calendar'), '<em><small>', '</small></em>');
						?></label>
					</p>
					
					<p class="field-block">
						<input id="insert_r34ics_organizer" name="insert_r34ics_organizer" type="checkbox" onchange="if (this.checked) { jQuery('#insert_r34ics_toggle_wrapper').show(); } else if (!this.checked && !jQuery('#insert_r34ics_location').prop('checked') && !jQuery('#insert_r34ics_eventdesc').prop('checked')) { jQuery('#insert_r34ics_toggle_wrapper').hide(); }" />
						<label for="insert_r34ics_organizer"><?php
						/* translators: 1. HTML tag 2. HTML tag */
						printf(esc_html__('Show event organizers %1$s(if available)%2$s', 'ics-calendar'), '<em><small>', '</small></em>');
						?></label>
					</p>
					
					<p class="field-block">
						<small><?php
						/* translators: 1. HTML tag 2. HTML tag 3. HTML tag 4. HTML tag */
						printf(esc_html__('%1$sNote:%2$s Additional %3$sdisplay options%4$s are available by manually editing the shortcode after insertion.', 'ics-calendar'), '<strong>', '</strong>', '<a href="admin.php?page=ics-calendar#event-display-options" target="_blank">', '</a>');
						?></small>
					</p>
					
					<p style="text-align: right;">
						<input name="insert" type="submit" class="button button-primary button-large" value="<?php
						/* translators: 1: Plugin name (do not translate) */
						printf(esc_attr__('Insert %1$s', 'ics-calendar'), 'ICS Calendar');
						?>" />
					</p>

				</form>
			</div>

	</div>
</div>
