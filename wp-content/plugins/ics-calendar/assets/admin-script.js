/*! ICS Calendar admin scripts https://icscalendar.com */


jQuery(function() {

	// Admin page tabbed navigation
	jQuery('.r34ics-menu a, .wrap.r34ics a[href^="#"]').on('click', function() {
		var href = jQuery(this).attr('href');
		jQuery(href).attr('data-current', 'current').siblings().removeAttr('data-current');
		jQuery('.r34ics-menu a[href="' + href + '"]').attr('data-current', 'current').parent().siblings().find('a').removeAttr('data-current');
		if (history.pushState) {
			window.history.pushState({}, document.title, href);
		}
		return false;
	});
	
	// Admin "Add ICS Calendar" button
	jQuery('#poststuff').on('click', '.add_r34ics', function() { // Delegate event for editors with delayed initialization (e.g. ACF)
		jQuery('#insert_r34ics').addClass('open');
	});
	jQuery('#insert_r34ics_close, #insert_r34ics_overlay').on('click', function() {
		jQuery('#insert_r34ics').removeClass('open');
	});
	jQuery('#insert_r34ics_form').on('submit', function() {
	
		// Saved calendars are handled by the Pro plugin
		if (jQuery(this).hasClass('saved_calendar')) { return false; }
		
		// Validate required fields
		if (jQuery('#insert_r34ics_url').val() == '' && jQuery('#insert_r34ics_id').length == 0 || jQuery('#insert_r34ics_id').val() == '') {
			alert('ICS Subscribe URL is required.');
			jQuery('#insert_r34ics_url').focus();
			return false;
		}
		
		// Concatenate shortcode
		var r34ics_shortcode = '[ics_calendar url="' + jQuery('#insert_r34ics_url').val().replace('"','') + '"';
		if (jQuery('#insert_r34ics_title').val() != '') {
			r34ics_shortcode += ' title="' + jQuery('#insert_r34ics_title').val().replace('"','') + '"';
		}
		if (jQuery('#insert_r34ics_description').val() != '') {
			r34ics_shortcode += ' description="' + jQuery('#insert_r34ics_description').val().replace('"','') + '"';
		}
		if (jQuery('#insert_r34ics_view').val() != '') {
			r34ics_shortcode += ' view="' + jQuery('#insert_r34ics_view').val().replace('"','') + '"';
		}
		if (jQuery('#insert_r34ics_view').val() == 'list' && parseInt(jQuery('#insert_r34ics_count').val()) > 0) {
			r34ics_shortcode += ' count="' + parseInt(jQuery('#insert_r34ics_count').val()) + '"';
		}
		if (jQuery('#insert_r34ics_view').val() == 'list' && jQuery('#insert_r34ics_format').val() != '') {
			r34ics_shortcode += ' format="' + jQuery('#insert_r34ics_format').val().replace('"','') + '"';
		}
		if (jQuery('#insert_r34ics_eventdesc').prop('checked') == true) {
			r34ics_shortcode += ' eventdesc="true"';
		}
		if (jQuery('#insert_r34ics_location').prop('checked') == true) {
			r34ics_shortcode += ' location="true"';
		}
		if (jQuery('#insert_r34ics_organizer').prop('checked') == true) {
			r34ics_shortcode += ' organizer="true"';
		}

		// @todo Add a way to insert WP hook here
					
		r34ics_shortcode += ']';
	
		// Insert shortcode and close window
		window.send_to_editor(r34ics_shortcode);
		jQuery('#insert_r34ics_form')[0].reset();
		jQuery('#r34ics_list_view_options').hide();
		jQuery('#insert_r34ics').removeClass('open');
		return false;
	});

});

document.addEventListener("DOMContentLoaded", function() {

	// Load page with correct tab selected
	if (jQuery('.r34ics').length > 0) {
		setTimeout(function() {
			var initial_tab = (location.hash && jQuery('.r34ics-menu a[href="' + location.hash + '"]').length > 0) ? location.hash : jQuery('.r34ics-menu a:first-of-type').attr('href');
			jQuery('.r34ics-menu a[href="' + initial_tab + '"]').trigger('click');
			window.scrollTo(-100, -100);
		}, 1);
	}

});
