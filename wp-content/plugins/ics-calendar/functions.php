<?php

// Allowed HTML tags for heading substitutions
function r34ics_allowed_heading_tags() {
	return array('h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span');
}


// Check a heading tag to make sure it's allowed, with an optional fallback default
function r34ics_allowed_heading_tags_check($tag='', $default='div') {
	$allowed = r34ics_allowed_heading_tags();
	if (in_array($tag, $allowed)) { return $tag; }
	elseif (in_array($default, $allowed)) { return $default; }
	return 'div';
}


// Check for boolean values in shortcode
function r34ics_boolean_check($val) {
	$check = strtolower(trim(wp_strip_all_tags((string)$val)));
	if ($check === '1' || $check === 'true' || $check === 'on') { return true; }
	if ($check === '0' || $check === 'false' || $check === 'off' || $check === 'none') { return false; }
	if ($check === 'null' || $check === '') { return null; }
	return (bool)$val;
}


// Sanitize a hex color string
function r34ics_color_hex_sanitize($color='') {
	if (empty($color)) { return ''; }
	// Allow 'transparent' as-is
	if ($color == 'transparent') { return $color; }
	// Strip invalid characters from string
	$output = preg_replace('/[^0-9a-f]/', '', $color);
	// Convert 3-character hex to 6-character hex
	if (strlen($output ?? '') == 3) {
		$output = str_repeat(substr($output,0,1),2) . str_repeat(substr($output,1,1),2) . str_repeat(substr($output,2,1),2);
	}
	// If after all of the above we still have 6-character hex, return it with preceding #; otherwise return null
	return (strlen($output ?? '') == 6) ? '#' . $output : '';
}


// Return allowed fields array for wp_kses() with methods like R34ICS2::color_key_html()
function r34ics_color_key_allowed() {
	return array_merge(wp_kses_allowed_html('post'),
		array(
			'label' => array(
				'for' => array(),
			),
			'input' => array(
				'type' => array(),
				'id' => array(),
				'class' => array(),
				'data-feed-key' => array(),
				'checked' => array(),
				'for' => array(),
			),
		)
	);
}


// Return the hex value for any HTML named color
// Based on: https://stackoverflow.com/a/5925612
function r34ics_color_name2hex($name) {
	$name = strtolower($name);
	$colors = array(
		'aliceblue' => '#f0f8ff',
		'antiquewhite' => '#faebd7',
		'aqua' => '#00ffff',
		'aquamarine' => '#7fffd4',
		'azure' => '#f0ffff',
		'beige' => '#f5f5dc',
		'bisque' => '#ffe4c4',
		'black' => '#000000',
		'blanchedalmond' => '#ffebcd',
		'blue' => '#0000ff',
		'blueviolet' => '#8a2be2',
		'brown' => '#a52a2a',
		'burlywood' => '#deb887',
		'cadetblue' => '#5f9ea0',
		'chartreuse' => '#7fff00',
		'chocolate' => '#d2691e',
		'coral' => '#ff7f50',
		'cornflowerblue' => '#6495ed',
		'cornsilk' => '#fff8dc',
		'crimson' => '#dc143c',
		'cyan' => '#00ffff',
		'darkblue' => '#00008b',
		'darkcyan' => '#008b8b',
		'darkgoldenrod' => '#b8860b',
		'darkgray' => '#a9a9a9',
		'darkgreen' => '#006400',
		'darkgrey' => '#a9a9a9',
		'darkkhaki' => '#bdb76b',
		'darkmagenta' => '#8b008b',
		'darkolivegreen' => '#556b2f',
		'darkorange' => '#ff8c00',
		'darkorchid' => '#9932cc',
		'darkred' => '#8b0000',
		'darksalmon' => '#e9967a',
		'darkseagreen' => '#8fbc8f',
		'darkslateblue' => '#483d8b',
		'darkslategray' => '#2f4f4f',
		'darkslategrey' => '#2f4f4f',
		'darkturquoise' => '#00ced1',
		'darkviolet' => '#9400d3',
		'deeppink' => '#ff1493',
		'deepskyblue' => '#00bfff',
		'dimgray' => '#696969',
		'dimgrey' => '#696969',
		'dodgerblue' => '#1e90ff',
		'firebrick' => '#b22222',
		'floralwhite' => '#fffaf0',
		'forestgreen' => '#228b22',
		'fuchsia' => '#ff00ff',
		'gainsboro' => '#dcdcdc',
		'ghostwhite' => '#f8f8ff',
		'gold' => '#ffd700',
		'goldenrod' => '#daa520',
		'gray' => '#808080',
		'green' => '#008000',
		'greenyellow' => '#adff2f',
		'grey' => '#808080',
		'honeydew' => '#f0fff0',
		'hotpink' => '#ff69b4',
		'indianred' => '#cd5c5c',
		'indigo' => '#4b0082',
		'ivory' => '#fffff0',
		'khaki' => '#f0e68c',
		'lavender' => '#e6e6fa',
		'lavenderblush' => '#fff0f5',
		'lawngreen' => '#7cfc00',
		'lemonchiffon' => '#fffacd',
		'lightblue' => '#add8e6',
		'lightcoral' => '#f08080',
		'lightcyan' => '#e0ffff',
		'lightgoldenrodyellow' => '#fafad2',
		'lightgray' => '#d3d3d3',
		'lightgreen' => '#90ee90',
		'lightgrey' => '#d3d3d3',
		'lightpink' => '#ffb6c1',
		'lightsalmon' => '#ffa07a',
		'lightseagreen' => '#20b2aa',
		'lightskyblue' => '#87cefa',
		'lightslategray' => '#778899',
		'lightslategrey' => '#778899',
		'lightsteelblue' => '#b0c4de',
		'lightyellow' => '#ffffe0',
		'lime' => '#00ff00',
		'limegreen' => '#32cd32',
		'linen' => '#faf0e6',
		'magenta' => '#ff00ff',
		'maroon' => '#800000',
		'mediumaquamarine' => '#66cdaa',
		'mediumblue' => '#0000cd',
		'mediumorchid' => '#ba55d3',
		'mediumpurple' => '#9370d0',
		'mediumseagreen' => '#3cb371',
		'mediumslateblue' => '#7b68ee',
		'mediumspringgreen' => '#00fa9a',
		'mediumturquoise' => '#48d1cc',
		'mediumvioletred' => '#c71585',
		'midnightblue' => '#191970',
		'mintcream' => '#f5fffa',
		'mistyrose' => '#ffe4e1',
		'moccasin' => '#ffe4b5',
		'navajowhite' => '#ffdead',
		'navy' => '#000080',
		'oldlace' => '#fdf5e6',
		'olive' => '#808000',
		'olivedrab' => '#6b8e23',
		'orange' => '#ffa500',
		'orangered' => '#ff4500',
		'orchid' => '#da70d6',
		'palegoldenrod' => '#eee8aa',
		'palegreen' => '#98fb98',
		'paleturquoise' => '#afeeee',
		'palevioletred' => '#db7093',
		'papayawhip' => '#ffefd5',
		'peachpuff' => '#ffdab9',
		'peru' => '#cd853f',
		'pink' => '#ffc0cb',
		'plum' => '#dda0dd',
		'powderblue' => '#b0e0e6',
		'purple' => '#800080',
		'rebeccapurple' => '#663399',
		'red' => '#ff0000',
		'rosybrown' => '#bc8f8f',
		'royalblue' => '#4169e1',
		'saddlebrown' => '#8b4513',
		'salmon' => '#fa8072',
		'sandybrown' => '#f4a460',
		'seagreen' => '#2e8b57',
		'seashell' => '#fff5ee',
		'sienna' => '#a0522d',
		'silver' => '#c0c0c0',
		'skyblue' => '#87ceeb',
		'slateblue' => '#6a5acd',
		'slategray' => '#708090',
		'slategrey' => '#708090',
		'snow' => '#fffafa',
		'springgreen' => '#00ff7f',
		'steelblue' => '#4682b4',
		'tan' => '#d2b48c',
		'teal' => '#008080',
		'thistle' => '#d8bfd8',
		'tomato' => '#ff6347',
		'turquoise' => '#40e0d0',
		'violet' => '#ee82ee',
		'wheat' => '#f5deb3',
		'white' => '#ffffff',
		'whitesmoke' => '#f5f5f5',
		'yellow' => '#ffff00',
		'yellowgreen' => '#9acd32'
	);
	return isset($colors[$name]) ? $colors[$name] : $name;
}


// Build array of base and accent colors
function r34ics_color_set($colors, $alpha=0.75, $highlight_tint=true) {
	$arr = array();
	foreach ((array)$colors as $key => $color) {
		if (empty($color)) { continue; }
		$arr[$key] = array(
			'base' => r34ics_hex2rgba($color,1),
			'highlight' => r34ics_hex2rgba($color, $alpha, $highlight_tint),
		);
	}
	return $arr;
}


// Determine if white or black is a better text color for background color
function r34ics_color_text4bg($hex='', $trimhash=false) {
	$hex = (string)$hex; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$rgb = r34ics_hex2rgba($hex, null, false, true);
	// Calculate luminosity
	// Based on: https://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
	$luminosity = 0.2126 * pow($rgb[0]/255, 2.2) + // Red luminosity
		0.7152 * pow($rgb[1]/255, 2.2) + // Green luminosity
		0.0722 * pow($rgb[2]/255, 2.2); // Blue luminosity
	return ($luminosity > 0.5) ? 'black' : 'white';
}


// Clean up and convert a string for comparison (not for display)
function r34ics_comparison_string($str='') {
	return iconv('UTF-8', 'ASCII//TRANSLIT', strtolower(preg_replace('/[\s]+/','',($str ?: ''))));
}


// Create a cURL cookie path (if needed) and return it
function r34ics_curl_cookie_path($cookie_file='curl_cookie.txt') {
	global $wp_filesystem;
	$wp_upload_dir = wp_upload_dir();
	$cookie_dir = $wp_upload_dir['basedir'] . '/ics-calendar';
	if (!is_dir($cookie_dir)) {
		$wp_filesystem->mkdir($cookie_dir);
	}
	return $cookie_dir . '/' . $cookie_file;
}


// Smart formatted date strings to eliminate prior use of gmmktime() and the timezone-related issues it caused (v. 7.0.0)
function r34ics_date($format='', $dt_str='', $tz=null, $offset='') {
	global $wp_locale;
	$date = null;
	$format = (string)$format; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$dt_str = (string)$dt_str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$offset = (string)$offset; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	// Safely catch Unix timestamps
	if (strlen($dt_str) >= 10 && is_numeric($dt_str)) { $dt_str = '@' . $dt_str; }
	// Convert $tz to DateTimeZone object if applicable
	if (!empty($tz) && is_string($tz)) { $tz = new DateTimeZone($tz); }
	// Set default timezone if null
	if (empty($tz)) { global $R34ICS2; $tz = $R34ICS2->tz; }
	// Fix signs in offset
	$offset = str_replace('--', '+', str_replace('+-', '-', $offset));
	// Create new datetime from date string
	$dt = new DateTime(trim($dt_str . ' ' . $offset), $tz);
	// Now that we've made our adjustments, we'll just use wp_date() from here
	return wp_date($format, $dt->getTimestamp(), $tz);
}


// Set a display date format based on a passed-in format, or by adding day of week and removing year from site format; optionally wraps elements in tags
function r34ics_date_format($format='', $spans=false) {
	$format = !empty($format) ? wp_strip_all_tags($format) : 'l ' . trim(preg_replace('/((, )?Y|l(, )?)/', '', get_option('date_format')), ',/-');
	// Add spans; only works if the format doesn't already have some custom (escaped) characters
	if (!empty($spans) && strpos($format, "\\") === false) {
		$format = preg_replace('/(([DlFmMndjYy])[S]?[,\.]?)/', '\<\s\p\a\n \d\a\t\a\-\d\a\t\e\-\f\o\r\m\a\t\=\"\\\$2\"\>$1\<\/\s\p\a\n\>', $format);
	}
	return trim($format);
}


// Prepare a set of CSS classes for a day in the grid
function r34ics_day_classes($args) {
	$defaults = array(
		'date' => r34ics_date('Ymd'),
		'today' => r34ics_date('Ymd'),
		'count' => 0,
		'filler' => false,
		'flat' => true,
	);
	extract(array_merge($defaults, $args));
	$classes = array();
	if ($date < $today) {
		$classes[] = 'past';
	}
	elseif ($date == $today) {
		$classes[] = 'today';
	}
	else {
		$classes[] = 'future';
	}
	if ($count == 0) {
		$classes[] = 'empty';
	}
	elseif ($count == 1 && !empty($filler)) {
		$classes[] = 'available';
	}
	else {
		$classes[] = 'has_events';
	}
	// Append day number and day of week
	$classes[] = 	'd_' . r34ics_date('d', $date);
	$classes[] = 	'dow_' . r34ics_date('w', $date);

	if (!empty($flat)) { $classes = implode(' ', $classes); }
	// Allow external manipulation of the classes array
	$classes = apply_filters('r34ics_day_classes', $classes, $args);
	return $classes;
}


// Get count of events on a given day
function r34ics_day_events_count($day_events) {
	$day_events_count = 0;
	if (!empty($day_events['all-day'][0]['filler'])) {
		$day_events_count = 0;
	}
	else {
		foreach ((array)$day_events as $time => $events) {
			$day_events_count = $day_events_count + count((array)$events);
		}
	}
	return intval($day_events_count);
}


// Get array of the feed keys that have events on a given day
function r34ics_day_events_feed_keys($day_events, $delim=null) {
	$feed_keys = array();
	foreach ((array)$day_events as $time => $events) {
		foreach ((array)$events as $event) {
			$feed_keys[$event['feed_key']] = $event['feed_key'];
		}
	}
	if (!empty($delim)) { $feed_keys = implode($delim, $feed_keys); }
	return $feed_keys;
}


// Display deferred admin notices
function r34ics_deferred_admin_notices() {
	if (!is_admin() || wp_doing_ajax()) { return; }
	global $r34ics_deferred_admin_notices;
	if (empty($r34ics_deferred_admin_notices)) {
		$r34ics_deferred_admin_notices = get_option('r34ics_deferred_admin_notices', array());
	}
	if (!empty($r34ics_deferred_admin_notices)) {
		foreach ((array)$r34ics_deferred_admin_notices as $notice_key => $notice) {
			if (!empty($notice['dismissible']) && method_exists('PAnD', 'is_admin_notice_active')) {
				if (!PAnD::is_admin_notice_active($notice_key . '-' . $notice['dismissible'])) { continue; }
				echo '<div class="notice notice-' . esc_attr($notice['status']) . ' is-dismissible r34ics-admin-notice" data-dismissible="' . esc_attr($notice_key . '-' . $notice['dismissible']) . '"><div>' . wp_kses_post($notice['content'] ?: '') . '</div></div>';
			}
			else {
				echo '<div class="notice notice-' . esc_attr($notice['status']) . ' r34ics-admin-notice"><div>' . wp_kses_post($notice['content'] ?: '') . '</div></div>';
			}
		}
	}
	unset($r34ics_deferred_admin_notices);
	delete_option('r34ics_deferred_admin_notices');
}


// Check if a URL's domain is the same as the current site
function r34ics_domain_match($url='') {
	// Excessive sanitization to placate Plugin Check
	return (wp_parse_url($url, PHP_URL_HOST) == (isset($_SERVER['SERVER_NAME']) ? wp_kses_post(wp_unslash($_SERVER['SERVER_NAME']) ?: '') : ''));
}


// Check if a string is empty of text content
function r34ics_empty_content($str='') {
	$str = (string)$str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	return empty(trim(str_replace('&nbsp;', '', strip_tags($str, '<img><iframe><audio><video>'))));
}


// Generate CSS classes to apply to wrapper for an event
function r34ics_event_css_classes($event, $time, $args) {
	$classes = array('event', $time);
	if (!empty($event['multiday']['position'])) {
		$classes[] = 'multiday_' . $event['multiday']['position'];
	}
	// Deprecated
	elseif (!empty($event['multiday_position'])) {
		$classes[] = 'multiday_' . $event['multiday_position'];
	}
	// Allow external manipulation of the classes array
	$classes = apply_filters('r34ics_event_css_classes', $classes, $event, $time, $args);
	return implode(' ', $classes);
}


// Generate dynamic feed colors CSS
function r34ics_feed_colors_css($ics_data, $padding=false, $hover=false) {
		ob_start();
		if (!empty($ics_data['colors'])) {
			foreach ($ics_data['colors'] as $feed_key => $color) {
				// General styling for all views
				?>
				#<?php echo esc_attr($ics_data['guid']); ?> *[data-feed-color="<?php echo esc_attr($color['base']); ?>"]:not([type=checkbox]) {
					background: <?php echo esc_attr($color['highlight']); ?>;
					<?php
					if (!empty($padding)) {
						?>
						padding: 0.1em 0.5em;
						<?php
					}
					if (!empty($hover)) {
						?>
						border: 1px solid <?php echo esc_attr(r34ics_hex2rgba($color['base'],0.25)); ?>;
						z-index: 1;
						<?php
					}
					?>
					border-left: 4px solid <?php echo esc_attr($color['base']); ?>;
				}
				.ics-calendar#<?php echo esc_attr($ics_data['guid']); ?> .event[data-feed-color="<?php echo esc_attr($color['base']); ?>"] .descloc .descloc_feed_label:before {
					background-color: <?php echo esc_attr($color['base']); ?>;
				}
				<?php
				// Specific styling for basic view
				if ($ics_data['view'] == 'basic') {
					?>
					.ics-calendar#<?php echo esc_attr($ics_data['guid']); ?> *[data-feed-color="<?php echo esc_attr($color['base']); ?>"]:not(.ics-calendar-color-key-item) {
						background: transparent;
					}

					.ics-calendar:not(.nostyle)#<?php echo esc_attr($ics_data['guid']); ?> *[data-feed-color="<?php echo esc_attr($color['base']); ?>"]:not(.ics-calendar-color-key-item) {
						border: none;
						padding: 0;
					}

					.ics-calendar:not(.nostyle)#<?php echo esc_attr($ics_data['guid']); ?> *[data-feed-color="<?php echo esc_attr($color['base']); ?>"] .date {
						background: <?php echo esc_attr($color['highlight']); ?>;
						border-color: <?php echo esc_attr($color['base']); ?>;
					}
					<?php
				}
				// Specific styling for list views
				if ($ics_data['view'] == 'list' || !empty($padding)) {
					?>
					.ics-calendar#<?php echo esc_attr($ics_data['guid']); ?> dl.events dt.time[data-feed-color="<?php echo esc_attr($color['base']); ?>"] {
						padding-top: 0.5em;
						padding-bottom: 0;
					}
					.ics-calendar#<?php echo esc_attr($ics_data['guid']); ?> dl.events dt.time[data-feed-color="<?php echo esc_attr($color['base']); ?>"] + dd.event[data-feed-color="<?php echo esc_attr($color['base']); ?>"] {
						padding-top: 0;
						padding-bottom: 0.5em;
					}
					.ics-calendar#<?php echo esc_attr($ics_data['guid']); ?> dl.events dd.event[data-feed-color="<?php echo esc_attr($color['base']); ?>"]:first-child {
						padding-top: 0.5em;
						padding-bottom: 0.5em;
					}
					<?php
				}
				// Hover state effects
				if (!empty($hover)) {
					?>
					#<?php echo esc_attr($ics_data['guid']); ?> *[data-feed-color="<?php echo esc_attr($color['base']); ?>"]:not([type=checkbox]):hover {
						background: <?php echo esc_attr(r34ics_hex2rgba($color['base'],0.8333)); ?>;
						color: <?php echo esc_attr(r34ics_color_text4bg($color['base'])); ?>;
						height: auto !important;
						z-index: 2;
					}
					<?php
				}
			}
		}
		if (!empty($ics_data['tablebg'])) {
			?>
			.ics-calendar table { background-color: <?php echo esc_attr($ics_data['tablebg']); ?> !important; }
			<?php
		}
		// Allow external manipulation of the CSS output
		$feed_colors_css = apply_filters('r34ics_feed_colors_css', ob_get_clean(), $ics_data, $padding, $hover);
		// Output CSS
		if (!empty($feed_colors_css)) {
			echo '<style type="text/css">' . wp_kses_post(r34ics_minify_css($feed_colors_css) ?: '') . '</style>';
		}
}


/**
 * Replicate (some of) the effects of 'the_content' filter without actually calling that
 * filter because we might pull actual post content in along the way.
 * 
 * Note: It's perhaps a gray area whether or not 'the_content' should be used for this but
 * Elementor, for one, messes things up for us with this filter!
 * 
 * Also note: This skips some of the functions WP normally runs on 'the_content' because
 * we're assuming this is general-purpose text, not WP content.
 */
function r34ics_filter_the_content($str='') {
	$str = (string)$str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	return trim(wpautop(convert_chars(wptexturize($str))));
}


// Get first day of a week/month/year
function r34ics_first_day_of($interval, $dt_str='') {
	$dt_str = (string)$dt_str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$first_day = false;
	switch ($interval) {
		case 'year':
			$first_day = strtotime(r34ics_date('Y', $dt_str) . '0101');
			break;
		case 'week':
			$start_of_week = get_option('start_of_week', 0);
			$this_day = r34ics_date('w', $dt_str);
			$days_offset = $this_day - $start_of_week;
			if ($days_offset < 0) { $days_offset = $days_offset + 7; }
			$first_day = strtotime(r34ics_date('Ymd', $dt_str, null, '-' . intval($days_offset) . ' days'));
			break;
		case 'month':
		default:
			$first_day = strtotime(r34ics_date('Ym', $dt_str) . '01');
			break;
	}
	return $first_day;
}
// Deprecated alias
function r34ics_first_day_of_current($interval, $dt_str='') {
	$dt_str = (string)$dt_str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	return r34ics_first_day_of($interval, $dt_str);
}


// Get admin settings URL (with conditional check for ICS Calendar Pro)
function r34ics_get_admin_url($page='') {
	$page = in_array($page, array('settings', 'utilities')) ? $page : 'settings';
	if (class_exists('R34ICS2Pro')) {
		$url = admin_url('edit.php?post_type=r34icspro_calendar&page=ics-calendar-pro-' . $page);
	}
	else {
		$url = admin_url('admin.php?page=ics-calendar');
	}
	return $url;
}


// Get timezone for feed
function r34ics_get_feed_tz($ics_data, $feed_key) {
	$tzid = '';
	if (is_array($ics_data['tz'])) {
		$tzid = isset($ics_data['tz'][$feed_key]) ? trim($ics_data['tz'][$feed_key] ?? '') : trim($ics_data['tz'][0] ?? '');
	}
	elseif (!empty($ics_data['tz'])) {
		$tzid = $ics_data['tz'];
	}
	return r34ics_is_valid_tz($tzid) ? timezone_open($tzid) : wp_timezone();
}


// Return the $ics_data array for the given set of $args
// Useful for manipulating parsed calendar data without displaying the calendar
// @todo Refactor functionality from R34ICS2::display_calendar() method into its own standalone method
function r34ics_get_ics_data($args) {
	global $R34ICS2;
	if (is_object($R34ICS2) && method_exists($R34ICS2, 'display_calendar')) {
		// Set required values
		$args = array_merge(array(
			'arrayonly' => true,
			'limitdays' => ($args['limitdays'] ?? 365),
			'startdate' => (empty($args['startdate']) && empty($args['pastdays'])) ? wp_date('Ymd', current_time('timestamp')) : null,
			'view' => ($args['view'] ?? 'month'),
		), $args);
		$args = apply_filters('r34ics_display_calendar_args', $args, array());
		return $R34ICS2->display_calendar($args);
	}
	return false;
}


// Determine whether or not event has details to be displayed (in .descloc)
function r34ics_has_desc($args, $event) {
	global $R34ICS2;
	$has_desc = null;
	// Override all other factors and return false if using `maskinfo`
	// Removed because this prevents the hover box with start/end times appearing on multi-day events
	//if (!empty($args['maskinfo'])) {
	//	$has_desc = false;
	//}
	// Assess settings and presence of content to determine whether or not there is a description to display
	//else {
		$has_desc =
			// TRUE if ANY of the following conditions are met:
			// We want the description AND the event has a description, URL or attachment
			(!empty($args['eventdesc']) && (!empty($event['eventdesc']) || !empty($event['url']) || !empty($event['attach']))) ||
			// We want the location AND the event has a location
			(!empty($args['location']) && !empty($event['location'])) ||
			// We want the organizer AND the event has an organizer or contact
			(!empty($args['organizer']) && (!empty($event['organizer']) || !empty($event['contact']))) ||
			// We are NOT hiding recurrence details AND this is a recurring event
			(empty($args['hiderecurrence']) && !empty($event['rrule'])) ||
			// We want the individual event .ics download
			(!empty($args['eventdl'])) ||
			// This is a multi-day event AND we are either NOT using list view OR toggle is set to lightbox
			((!empty($event['multiday'])) && (!in_array($args['view'], (array)$R34ICS2->get_list_style_views()) || $args['toggle'] == 'lightbox')) ||
			// Feed label in description is turned on and we have a feed label
			(!empty($args['feedlabelindesc']) && !empty($args['feedlabel_array']) && isset($event['feed_key']) && !empty($args['feedlabel_array'][$event['feed_key']]));
	//}
	// Allow external filtering of the value
	$has_desc = apply_filters('r34ics_has_desc', $has_desc, $args, $event);
	// Return the value
	return $has_desc;
}


// Convert hex color to rgba
function r34ics_hex2rgba($color='', $alpha=1, $tint=false, $output_array=false) {
	$r = $g = $b = 0;
	$color = (string)$color; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$color = trim($color);
	// Convert named colors
	$color = r34ics_color_name2hex($color);
	// Strip #
	if (strpos($color, '#') === 0) { $color = str_replace('#', '', $color); }
	// rgb() or rgba() -- we ignore the alpha value in rgba()
	if (strpos($color, 'rgb') === 0) {
		$rgb = explode(',', preg_replace('/[^0-9,]/', '', $color));
		$r = intval($rgb[0]);
		$g = intval($rgb[1]);
		$b = intval($rgb[2]);
	}
	// 3-digit hex
	elseif (strlen($color) == 3) {
		$r = hexdec(substr($color,0,1));
		$g = hexdec(substr($color,1,1));
		$b = hexdec(substr($color,2,1));
	}
	// 6-digit hex
	elseif (strlen($color) == 6) {
		$r = hexdec(substr($color,0,2));
		$g = hexdec(substr($color,2,2));
		$b = hexdec(substr($color,4,2));
	}
	// Lighten tint
	if (!empty($tint)) {
		$r = $r + round((255 - $r) / 1.3333);
		$g = $g + round((255 - $g) / 1.3333);
		$b = $b + round((255 - $b) / 1.3333);
	}
	if (!empty($output_array)) { return array($r, $g, $b); }
	elseif ($alpha !== null) { return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . floatval($alpha) . ')'; }
	else { return 'rgb(' . $r . ',' . $g . ',' . $b . ')'; }
}


// Handle the hiderecurrence parameter -- returns an array of 1 or more frequency values or boolean to hide/show all
function r34ics_hiderecurrence_parse($hiderecurrence='') {
	$hiderecurrence = (string)$hiderecurrence; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	// Explode pipe- or space-delimited array
	$hiderecurrence = r34ics_space_pipe_explode(strtolower($hiderecurrence));
	if (is_array($hiderecurrence)) { return $hiderecurrence; }
	// Handle specific frequency values
	if (in_array(strtolower($hiderecurrence), array('yearly', 'monthly', 'weekly', 'daily'))) { return array(strtolower($hiderecurrence)); }
	// Return boolean value
	return r34ics_boolean_check($hiderecurrence);
}


// Get an hour format (e.g. for grid headings) based on the site's time format
function r34ics_hour_format($time_format='') {
	$time_format = (string)$time_format; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$hour_format = '';
	if (empty($time_format)) { $time_format = get_option('time_format'); }
	switch ($time_format) {
		case 'H:i':
			$hour_format = 'H:00';
			break;
		case 'h:i':
			$hour_format = 'h:00';
			break;
		case 'Hi':
			$hour_format = 'H00';
			break;
		case 'g:i a':
			$hour_format = 'g a';
			break;
		case 'g:i A':
		default:
			$hour_format = 'g A';
			break;
	}
	return $hour_format;
}


// Detect if we're in the block editor
function r34ics_is_block_editor() {
	if (function_exists('is_block_editor')) { return is_block_editor(); }
	if (function_exists('get_current_screen')) {
		$screen = get_current_screen();
		if ($screen && method_exists($screen, 'is_block_editor')) {
			return $screen->is_block_editor();
		}
	}
	return false;
}


// Check for an empty array (needed since we occasionally create empty nodes, but want to know if the array is ONLY empty nodes)
// Based on: https://sandbox.onlinephpfunctions.com/code/95d15b5972ec9a5096c11ba2f2042c04426220a1
function r34ics_is_empty_array(array $array) {
	$empty = true;
	array_walk_recursive($array, function($leaf) use (&$empty) {
		// Added empty($leaf) v.11.4.1.2 to prevent fatal errors with DateTimeZone elements when $leaf is NOT empty
		if ($leaf === [] || (is_scalar($leaf) && trim($leaf ?: '') === '') || empty($leaf)) { return; }
		$empty = false;
	});
	return $empty;
}


// Detect if a string contains HTML (not 100% reliable)
// Source: https://stackoverflow.com/a/33614682
function r34ics_is_html($str='') {
	$str = (string)$str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	return preg_match('/\/[a-z]*>/i', $str) != 0;
}


// Detect if we're running an instance of WordPress Playground (or any PHP.wasm implementation)
function r34ics_is_playground() {
	// Excessive sanitization to placate Plugin Check
	return (isset($_SERVER['SERVER_SOFTWARE']) && (wp_kses_post(wp_unslash($_SERVER['SERVER_SOFTWARE']) ?: '') == 'PHP.wasm'));
}


// Stupid exceptions!
// Source: https://stackoverflow.com/a/37231388
function r34ics_is_valid_tz($tzid='') {
	if (empty($tzid)) { return false; }
	foreach (timezone_abbreviations_list() as $zone) {
		foreach ($zone as $item) { if ($item['timezone_id'] == $tzid) { return true; } }
	}
	return false;
}


// Debugging tool: output a given string as a JavaScript alert
function r34ics_js_alert($str='') {
	$str = (string)$str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	if (current_user_can('manage_options')) {
		echo '<script>alert("' . esc_attr(str_replace('"', '&quot;', $str)) . '");</script>';
	}
}


// Get last day of current week/month/year
function r34ics_last_day_of($interval, $dt_str='') {
	$dt_str = (string)$dt_str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$last_day = false;
	switch ($interval) {
		case 'year':
			$last_day = strtotime(r34ics_date('Y', $dt_str) . '1231');
			break;
		case 'week':
			$end_of_week = get_option('start_of_week', 0) - 1;
			if ($end_of_week < 0) { $end_of_week = $end_of_week + 7; }
			$this_day = r34ics_date('w', $dt_str);
			$days_offset = $end_of_week - $this_day;
			if ($days_offset < 0) { $days_offset = $days_offset + 7; }
			$last_day = strtotime(r34ics_date('Ymd', $dt_str, null, '+' . intval($days_offset) . ' days'));
			break;
		case 'month':
		default:
			$last_day = strtotime(r34ics_date('n', $dt_str) . '/' . r34ics_date('t', $dt_str) . '/' . r34ics_date('Y', $dt_str));
			break;
	}
	return $last_day;
}
// Deprecated alias
function r34ics_last_day_of_current($interval, $dt_str=null) {
	return r34ics_last_day_of($interval, $dt_str);
}


// Build the toggle lightbox container
/**
 * Note: This function previously included a global variable to limit it to one instance
 * per page, however a user was running into an issue where that was preventing it from
 * appearing at all. We are now removing redundancies after the fact using jQuery.
 */
function r34ics_lightbox_container($echo=true) {
	$output = '<div class="r34ics_lightbox"><div class="r34ics_lightbox_inner"><div class="r34ics_lightbox_close" tabindex="0">&times;</div><div class="r34ics_lightbox_content"></div></div></div>';
	if (!empty($echo)) { echo wp_kses_post($output ?: ''); return true; }
	return $output;
}


// Kludge to fix rare cases where lines aren't properly folded
// See: https://icalendar.org/iCalendar-RFC-5545/3-1-content-lines.html
function r34ics_line_break_fix($ics_contents='') {
	$ics_contents = (string)$ics_contents; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$lines = explode("\r\n", $ics_contents);
	$replace_contents = false;
	$prev = null;
	foreach ((array)$lines as $key => $line) {
		preg_match('/([A-Z]+[:;])/', $line, $matches, PREG_OFFSET_CAPTURE);
		if (!isset($matches[1][1]) || $matches[1][1] !== 0) {
			$lines[$key] = ' ' . trim($line ?? '');
			// May need to also insert an extra space if the last word of
			// the previous line is not a URL... this isn't perfect!
			if (!empty($prev)) {
				$prev_arr = null;
				if (strpos(trim($prev), ' ') !== false) {
					$prev_arr = explode(' ', trim($prev));
				}
				elseif (strpos(trim($prev), "\\n") !== false) {
					$prev_arr = explode("\\n", trim($prev));
				}
				if (!empty($prev_arr)) {
					$prev_count = count($prev_arr);
					if	($prev_count > 1 && strpos(($prev_arr[$prev_count - 1] ?? ''), 'http') === false)
					{
						$lines[$key] = ' ' . $lines[$key];
					}
				}
			}
			$replace_contents = true;
		}
		$prev = $line;
	}
	if ($replace_contents) {
		$ics_contents = implode("\r\n", $lines);
	}
	return $ics_contents;
}


// Convert a location string into a Google Maps link (note: may not always yield a usable map)
function r34ics_location_map_link($location='',$geo='',$map_source="google") {
	$location = (string)$location; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$location = trim($location);
	$geo = (string)$geo; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$geo = trim($geo);
	// If the text contains HTML, return it as-is
	if (r34ics_is_html($location)) {
		return $location;
	}
	// If the text starts with a URL, just make it a clickable link and return it
	elseif (strpos($location, 'http') === 0) {
		return r34ics_maybe_make_clickable($location);
	}
	// Treat the entire string as a map search
	elseif (!empty($location) || !empty($geo)) {
		$geo_array = !empty($geo) ? explode(';',$geo) : null;
		switch (strtolower($map_source)) {
			case 'bing':
				if (is_array($geo_array) && count($geo_array) == 2) {
					return '<a href="https://www.bing.com/maps?cp=' . floatval($geo_array[0]) . '%7E' . floatval($geo_array[1]) . '&lvl=15.0">' . (!empty($location) ? $location : esc_html__('Map', 'ics-calendar')) . '</a>';
				}
				else {
					return '<a href="https://www.bing.com/maps?q=' . rawurlencode($location) . '">' . $location . '</a>';
				}
				break;
			case 'openstreetmap':
				if (is_array($geo_array) && count($geo_array) == 2) {
					return '<a href="https://www.openstreetmap.org/#map=15/' . floatval($geo_array[0]) . '/' . floatval($geo_array[1]) . '">' . (!empty($location) ? $location : esc_html__('Map', 'ics-calendar')) . '</a>';
				}
				else {
					return '<a href="https://www.openstreetmap.org/search?query=' . rawurlencode($location) . '">' . $location . '</a>';
				}
				break;
			case 'google':
			default:
				if (is_array($geo_array) && count($geo_array) == 2) {
					return '<a href="https://www.google.com/maps/@' . floatval($geo_array[0]) . ',' . floatval($geo_array[1]) . ',15z">' . (!empty($location) ? $location : esc_html__('Map', 'ics-calendar')) . '</a>';
				}
				else {
					return '<a href="https://maps.google.com/?q=' . rawurlencode($location) . '">' . $location . '</a>';
				}
				break;
		}
	}
	return '';
}


// Enfold a text string if it's longer than 75 characters, minus the label length
function r34ics_maybe_enfold($str='', $len=0) {
	if (empty($str)) { return ''; }
	// Convert line breaks to literal \n text string (must come first!)
	$str = str_replace("\n", "\\n", $str);
	// If string is too short to enfold, return it unaltered
	if (strlen($str) <= (75 - $len)) { return $str; }
	// Filler to prepend to first line, representing the label length (minus 1)
	$rpt = str_repeat(';', ($len - 1));
	// Split text into array of 74-character substrings
	$lines = str_split($rpt . $str, 74);
	// Trim the filler on the first line and prepend a space to all other lines
	foreach ((array)$lines as $key => $line) {
		if ($key == 0) { $lines[$key] = ltrim(($line ?? ''), ';'); }
		else { $lines[$key] = ' ' . $line; }
	}
	// Collapse the array back into a single string
	$str = implode("\r\n", $lines);
	// Return enfolded string
	return $str;
}


// Apply make_clickable() function to text string, and also deal with common quirks of iCalendar description data
function r34ics_maybe_make_clickable($str='') {
	$str = (string)$str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice

	// Check if the string has any HTML (to decide whether or not to run nl2br() on the end result)
	$has_html = r34ics_is_html($str);

	// Convert HTML entities
	$str = html_entity_decode($str);
	
	// Microsoft Teams / Office 365
	// Get rid of the long line of underscores
	$str = trim(str_replace('________________________________________________________________________________', '', $str));
	// Assign URLs inside angle brackets to the text that immediately precedes them
	if (strpos($str, '<http') !== false) {
		$str = preg_replace('/(([\s]*)([^<\v\|]+?)<([^>]+?)>)/', '\2<a href="\4">\3</a>', $str);
	}
	// Handle URLs occuring on their own inside square brackets -- not caught by make_clickable()
	if (strpos($str, '[http') !== false) {
		$str = preg_replace('/\[(http[^\]]+?)\]/', '<a href="\1">\1</a>', $str);
	}
	
	// Add <br /> tags if appropriate (must come last to avoid interfering with above logic)
	if (!$has_html) { $str = nl2br($str); }

	// Run standard WP make_clickable() function on what's left
	return make_clickable($str);
}


// Get system memory limit in MB
function r34ics_memory_limit_mb() {
	$memory_limit = ini_get('memory_limit');
	if (strpos($memory_limit, 'G') !== false) {
		return intval($memory_limit) * 1024;
	}
	return intval($memory_limit);
}


// Build a select menu for memory limit
function r34ics_memory_limit_select($field_name, $field_id=null, $current=null) {
	if (empty($field_id)) { $field_id = 'r34ics_' . $field_name; }
	$system_memory_limit = r34ics_memory_limit_mb();
	/* Standardize minimum up to next multiple of 128 for a consistent set of options */
	$min_value = $system_memory_limit + ($system_memory_limit % 128);
	$max_value = 2048;
	if ($max_value <= $min_value) { $values = array($system_memory_limit); }
	else { $values = range($min_value, $max_value, 128); }
	$html = '<select name="' . esc_attr($field_name) . '" id="' . esc_attr($field_id) . '">';
	foreach ((array)$values as $value) {
		$display = ($value < 1024) ? intval($value) . ' MB' : round($value / 1024, 3) . ' GB';
		$html .= '<option value="' . intval($value) . '"';
		if ($value == intval($current)) { $html .= ' selected="selected"'; }
		$html .= '>' . wp_kses_post($display ?: '') . '</option>';
	}
	$html .= '</select>';
	return $html;
}


// Minify CSS
// Based on: http://manas.tungare.name/software/css-compression-in-php/
function r34ics_minify_css($css) {
	// Remove comments
	$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', ($css ?? ''));
	// Remove space after colons
	$css = str_replace(': ', ':', $css);
	// Remove space around braces
	$css = str_replace(array(' {','{ '), '{', $css);
	$css = str_replace(array(' }','} '), '}', $css);
	// Remove extra whitespace
	// Note: This isn't as compact as it *could* be, but running output through wp_kses_post() causes issues (changed v. 11.5.1.1)
	$css = preg_replace('/[\s]+/', ' ', $css);
	return $css;
}


// Determine if a given month (Ym format) is in the parsed range for the current ICS data set
function r34ics_month_in_range($ym='', $ics_data=array()) {
	if (empty(intval($ym)) || strlen($ym) != 6 || !isset($ics_data['earliest']) || !isset($ics_data['latest'])) { return null; }
	$ym = intval($ym);
	$earliest_month = intval(substr($ics_data['earliest'],0,6));
	$latest_month = intval(substr($ics_data['latest'],0,6));
	return $ym >= $earliest_month && $ym <= $latest_month;
}


// Create a multi-day date label
function r34ics_multiday_date_label($date_format, $event, $args) {
	/*
	Version 9.6.5.1 revises the change from version 9.6.3.2:
	Restructured into MM/DD/YYYY format because, for an unknown reason,
	both wp_date() and r34ics_date() are shifting these back by 1 day if
	in YYYYMMDD format.
	*/
	$md_start = !empty($event['multiday']['start_date'])
		? r34ics_date($date_format,
			substr($event['multiday']['start_date'],4,2) . '/' .
			substr($event['multiday']['start_date'],6,2) . '/' .
			substr($event['multiday']['start_date'],0,4),
			$event['tz_start']
		)
		: '';
	$md_end = !empty($event['multiday']['end_date'])
		? r34ics_date($date_format,
			substr($event['multiday']['end_date'],4,2) . '/' .
			substr($event['multiday']['end_date'],6,2) . '/' .
			substr($event['multiday']['end_date'],0,4),
			$event['tz_end']
		)
		: '';
	if (!empty($event['multiday']['start_time'])) {
		$md_start .= ' <small class="time-inline">' . r34ics_time_format($event['multiday']['start_time'], $args['timeformat'], $event['tz_start'], $event['multiday']['start_date']) . '</small>';
	}
	if (!empty($event['multiday']['end_time'])) {
		$md_end .= ' <small class="time-inline">' . r34ics_time_format($event['multiday']['end_time'], $args['timeformat'], $event['tz_end'], $event['multiday']['end_date']) . '</small>';
	}
	return '<span class="multiday-start">' . $md_start . '</span><span class="multiday-sep"> &#8211; </span><span class="multiday-end">' . $md_end . '</span>';
}


// Format organizer array data
function r34ics_organizer_format($organizer='') {
	$output = '';
	if (is_array($organizer)) {
		if (count((array)$organizer) == 2 && isset($organizer[0]['CN'])) {
			$output .= '<div class="organizer_email"><a href="' . esc_url($organizer[1]) . '" rel="noopener noreferrer nofollow">' . rawurldecode($organizer[0]['CN']) . '</a></div>';
		}
		elseif (!empty($organizer[1]) && is_scalar($organizer[1])) {
			$output .= '<div>' . $organizer[1] . '</div>';
		}
	}
	elseif (!empty($organizer)) {
		$output .= '<div>' . $organizer . '</div>';
	}
	return $output;
}


// Purge all of this plugin's transient calendar data (does not affect any of the plugin's other transients)
function r34ics_purge_calendar_transients() {

	// First we delete the saved feed URL list
	delete_option('r34ics_feed_urls');
	
	// Hook in external actions (e.g. for purging ICS Calendar Pro data)
	do_action('r34ics_purge_calendar_transients');
	
	// Now we purge the plugin's transients and return the results of the query
	// We do this with a custom SQL query because it's a lot simpler!
	global $wpdb;
	return $wpdb->query($wpdb->prepare("DELETE FROM `" . $wpdb->options . "` WHERE `option_name` LIKE %s AND `option_name` LIKE %s", '%_transient_%', '%R34ICS2%'));
}


// Strip embedded images from descriptions (they can blow up the array size and probably won't render properly anyway)
function r34ics_raw_feed_strip_embedded_images($ics_contents, $range_start=null, $range_end=null, $args=array()) {
	$ics_contents = preg_replace('/(<img([^>]+?)src="data:image\/([^>]+?)>)/', '', $ics_contents);
	return $ics_contents;
}


// Strip invalid "DTEND:None" lines from feed (issue with feeds from easyVerein)
function r34ics_raw_feed_strip_dtend_none($ics_contents, $range_start=null, $range_end=null, $args=array()) {
	if (stripos($ics_contents, 'DTEND:None') !== false) {
		$ics_contents = str_ireplace('DTEND:None', '', $ics_contents);
	}
	return $ics_contents;
}


// Convert a recurrence rule into a human-readable expression (with i18n support)
function r34ics_recurrence_description($rrule=null, $hiderecurrence=null, $html=true) {
	$output = '';
	// Hide recurrence altogether?
	if (!is_array($hiderecurrence) && !empty($hiderecurrence)) { return null; }
	if (!empty($rrule)) {
		// Explode recurrence rules
		$recur = r34ics_recurrence_explode($rrule);
		// Hide this recurrence frequency?
		if (is_array($hiderecurrence) && in_array(strtolower($recur['FREQ']), $hiderecurrence)) { return null; }
		// General recurrence description
		$output = esc_html__('Recurring event', 'ics-calendar');
		// Get more specific if we can
		if (!empty($recur['FREQ'])) {
			switch ($recur['FREQ']) {
				case 'YEARLY':
					/* translators: 1. Dynamic value */
					$output = (isset($recur['INTERVAL']) && $recur['INTERVAL'] > 1) ? sprintf(esc_html__('Recurs every %1$s years', 'ics-calendar'), $recur['INTERVAL']) : esc_html__('Recurs yearly', 'ics-calendar');
					break;
				case 'MONTHLY':
					/* translators: 1. Dynamic value */
					$output = (isset($recur['INTERVAL']) && $recur['INTERVAL'] > 1) ? sprintf(esc_html__('Recurs every %1$s months', 'ics-calendar'), $recur['INTERVAL']) : esc_html__('Recurs monthly', 'ics-calendar');
					break;
				case 'WEEKLY':
					/* translators: 1. Dynamic value */
					$output = (isset($recur['INTERVAL']) && $recur['INTERVAL'] > 1) ? sprintf(esc_html__('Recurs every %1$s weeks', 'ics-calendar'), $recur['INTERVAL']) : esc_html__('Recurs weekly', 'ics-calendar');
					break;
				case 'DAILY':
					/* translators: 1. Dynamic value */
					$output = (isset($recur['INTERVAL']) && $recur['INTERVAL'] > 1) ? sprintf(esc_html__('Recurs every %1$s days', 'ics-calendar'), $recur['INTERVAL']) : esc_html__('Recurs daily', 'ics-calendar');
					break;
				default: break;
			}
		}
	}
	// Add HTML wrapper
	if (!empty($output) && !empty($html)) {
		$output = '<div class="recurrence">' . $output . '</div>';
	}
	return $output;
}


// Explode a recurrence rule into an array
function r34ics_recurrence_explode($rrule='') {
	$rrule = (string)$rrule; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$output = null;
	if ($parts = explode(';', $rrule)) {
		$output = array();
		foreach ((array)$parts as $part) {
			$arr = explode('=', $part);
			$output[$arr[0]] = $arr[1];
		}
	}
	return $output;
}


// Output target attribute based on sametab setting
function r34ics_sametab_target($sametab, $url) {
	$output = '';
	$target_string = ' target="_blank" rel="noopener noreferrer nofollow"';
	switch ($sametab) {
		case 'all':
			// Do nothing
			break;
		case 'none':
			$output = $target_string;
			break;
		case 'local':
		default:
			$output = empty($sametab) && !r34ics_domain_match($url) ? $target_string : '';
			break;
	}
	return $output;
}


// Sanitize (and sort) a list of hostnames
function r34ics_sanitize_hosts($hosts=null) {
	if (empty($hosts)) { return null; }
	if (!is_array($hosts)) { $hosts = explode("\n", $hosts); }
	$allowed_hosts = '';
	if (!empty($hosts)) {
		$allowed_hosts = array_filter(filter_var_array($hosts, FILTER_SANITIZE_URL));
		sort($allowed_hosts);
	}
	return $allowed_hosts;
}


// Scrape URL(s) from a string
// Note: Yes, there's this... https://daringfireball.net/2010/07/improved_regex_for_matching_urls
function r34ics_scrape_url_from_string($str, $first_only=true, $only_match_one=false) {
	preg_match_all('/https?:\/\/[^\s"\'<>]+/', $str, $matches);
	if (isset($matches[0]) && is_array($matches[0]) && count($matches[0]) > 0) {
		$urls = array_values(array_unique($matches[0]));
		foreach ((array)$urls as $key => $url) {
			$urls[$key] = esc_url(filter_var($url, FILTER_SANITIZE_URL));
		}
		if ($first_only || ($only_match_one && count($urls) == 1 && !empty($urls[0]))) { return $urls[0]; }
		elseif (!$only_match_one) { return $urls; }
	}
	return '';
}


// Scrub multiple instances of the same UID on the same day/time slot from the $ics_data array
// Note: This may be removing the *wrong* instance; we don't yet have a reliable way to determine that
function r34ics_scrub_duplicate_uids($ics_data, $args) {
	$elevated_level = ($args['fixredundantuids'] >= 2) ? intval($args['fixredundantuids']) : false;
	foreach ((array)$ics_data['events'] as $year => $months) {
		foreach ((array)$months as $month => $days) {
			foreach ((array)$days as $day => $times) {
				foreach ((array)$times as $time => $events) {	
					$uids = array();
					// Are we at an elevated level?
					if ($elevated_level) { $labels = array(); }
					foreach ((array)$events as $key => $event) {
						// Has this UID already appeared in this day/time slot? If so, remove it...
						if (in_array($event['uid'], $uids)) {
							unset($ics_data['events'][$year][$month][$day][$time][$key]);
						}
						// ...and also remove it if we're at an elevated level and there's a title match at the same time...
						elseif ($elevated_level && in_array($event['label'], $labels)) {
							unset($ics_data['events'][$year][$month][$day][$time][$key]);
						}
						// ...and if not, add it to the list of UIDs
						else {
							$uids[] = $event['uid'];
							if ($elevated_level) { $labels[] = $event['label']; }
						}
					}
				}
			}
		}
	}
	return $ics_data;
}


// Return allowed fields array for wp_kses() with functions like r34ics_memory_limit_select()
function r34ics_select_allowed() {
	return array_merge(wp_kses_allowed_html('post'),
		array(
			'select' => array(
				'class' => array(),
				'id' => array(),
				'name' => array(),
				'value' => array(),
				'type' => array(),
			),
			'option' => array(
				'selected' => array(),
				'value' => array(),
			),
		)
	);
}


/**
 * If the shortcode is in a Block Editor (Gutenberg) "paragraph" block, or has been pasted
 * into the WYSIWYG editor from a program that automatically makes URLs clickable, the URL
 * might be entered as a clickable link.
 * 
 * In that case, an 'href' node will most likely exist in the $atts array (but it will only
 * include the *last* URL, if the shortcode contains more than one), so we'll salvage what we
 * can and throw an error.
 */
function r34ics_shortcode_url_fix($atts) {
	if (!empty($atts['url'])) { return wp_strip_all_tags($atts['url']); }
	elseif (!empty($atts['href'])) {
		// Trigger an error so hopefully users will clean up their shortcodes
		/* translators: 1: Plugin name (do not translate) */
		trigger_error(sprintf(esc_html__('The "url" property in your %1$s shortcode appears to contain HTML tags; most likely your URL has been turned into a clickable link. Your calendar may not display properly as a result. Please remove the clickable link in the shortcode.', 'ics-calendar'), 'ICS Calendar'), E_USER_WARNING);
		return $atts['href'];
	}
	return false;
}


// Break a string into an array using any combination of spaces and/or pipes as the delimiter
// Returns the original string if spaces or pipes are not present (i.e. exploded array contains only one node)
function r34ics_space_pipe_explode($str='') {
	$str = (string)$str; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$exploded = preg_split('/[\s\|]+/', $str);
	if (count($exploded) == 1) { return $str; }
	return $exploded;
}


// Prepare a system report for diagnostic support
function r34ics_system_report($echo=true) {
	// Get info about current theme
	$theme = wp_get_theme();

	// Get list of active plugins and their versions
	$plugins = get_plugins();
	$active_plugins = get_option('active_plugins');
	$plugin_list = array();
	foreach ((array)$active_plugins as $plugin) {
		if (isset($plugins[$plugin]['Name']) && isset($plugins[$plugin]['Version'])) {
			$plugin_list[] = $plugins[$plugin]['Name'] . ' ' . $plugins[$plugin]['Version'];
		}
		else {
			$plugin_list[] = $plugin;
		}
	}
	sort($plugin_list);
		
	// Gather all report data
	global $wpdb;
	// Note: Some servers may have the php_uname() function disabled for security reasons
	$server_os = function_exists('php_uname')
		? php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m')
		: (defined('PHP_OS') ? PHP_OS : 'Unknown OS');
	$server_fields = array(
		'SERVER_SOFTWARE' => '',
		'SERVER_PROTOCOL' => '',
		'LOCAL_ADDR' => '',
		'SERVER_ADDR' => '',
		'SERVER_PORT' => '',
	);
	foreach ((array)$server_fields as $key => $value) {
		if (!empty($_SERVER[$key])) { $server_fields[$key] = wp_strip_all_tags(wp_unslash($_SERVER[$key])); }
	}
	$server_fields = array_filter($server_fields);
	$report = array(
		'Site' => get_bloginfo('name') . ' / ' . get_bloginfo('wpurl'),
		'WordPress' => get_bloginfo('version') . (function_exists('get_sites') ? ' Multisite (' . count(get_sites()) . ')' : ''),
		'Locale' => get_bloginfo('language') . ' ' . get_option('timezone_string'),
		'Active Theme' => $theme->name . ' ' . $theme->version . ' (' . pathinfo($theme->template_dir, PATHINFO_BASENAME) . ')',
		'Active Plugins' => $plugin_list,
		'Server' => array(
			$server_os,
			implode(' ', (array)$server_fields),
			'MySQL ' . $wpdb->dbh->server_version . ' ' . $wpdb->dbh->host_info,
			'PHP ' . (defined('PHP_VERSION') ? PHP_VERSION : ''),
		),
	);
	
	// Add PHP settings
	$php_settings = array(
		'allow_url_fopen',
		'date.timezone',
		'default_charset',
		'display_errors',
		'max_execution_time',
		'memory_limit',
		'post_max_size',
		'upload_max_filesize',
	);
	foreach ((array)$php_settings as $setting_name) {
		$report['PHP Settings'][$setting_name] = ini_get($setting_name);
	}
		
	// Note: cURL settings were removed here in version 11.0.0 switch to wp_remote_get()
	
	// Add relevant WordPress core settings
	$wp_settings = array(
		'blog_charset',
		'date_format',
		'gmt_offset',
		'start_of_week',
		'time_format',
	);
	foreach ((array)$wp_settings as $setting_name) {
		$report['WordPress Settings'][$setting_name] = get_option($setting_name);
	}

	// Add ICS Calendar saved settings
	$settings_fields = array(
		'r34ics_ajax_by_default',
		'r34ics_ajax_bypass_nonce',
		'r34ics_allowed_hosts',
		'r34ics_display_add_calendar_button_false',
		'r34ics_display_calendar_memory_limit',
		'r34ics_feed_urls',
		'r34ics_previous_version',
		'r34ics_transients_expiration',
		'r34ics_url_get_contents_legacy_method',
		'r34ics_use_new_defaults_10_6',
		'r34ics_version',
	);
	foreach ((array)$settings_fields as $field) {
		$report['Plugin Settings'][$field] = get_option($field);
	}
	
	// Add external report details
	$report = apply_filters('r34ics_system_report_array', $report);

	// Output report data
	if (!empty($echo)) {
		foreach ((array)$report as $key => $value) {
			if (!is_int($key)) { echo wp_kses_post($key ?: '') . ': '; }
			if (is_array($value)) {
				echo "\n";
				foreach ((array)$value as $key2 => $value2) {
					echo '&nbsp;&nbsp;' . (!is_int($key2) ? wp_kses_post($key2 ?: '') . ': ' : '');
					if (is_array($value2)) {
						echo "\n";
						foreach ((array)$value2 as $key3 => $value3) {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;' . (!is_int($key3) ? wp_kses_post($key3 ?: '') . ': ' : '');
							if (is_array($value3)) {
								print_r($value3);
							}
							else {
								echo wp_kses_post($value3 ?: '') . "\n";
							}
						}
					}
					else {
						echo wp_kses_post($value2 ?: '') . "\n";
					}
				}
			}
			else {
				echo wp_kses_post($value ?: '') . "\n";
			}
		}
		return true;
	}
	else {
		return $report;
	}
}


/**
 * Simple time formatter that will take a basic time string and convert it to a time in the desired format
 * This is because we want to be able to manipulate a time string *without* involving timezone adjustments!
 *
 * Quick reference:
 * g	12-hour without leading zero
 * G	24-hour without leading zero
 * h	12-hour with leading zero
 * H	24-hour with leading zero
 * i	minutes with leading zero
 * s	seconds with leading zero
 * a	lowercase am/pm
 * A	uppercase AM/PM
 * \	precedes literal character (only needed if also a formatting character)
 * 
 */
function r34ics_time_format($time_string='', $time_format='', $tz='', $date=null) {
	// Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	$time_string = (string)$time_string;
	$time_format = (string)$time_format;

	$output = '';

	// Get time format from WP settings if not passed in
	if (empty($time_format)) { $time_format = get_option('time_format'); }

	// Strip unsupported format elements from string (a temporary workaround until these can be supported)
	$time_format = trim(preg_replace('/[BsueOPZ]/', '', $time_format));
	$time_format_minus_t = trim(preg_replace('/[T]/', '', $time_format));

	// Get digits from time string
	$time_digits = preg_replace('/[^0-9]+/', '', $time_string);

	// Get am/pm from time string
	$time_ampm = preg_replace('/[^amp]+/', '', strtolower($time_string));
	if ($time_ampm != 'am' && $time_ampm != 'pm') { $time_ampm = null; }

	// Prepend zero to digits if length is odd
	if (strlen($time_digits) % 2 == 1) { $time_digits = '0' . $time_digits; }

	// Get hour, minutes and seconds from time digits
	$time_h = substr($time_digits, 0, 2);
	$time_m = substr($time_digits, 2, 2);
	$time_s = strlen($time_digits) == 6 ? substr($time_digits, 4, 2) : null;

	// Convert hour to correct 24-hour value if needed
	if ($time_ampm == 'pm') { $time_h = (int)$time_h + 12; }
	if ($time_ampm == 'am' && $time_h == '12') { $time_h = '00'; }

	// Determine am/pm if not passed in
	if (empty($time_ampm)) { $time_ampm = (int)$time_h >= 12 ? 'pm' : 'am'; }

	// Get 12-hour version of hour
	$time_h12 = (int)$time_h % 12;
	if ($time_h12 == 0) { $time_h12 = 12; }
	if ($time_h12 < 10) { $time_h12 = '0' . (string)$time_h12; }

	// Convert am/pm abbreviations for Greek (this is simpler than putting it in the i18n files)
	if (get_locale() == 'el') { $time_ampm = ($time_ampm == 'am') ? 'πμ' : 'μμ'; }

	// Format output
	switch ($time_format_minus_t) {

		// 12-hour formats without seconds
		case 'g:i a': $output = intval($time_h12) . ':' . $time_m . '&nbsp;' . $time_ampm; break;
		case 'g:ia': $output = intval($time_h12) . ':' . $time_m . $time_ampm; break;
		case 'g:i A': $output = intval($time_h12) . ':' . $time_m . '&nbsp;' . strtoupper($time_ampm); break;
		case 'g:iA': $output = intval($time_h12) . ':' . $time_m . strtoupper($time_ampm); break;
		case 'h:i a': $output = $time_h12 . ':' . $time_m . '&nbsp;' . $time_ampm; break;
		case 'h:ia': $output = $time_h12 . ':' . $time_m . $time_ampm; break;
		case 'h:i A': $output = $time_h12 . ':' . $time_m . '&nbsp;' . strtoupper($time_ampm); break;
		case 'h:iA': $output = $time_h12 . ':' . $time_m . strtoupper($time_ampm); break;

		// 24-hour formats without seconds
		case 'G:i': $output = intval($time_h) . ':' . $time_m; break;
		case 'G.i': $output = intval($time_h) . '.' . $time_m; break;
		case 'Gi': $output = intval($time_h) . $time_m; break;

		// case 'H:i': is the default, below
		case 'Hi': $output = $time_h . $time_m; break;

		// 24-hour formats without seconds, using h and m or min
		case 'G \h i \m\i\n': $output = intval($time_h) . '&nbsp;h&nbsp;' . $time_m . '&nbsp;min'; break;
		case 'G\h i\m\i\n': $output = intval($time_h) . 'h&nbsp;' . $time_m . 'min'; break;
		case 'G\hi': $output = intval($time_h) . 'h' . $time_m; break;
		case 'G\hi\m\i\n': $output = intval($time_h) . 'h' . $time_m . 'min'; break;
		case 'G \h i \m': $output = intval($time_h) . '&nbsp;h&nbsp;' . $time_m . '&nbsp;m'; break;
		case 'G\h i\m': $output = intval($time_h) . 'h&nbsp;' . $time_m . 'm'; break;
		case 'G\hi\m': $output = intval($time_h) . 'h' . $time_m . 'm'; break;
		case 'H \h i \m\i\n': $output = $time_h . '&nbsp;h&nbsp;' . $time_m . '&nbsp;min'; break;
		case 'H\h i\m\i\n': $output = $time_h . 'h&nbsp;' . $time_m . 'min'; break;
		case 'H\hi\m\i\n': $output = $time_h . 'h' . $time_m . 'min'; break;
		case 'H \h i \m': $output = $time_h . '&nbsp;h&nbsp;' . $time_m . '&nbsp;m'; break;
		case 'H\h i\m': $output = $time_h . 'h&nbsp;' . $time_m . 'm'; break;
		case 'H\hi\m': $output = $time_h . 'h' . $time_m . 'm'; break;

		// 12-hour formats with seconds
		case 'g:i:s a': $output = intval($time_h12) . ':' . $time_m . ':' . $time_s . '&nbsp;' . $time_ampm; break;
		case 'g:i:sa': $output = intval($time_h12) . ':' . $time_m . ':' . $time_s . $time_ampm; break;
		case 'g:i:s A': $output = intval($time_h12) . ':' . $time_m . ':' . $time_s . '&nbsp;' . strtoupper($time_ampm); break;
		case 'g:i:sA': $output = intval($time_h12) . ':' . $time_m . ':' . $time_s . strtoupper($time_ampm); break;
		case 'h:i:s a': $output = $time_h12 . ':' . $time_m . ':' . $time_s . '&nbsp;' . $time_ampm; break;
		case 'h:i:sa': $output = $time_h12 . ':' . $time_m . ':' . $time_s . $time_ampm; break;
		case 'h:i:s A': $output = $time_h12 . ':' . $time_m . ':' . $time_s . '&nbsp;' . strtoupper($time_ampm); break;
		case 'h:i:sA': $output = $time_h12 . ':' . $time_m . ':' . $time_s . strtoupper($time_ampm); break;

		// 24-hour formats with seconds
		case 'G:i:s': $output = intval($time_h) . ':' . $time_m . ':' . $time_s; break;
		case 'H:i:s': $output = $time_h . ':' . $time_m . ':' . $time_s; break;
		case 'His': $output = $time_h . $time_m . $time_s; break;

		// Hour-only formats used for grid labels
		case 'H:00': $output = $time_h . ':00'; break;
		case 'h:00': $output = $time_h12 . ':00'; break;
		case 'H00': $output = $time_h . '00'; break;
		case 'g a': $output = intval($time_h12) . ' ' . $time_ampm; break;
		case 'g A': $output = intval($time_h12) . ' ' . strtoupper($time_ampm); break;

		// Default
		case 'H:i':
		default: $output = $time_h . ':' . $time_m; break;

	}

	// Append T (timezone) if applicable
	// @todo Insert in the prescribed spot rather than just appending to end!
	if (!empty($tz) && $time_format_minus_t != $time_format) {
		if (is_string($tz)) {
			$tz = (r34ics_is_valid_tz($tz) ? timezone_open($tz) : wp_timezone());
		}
		$output .= ' ' . wp_date('T', (!empty($date) ? r34ics_date('U', $date) : null), $tz);
	}

	// Return output
	return $output;
}


// Generate a unique ID
/**
 * Note: This is not a true GUID/UUID; but we don't need anything cryptographically
 * secure, just a reasonably reliably unique string on the page. This produces a much
 * shorter string than the old method (and possibly generates faster), which is useful
 * in cases where we insert a LOT of these in the HTML output.
 */
function r34ics_uid() { return uniqid('r') . dechex(wp_rand(16,255)); }
// Deprecated; use r34ics_uid() instead
function r34ics_guid($deprecated1=true, $deprecated2=true) { return r34ics_uid(); }


// Return the URL associated with a uniqid value
function r34ics_uniqid_url($uniqid='') {
	if (empty($uniqid)) { return false; }
	$r34ics_feed_urls = get_option('r34ics_feed_urls');
	return sanitize_url($r34ics_feed_urls[$uniqid]) ?? false;
}


// This function has been replaced with the protected method R34ICS2::_url_get_contents()
// There is no graceful deprecation, as the function had no legitimate uses outside of this plugin
function r34ics_url_get_contents($deprecated_1=null, $deprecated_2=null, $deprecated_3=null, $deprecated_4=null, $deprecated_5=null) {
	trigger_error(esc_html__('The r34ics_url_get_contents() function is no longer supported.', 'ics-calendar'), E_USER_DEPRECATED);
	return false;
}


// Determine if it will be possible to retrieve a remote URL
function r34ics_url_open_allowed() {
	return (defined('CURLVERSION_NOW') || ini_get('allow_url_fopen'));
}


// Return a feed URL's uniqid() value
function r34ics_url_uniqid($url='') {
	$url = (string)$url; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	if (empty($url)) { return false; }
	$match = null;
	// We convert webcal:// URLs to https:// before saving them to r34ics_feed_urls, so we need to do it here too
	if (strpos($url,'webcal://') === 0) { $url = str_replace('webcal://','https://',$url); }
	// Convert ampersand entities (&amp;) to plain ampersands
	if (strpos($url,'&amp;') !== false) { $url = str_replace('&amp;','&',$url); }
	// If the option exists, we find the matching key
	if ($r34ics_feed_urls = get_option('r34ics_feed_urls')) {
		$match = array_search($url, $r34ics_feed_urls);
	}
	// If the matching key does not exist, we create it
	return !empty($match) ? $match : r34ics_url_uniqid_update($url, false);
}


// Add a new URL to r34ics_feed_urls -- $check prevents a recursive loop with r34ics_url_uniqid()
function r34ics_url_uniqid_update($url, $check=true) {
	if (empty($check) || !($uniqid = r34ics_url_uniqid($url))) {
		// Convert ampersand entities (&amp;) to plain ampersands
		if (strpos($url,'&amp;') !== false) { $url = sanitize_url(str_replace('&amp;','&',$url)); }
		$uniqid = uniqid('r34ics-url-', true);
		$r34ics_feed_urls = array_filter(array_merge((array)get_option('r34ics_feed_urls'), array($uniqid => $url)));
		update_option('r34ics_feed_urls', $r34ics_feed_urls);
	}
	return $uniqid;
}


// Convert a pipe- or space-delimited list of URLs or uniqids to the other format
function r34ics_url_uniqid_array_convert($items='') {
	$items = (array)r34ics_space_pipe_explode($items);
	foreach ((array)$items as $key => $item) {
		if (strpos($item,'http') === 0 || strpos($item,'webcal://') === 0) {
			$items[$key] = r34ics_url_uniqid($item);
		}
		else {
			$items[$key] = r34ics_uniqid_url($item);
		}
	}
	return implode('|', $items);
}


// Recursively filter an array with a callback function
// Note: Converts nested objects to arrays!
// Based on: https://gist.github.com/benjamw/1690140?permalink_comment_id=3172968#gistcomment-3172968
function _r34ics_array_filter_recursive($array, $callback=null) {
	foreach ($array as $key => $value) {
		if (is_object($value)) { $value = (array)$value; }
		if (is_array($value)) {
			$array[$key] = call_user_func_array(__FUNCTION__, array($value, $callback));
		}
		elseif (!empty($callback)) {
			$array[$key] = $callback($value);
		}
	}
	return $array;
}


// Print an array with preformatted HTML -- for debugging only
function _r34ics_debug($arr) {
	if (!current_user_can('manage_options')) { return false; }
	global $r34ics_debug_output;
	// Append general settings
	$settings_fields = array(
		'r34ics_ajax_by_default',
		'r34ics_ajax_bypass_nonce',
		'r34ics_allowed_hosts',
		'r34ics_display_add_calendar_button_false',
		'r34ics_display_calendar_memory_limit',
		'r34ics_feed_urls',
		'r34ics_previous_version',
		'r34ics_transients_expiration',
		'r34ics_url_get_contents_legacy_method',
		'r34ics_use_new_defaults_10_6',
		'r34ics_version',
	);
	foreach ((array)$settings_fields as $field) {
		$arr['Plugin Settings'][$field] = get_option($field);
	}
	$arr = apply_filters('r34ics_debug_array', $arr);
	// Process HTML entities on array values
	$arr = _r34ics_array_filter_recursive($arr, function($val) {
		if (is_scalar($val)) {
			$val = wp_strip_all_tags(preg_replace('/[\s]+/', ' ', $val));
		}
		return $val;
	});
	// Buffer and prepare output
	ob_start();
	echo '<hr /><pre>';
	print_r($arr);
	echo '</pre>';
	$r34ics_debug_output .= ob_get_clean();
	return null;
}
// Prepare debugging output
function _r34ics_wp_footer_debug_output() {
	if (!current_user_can('manage_options')) { return false; }
	global $r34ics_debug_output;
	if (empty($r34ics_debug_output)) { return false; }
	echo '<div class="r34ics_debug_wrapper minimized"><div class="r34ics_debug_toggle">&#9662;</div><div class="r34ics_debug_header"><h4>ICS Calendar Debugger</h4></div><div class="r34ics_debug_content">';
	echo wp_kses_post($r34ics_debug_output ?: '');
	echo '</div></div>';
	return true;
}
// Output final debugging results
add_action('wp_footer', '_r34ics_wp_footer_debug_output');
// Deprecated alias functions
function r34ics_debug($arr) { return _r34ics_debug($arr); }
function r34ics_wp_footer_debug_output() { return _r34ics_wp_footer_debug_output(); }
