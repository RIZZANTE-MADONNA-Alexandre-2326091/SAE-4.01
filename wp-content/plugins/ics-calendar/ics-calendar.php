<?php
/*
Plugin Name: ICS Calendar
Plugin URI: https://icscalendar.com
Description: Turn your Google Calendar, Microsoft Office 365 or Apple iCloud Calendar into a seamlessly integrated, auto-updating, zero-maintenance WordPress experience.
Version: 11.5.6
Requires at least: 4.9
Requires PHP: 7.0
Author: Room 34 Creative Services, LLC
Author URI: https://icscalendar.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ics-calendar
Domain Path: /i18n/languages/
*/

/*
  Copyright 2025 Room 34 Creative Services, LLC. (Email: info@room34.com)

	This program is free software; you can redistribute it and/or modify it under the terms
	of the GNU General Public License, version 2, as published by the Free Software
	Foundation.

	This program is distributed in the hope that it will be useful, but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
	PARTICULAR PURPOSE. See the GNU General Public License for more details.

	https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
	
	NOTE: The ICS Calendar and ICS Calendar Pro names, logos, and related branding assets
	are trademarks of Room 34 Creative Services, LLC and may not be used without permission.
	They are distributed with this software for the purpose of identification only. Reuse or
	redistribution of these assets is NOT covered by the GPL, and is hereby prohibited.
*/


// Don't load directly
if (!defined('ABSPATH')) { exit; }


// Check if embedded version is already been loaded, to prevent fatal error on activation
if (!class_exists('R34ICS2')) {
	
	// Load required files
	require_once(plugin_dir_path(__FILE__) . 'class-r34ics.php');
	require_once(plugin_dir_path(__FILE__) . 'functions.php');
	require_once(plugin_dir_path(__FILE__) . 'r34ics-ajax.php');
	
	
	// Backward compatibility for WP < 5.3
	if (!function_exists('wp_date')) {
		require_once(plugin_dir_path(__FILE__) . 'compatibility.php');
	}
	
	
	// Initialize plugin functionality
	function r34ics_plugins_loaded() {
	
		// Instantiate class
		global $R34ICS2;
		$R34ICS2 = new R34ICS2();
				
		// Conditionally run update function
		if (is_admin() && version_compare(get_option('r34ics_version'), $R34ICS2->version, '!=')) { r34ics_update(); }
		
	}
	add_action('plugins_loaded', 'r34ics_plugins_loaded');
	
	
	// Load text domain for translations
	/**
	 * Note: We are loading this absolutely as early as possible to avoid WP 6.7 warnings.
	 * ICS Calendar Pro has additional requirements for translations, hence the extremely
	 * low priority value 1 - PHP_INT_MAX.
	 */
	function r34ics_load_plugin_textdomain() {
		load_plugin_textdomain('ics-calendar', false, basename(plugin_dir_path(__FILE__)) . '/i18n/languages/');
	}
	add_action('init', 'r34ics_load_plugin_textdomain', 1 - PHP_INT_MAX);


	// Force loading of embedded translation files instead of community translations
	/**
	 * Note: This only runs just-in-time, when WordPress first encounters a translation
	 * string for this domain, for a language that already has a community translation
	 * file downloaded inside WP_LANG_DIR/plugins.
	 */
	function r34ics_load_textdomain_mofile($mofile, $domain) {
		if ($domain == 'ics-calendar' && strpos($mofile, WP_LANG_DIR . '/plugins') !== false) {
			$locale = apply_filters('plugin_locale', determine_locale(), $domain);
			$locales = r34ics_i18n_locales();
			// Only replace the locales we have translated directly within the plugin
			if (is_array($locales) && in_array($locale, $locales)) {
				$mofile = plugin_dir_path(__FILE__) . '/i18n/languages/' . $domain . '-' . $locale . '.mo';
			}
		}
		return $mofile;
	}
	add_filter('load_textdomain_mofile', 'r34ics_load_textdomain_mofile', 10, 2);

	
	// Get list of translated locales
	function r34ics_i18n_locales() {
		$locales = get_option('r34ics_i18n_locales');
		if (empty($locales)) {
			$locales = array();
			if (!function_exists('list_files')) {
				include_once(ABSPATH . 'wp-admin/includes/file.php');
			}
			$files = list_files(plugin_dir_path(__FILE__) . '/i18n/languages', 1);
			foreach ((array)$files as $file) {
				if (pathinfo($file, PATHINFO_EXTENSION) == 'mo') {
					$locales[] = str_replace('ics-calendar-', '', pathinfo($file, PATHINFO_FILENAME));
				}
			}
			update_option('r34ics_i18n_locales', $locales);
		}
		return $locales;
	}	
	
	
	// Install
	register_activation_hook(__FILE__, 'r34ics_install');
	function r34ics_install() {
		global $R34ICS2;
	
		// Flush rewrite rules
		flush_rewrite_rules();
		
		// Remember previous version
		$previous_version = get_option('r34ics_version');
		update_option('r34ics_previous_version', $previous_version);
		
		// Set version
		if (isset($R34ICS2->version)) {
			update_option('r34ics_version', $R34ICS2->version);
		}
		
		// New installation; write option to use new defaults
		if (empty($previous_version)) {
			update_option('r34ics_use_new_defaults_10_6', true);
		}
	
		// Prepare deferred admin notices
		global $r34ics_deferred_admin_notices;
		if (empty($r34ics_deferred_admin_notices)) {
			$r34ics_deferred_admin_notices = get_option('r34ics_deferred_admin_notices', array());
		}
	
		// Admin notice with link to settings
		$r34ics_deferred_admin_notices['r34ics_first_load'] = array(
			/* translators: 1: Plugin name (do not translate) and HTML tags 2. HTML tag 3. HTML tag 4. HTML tag 5. HTML tag */
			'content' => '<p>' . sprintf(esc_html__('Thank you for installing %1$s. Before creating your first calendar shortcode, please visit your %2$sGeneral Settings%3$s page and verify that your site language, timezone and date/time format settings are correct. See our %4$sUser Guide%5$s for more information.', 'ics-calendar'), '<strong>ICS Calendar</strong>', '<a href="' . admin_url('options-general.php') . '">', '</a>', '<a href="https://icscalendar.com/general-wordpress-settings/" target="_blank">', '</a>') . '</p>',
			'status' => 'info',
			'dismissible' => 'forever',
		);
		
		// Save deferred admin notices
		update_option('r34ics_deferred_admin_notices', $r34ics_deferred_admin_notices);
	}
	
	
	// Updates
	function r34ics_update() {
		global $R34ICS2;
	
		// Remember previous version
		$previous_version = get_option('r34ics_version');
		update_option('r34ics_previous_version', $previous_version);
		
		// Update version
		if (isset($R34ICS2->version)) {
			update_option('r34ics_version', $R34ICS2->version);
		}
		
		// Version-specific updates
		// v. 6.11.1 renamed option from 'r34ics_transient_expiration' to 'r34ics_transient_expiration' so it's not a transient itself
		if (version_compare($previous_version, '6.11.1', '<')) {
			$transients_expiration = get_option('r34ics_transient_expiration') ? get_option('r34ics_transient_expiration') : 3600;
			update_option('r34ics_transients_expiration', $transients_expiration);
			delete_option('r34ics_transient_expiration');
		}
		
		// v. 10.7.1 replaces "Load JS and CSS files on wp_enqueue_scripts action" option with check for block themes
		// Block themes support conditionally enqueuing JS and CSS when the page contains the ICS Calendar shortcode
		if (version_compare($previous_version, '10.7.1', '<')) {
			delete_option('r34ics_load_css_js_on_wp_enqueue_scripts');
		}
	
		// Prepare deferred admin notices
		global $r34ics_deferred_admin_notices;
		if (empty($r34ics_deferred_admin_notices)) {
			$r34ics_deferred_admin_notices = get_option('r34ics_deferred_admin_notices', array());
		}
	
		// Admin notice about refactored R34ICS2::_url_get_contents() method
		if (version_compare($previous_version, '11.0.0', '<')) {
			$r34ics_deferred_admin_notices['r34ics_refactoring_in_v_11'] = array(
				/* translators: 1. HTML tag 2. HTML tag 3: Plugin name (do not translate) 4. HTML tag 5. HTML tag */
				'content' => '<p>' . sprintf(esc_html__('%1$sPlease note:%2$s %3$s version 11.0 streamlines the way ICS feed URLs are retrieved. This change uses a standard built-in WordPress function, so it should be fully compatible with all existing installations. If you encounter any new issues after upgrading to version 11 or later, please visit the %4$sWordPress Support Forums%5$s for assistance.', 'ics-calendar'), '<strong>', '</strong>', 'ICS Calendar', '<a href="https://wordpress.org/support/plugin/ics-calendar/" target="_blank">', '</a>') . '</p>',
				'status' => 'info',
				'dismissible' => 'forever',
			);
		}
		else {
			unset($r34ics_deferred_admin_notices['r34ics_refactoring_in_v_11']);
		}
		// Admin notice about new default options
		if (version_compare($previous_version, '10.6.0', '<')) {
			$r34ics_deferred_admin_notices['r34ics_new_parameter_defaults_10_6'] = array(
				/* translators: 1. HTML tag 2. HTML tag 3: Plugin name (do not translate) 4. HTML tag 5. HTML tag 6. HTML tag 7. HTML tag 8. Additional translation string and HTML tags 9. HTML tag and plugin name (do not translate) 10. HTML tag */
				'content' => '<p>' . sprintf(esc_html__('%1$sPlease note:%2$s %3$s version 10.6 changes the default options for several shortcode settings. In order to maintain consistency, these new defaults are %4$snot%5$s enabled when upgrading from an earlier version. If you would like to learn more about the changes please read our %6$sblog post%7$s, or to switch to the new defaults, turn on the %8$s option on the %9$s settings%10$s page.', 'ics-calendar'), '<strong>', '</strong>', 'ICS Calendar', '<em>', '</em>', '<a href="https://icscalendar.com/updated-parameter-defaults-in-ics-calendar-10-6/" target="_blank">', '</a>', '<strong>' . esc_html__('Use new parameter defaults (v.10.6)', 'ics-calendar') . '</strong>', '<a href="' . esc_url(r34ics_get_admin_url('settings')) . '">ICS Calendar', '</a>') . '</p>',
				'status' => 'info',
				'dismissible' => 'forever',
			);
		}
		else {
			unset($r34ics_deferred_admin_notices['r34ics_new_parameter_defaults_10_6']);
		}
	
		// Save deferred admin notices
		update_option('r34ics_deferred_admin_notices', $r34ics_deferred_admin_notices);
	
		// Purge calendar transients
		r34ics_purge_calendar_transients();
		
	}
	
	
	// Purge transients on certain option updates
	add_action('update_option_start_of_week', 'r34ics_purge_calendar_transients');
	add_action('update_option_timezone_string', 'r34ics_purge_calendar_transients');


	// Deferred admin notices (runs on shutdown to catch all collected notices during script execution)
	add_action('shutdown', 'r34ics_deferred_admin_notices', PHP_INT_MAX - 1);
	
}
