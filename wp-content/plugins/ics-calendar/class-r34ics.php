<?php

// Don't load directly
if (!defined('ABSPATH')) { exit; }

if (!class_exists('R34ICS2')) {

	class R34ICS2 {
	
		const NAME = 'ICS Calendar';
		public $version = ''; // Value loads automatically as of v.10.8.7
	
		// Note: These names no longer correspond exactly with HTML named colors
		public $colors = array(
			'ics-red' => '#dc143c', // rgb(220,20,60)
			'ics-purple' => '#312a85', // rgb(49,42,133)
			'white' => '#ffffff', // rgb(255,255,255)
			'whitesmoke' => '#f0f3f6', // rgb(240,243,246)
			'gainsboro' => '#d9dcdf', // rgb(217,220,223)
			'darkgray' => '#a3a8ac', // rgb(163,168,172)
			'gray' => '#70787f', // rgb(112,120,127)
			'dimgray' => '#60686f', // rgb(96,104,111)
			'black' => '#10181f', // rgb(16,24,31)
			'dodgerblue' => '#1e90ff', // dodgerblue rgb(30,144,255)
			'gold' => '#ffd700', // gold rgb(255,215,0)
			'lemonchiffon' => '#fffacd', // limegreen rgb(255,250,205)
			'limegreen' => '#32cd32', // limegreen rgb(50,205,50)
			'orangered' => '#ff4500', // orangered rgb(255,69,0)
			'trans10' => 'rgba(105,105,105,0.1)',
			'trans20' => 'rgba(105,105,105,0.2)',
			'trans30' => 'rgba(105,105,105,0.3)',
			'trans40' => 'rgba(105,105,105,0.4)',
			'trans50' => 'rgba(105,105,105,0.5)',
			'trans60' => 'rgba(105,105,105,0.6)',
			'trans70' => 'rgba(105,105,105,0.7)',
			'trans80' => 'rgba(105,105,105,0.8)',
			'trans90' => 'rgba(105,105,105,0.9)',
		);
	
		public $debug = false;
		public $debug_messages = array();
	
		public $icon_logo = 'data:image/svg+xml;base64,PHN2ZyBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGZpbGwtcnVsZT0iZXZlbm9kZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLW1pdGVybGltaXQ9IjIiIHZpZXdCb3g9IjAgMCAyMzMgMjU3IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Im0xNTEuODA1IDIzLjI3M2gtNzIuODMydi05LjMwOWMwLTcuNzA3LTYuMjU3LTEzLjk2NC0xMy45NjQtMTMuOTY0cy0xMy45NjMgNi4yNTctMTMuOTYzIDEzLjk2NHY5Ljc0M2MtMjguNzQ5IDMuNTIxLTUxLjA0NiAyOC4wNTEtNTEuMDQ2IDU3Ljc0OHYxMTYuMzYzYzAgMzIuMTEyIDI2LjA3MSA1OC4xODIgNTguMTgyIDU4LjE4MmgxMTYuMzY0YzMyLjExMSAwIDU4LjE4Mi0yNi4wNyA1OC4xODItNTguMTgydi0xMTYuMzYzYzAtMzAuMzY1LTIzLjMxMS01NS4zMjgtNTIuOTk2LTU3Ljk1NHYtOS41MzdjMC03LjcwNy02LjI1Ny0xMy45NjQtMTMuOTYzLTEzLjk2NC03LjcwNyAwLTEzLjk2NCA2LjI1Ny0xMy45NjQgMTMuOTY0em0tMTI5LjUwNyAxNzIuNjEzYzAgMjAuMzMzIDE2LjUwOCAzNi44NDIgMzYuODQxIDM2Ljg0MmgxMTIuNDk5YzIwLjMzNCAwIDM2Ljg0Mi0xNi41MDkgMzYuODQyLTM2Ljg0MnYtMTAyLjc5M2gtMTg2LjE4MnptMTQxLjM4OCAxNi4yNDZjMTUuMDY5IDAgMjkuMTg4LTcuNDY3IDI5LjE4OC0yMy4yMTUgMC0yNS42NTgtMzcuMDYyLTE5LjI3OC0zNy4wNjItMjcuOTY2IDAtMi40NDQgMi44NTEtNC43NTIgNy43MzgtNC43NTIgNC40OCAwIDcuODc0IDIuMDM3IDcuODc0IDUuNDMxdjEuMzU3aDIwLjA5MnYtMS4zNTdjMC0xMy4xNjktMTEuMjY3LTIyLjgwOC0yNy44My0yMi44MDgtMTYuNDI3IDAtMjcuOTY2IDkuNTAzLTI3Ljk2NiAyMy40ODcgMCAyNS4xMTUgMzYuOTI2IDE4LjE5MSAzNi45MjYgMjcuODMgMCAyLjg1MS0zLjM5NCA0LjYxNi04LjE0NSA0LjYxNi01LjE1OSAwLTkuNTAzLTIuMTcyLTkuNTAzLTYuMTA5di0xLjYzaC0yMC4yMjh2MS42M2MwIDEzLjU3NSAxMS41MzkgMjMuNDg2IDI4LjkxNiAyMy40ODZ6bS02My45NDIgMGMxOC4xOTEgMCAzMC4xMzgtMTEuNDA0IDMwLjEzOC0yOC43ODF2LTIuMDM2aC0yMC41djIuNTc5YzAgNi4yNDUtMy45MzYgOS45MS05LjYzOCA5LjkxLTcuMTk1IDAtMTAuNzI1LTQuMDcyLTEwLjcyNS0xMS44MTF2LTEzLjAzMmMwLTcuNzM5IDMuNjY1LTExLjgxMSAxMC43MjUtMTEuODExIDUuNzAyIDAgOS42MzggMy42NjUgOS42MzggOS45MXYyLjU3OWgyMC41di0yLjAzNmMwLTE3LjM3Ny0xMS45NDctMjguNzgxLTMwLjI3NC0yOC43ODEtMTkuMjc4IDAtMzAuOTUzIDExLjY3Ni0zMC45NTMgMzAuMDAzdjEzLjMwNGMwIDE4LjMyOCAxMS42NzUgMzAuMDAzIDMxLjA4OSAzMC4wMDN6bS02MC40MTMtMi4wMzdoMjAuMzY0di02OS4yMzZoLTIwLjM2NHptMC03Ni41NjdoMjAuMzY0di0xOS44MjFoLTIwLjM2NHoiIGZpbGw9IiNmZmYiLz48L3N2Zz4=';
		
		public $limit_days = 365;
		public $tz = null;
		
		protected $r34ics_path = '';
		protected $ical_path = '/vendors/ics-parser/src/ICal/ICal.php';
		protected $event_path = '/vendors/ics-parser/src/ICal/Event.php';
		protected $ical_legacy_path = '/vendors/ics-parser-legacy/src/ICal/ICal.php';
		protected $event_legacy_path = '/vendors/ics-parser-legacy/src/ICal/Event.php';
		protected $pand_path = '/vendors/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php';
		protected $parser_loaded = false;
		protected $scripts_enqueued = false;
		protected $scripts_registered = false;
		
		protected $shortcode_defaults = array(
			'ajax' => false,
			'arrayonly' => false,
			'attach' => '',
			'basicauth' => false,
			'category' => '',
			'color' => '',
			'columnlabels' => '',
			'combinemultiday' => false,
			'compact' => false,
			'count' => 0,
			'curlopts' => false, // Deprecated
			'currentweek' => false, // Deprecated
			'customoptions' => '',
			'debug' => false,
			'description' => '',
			'eventdesc' => false,
			'eventdl' => false,
			'eventlocaltime' => false,
			'extendmultiday' => false,
			'feedlabel' => '',
			'feedlabelindesc' => false,
			'fixredundantuids' => false,
			'format' => '',
			'formatmonthyear' => 'F Y',
			'fulldateintable' => false,
			'guid' => '',
			'hidealldayindicator' => false,
			'hidecancelledevents' => false,
			'hideprivateevents' => false,
			'hiderecurrence' => '',
			'hidetentativeevents' => false,
			'hidetimes' => false,
			'htmltagdate' => '',
			'htmltageventdesc' => '',
			'htmltageventtitle' => '',
			'htmltagmonth' => '',
			'htmltagtime' => '',
			'htmltagtitle' => '',
			'legacyparser' => false,
			'legendinline' => false, // Deprecated
			'legendposition' => '',
			'legendstyle' => '',
			'limitdays' => '',
			'limitdayscustom' => false, // Boolean based on limitdays; never set in shortcode
			'linebreakfix' => false,
			'linktitles' => false,
			'location' => false,
			'mapsource' => '',
			'maskinfo' => false,
			'method' => false, // Deprecated
			'monthnav' => '',
			'nolink' => false,
			'nomobile' => false,
			'nomonthheaders' => false,
			'nostyle' => false,
			'organizer' => false,
			'pagination' => false,
			'paginationposition' => '',
			'pastdays' => 0,
			'reload' => false,
			'resources' => false,
			'reverse' => false,
			'sametab' => 'local',
			'showendtimes' => false,
			'skip' => 0,
			'skipdomainerrors' => false,
			'skiprecurrence' => false,
			'solidcolors' => false,
			'startdate' => '',
			'stickymonths' => false,
			'tablebg' => '',
			'timeformat' => '',
			'title' => '',
			'toggle' => false,
			'tz' => '',
			'ua' => '',
			'url' => '',
			'view' => 'month',
			'weeknumbers' => false,
			'whitetext' => false,
		);
		
		protected $shortcode_defaults_new_10_6 = array(
			'count' => array('list' => 5, 'default' => 0),
			'description' => false,
			'eventdesc' => true,
			'htmltagdate' => array('basic' => 'div', 'default' => 'h4'),
			'htmltageventdesc' => array('basic' => 'div', 'default' => 'div'),
			'htmltageventtitle' => array('basic' => 'div', 'default' => 'span'),
			'htmltagmonth' => array('basic' => 'div', 'default' => 'h3'),
			'htmltagtime' => array('basic' => 'div', 'default' => 'span'),
			'htmltagtitle' => array('basic' => 'div', 'default' => 'h2'),
			'hiderecurrence' => true,
			'limitdays' => array('basic' => 365, 'list' => 365, 'week' => 21, 'default' => 455),
			'location' => true,
			'organizer' => true,
			'pastdays' => array('basic' => 0, 'list' => 0, 'week' => 7, 'default' => 90),
			'showendtimes' => true,
			'stickymonths' => true,
			'title' => false,
		);
		
		protected $shortcode_feed_array_params = array('category', 'color', 'feedlabel', 'url');
		protected $shortcode_dynamic_values = array('guid', 'startdate');
		
		protected $views = array('basic', 'list', 'month', 'week');
		protected $list_style_views = array('basic', 'list');
		
		
		public function __construct() {
	
			// Set version
			$this->version = $this->_get_version();
	
			// Set base timezone for wp_date() functions in templates
			$this->tz = new DateTimeZone(get_option('timezone_string') ? get_option('timezone_string') : 'UTC');
	
			// Set property values
			$this->r34ics_path = rtrim(plugin_dir_path(__FILE__), '/');
			$this->ical_path = $this->r34ics_path . $this->ical_path;
			$this->event_path = $this->r34ics_path . $this->event_path;
			$this->ical_legacy_path = $this->r34ics_path . $this->ical_legacy_path;
			$this->event_legacy_path = $this->r34ics_path . $this->event_legacy_path;
			$this->pand_path = plugin_dir_path(__FILE__) . $this->pand_path;
	
			// Define allowed views (must load later so filters can be applied by other plugins)
			add_action('init', function() {
				$this->views = apply_filters('r34ics_views', $this->views);
				$this->list_style_views = apply_filters('r34ics_list_style_views', $this->list_style_views);
			}, 11);
			
			// WP settings
			add_action('init', array(&$this, 'wp_settings'));
	
			// Set up admin menu
			add_action('admin_menu', array(&$this, 'admin_menu'));
			
			// Enqueue admin scripts
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			
			// Register scripts
			add_action('wp_enqueue_scripts', array(&$this, 'register_scripts'));
			
			// Enqueue scripts
			// @todo Resolve issues with late/conditional enqueuing, so this can be removed
			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'), 20);
	
			// Add ICS shortcode
			add_shortcode('ics_calendar', array(&$this, 'shortcode'));
	
			// Add editor button
			add_action('admin_init', array(&$this, 'editor_button'));
	
			// Add admin notices
			add_action('admin_init', array(&$this, 'admin_notices_persist_dismissals'));
			add_action('admin_notices', array(&$this, 'admin_notices'));
			
			// Add query variables
			add_filter('query_vars', array(&$this, 'query_vars'));
			
			// Core WP actions
			
			// Core WP filters
			add_filter('http_request_host_is_external', array(&$this, 'http_request_host_is_external'), 10, 3);
		
			// R34ICS2 actions
			add_action('r34ics_display_calendar_after_render_template', array(&$this, 'r34ics_display_calendar_after_render_template'), 10, 3);
			add_action('r34ics_display_calendar_after_wrapper', array(&$this, 'r34ics_display_calendar_after_wrapper'), 10, 3);
			add_action('r34ics_display_calendar_before_render_template', array(&$this, 'r34ics_display_calendar_before_render_template'), 10, 3);
			add_action('r34ics_display_calendar_before_wrapper', array(&$this, 'r34ics_display_calendar_before_wrapper'), 10, 3);
			add_action('r34ics_display_calendar_render_template', array(&$this, 'r34ics_display_calendar_render_template'), 10, 3);
			
			// R34ICS2 filters
			add_filter('r34ics_calendar_classes', array(&$this, 'r34ics_calendar_classes'), 10, 3);
			add_filter('r34ics_display_add_calendar_button', array(&$this, 'r34ics_display_add_calendar_button'), 10, 1);
			add_filter('r34ics_display_calendar_exclude_event', array(&$this, 'r34ics_display_calendar_exclude_event'), 10, 3);
			
			// Handle form submissions
			add_action('template_redirect', array(&$this, 'template_redirect'), 10, 0);
			
			// Add plugin action links
			add_filter('plugin_action_links_ics-calendar/ics-calendar.php', array($this, 'admin_plugin_action_links'));
			
			// Filter defaults
			$this->shortcode_defaults_new_10_6 = apply_filters('r34ics_shortcode_defaults_new_10_6', $this->shortcode_defaults_new_10_6);
	
		}
		
		
		public function admin_enqueue_scripts() {
			wp_enqueue_script('ics-calendar-admin', plugin_dir_url(__FILE__) . 'assets/admin-script.min.js', array('jquery'), get_option('r34ics_version'), true);
			wp_enqueue_style('ics-calendar-admin', plugin_dir_url(__FILE__) . 'assets/admin-style.min.css', false, get_option('r34ics_version'));
		}
	
	
		public function admin_menu() {
			if (!class_exists('R34ICS2Pro')) {
				add_menu_page(
					'ICS Calendar',
					'ICS Calendar',
					'edit_posts',
					'ics-calendar',
					array(&$this, 'admin_page'),
					$this->icon_logo,
					49
				);
			}
		}
	
	
		public function admin_notices() {
			$current_screen = get_current_screen();

			// Prepare deferred admin notices
			global $r34ics_deferred_admin_notices;
			if (empty($r34ics_deferred_admin_notices)) {
				$r34ics_deferred_admin_notices = get_option('r34ics_deferred_admin_notices', array());
			}
	
			// Dashboard-only notices for administrator-level users
			if (
				current_user_can('manage_options') &&
				in_array($current_screen->base, array(
					'dashboard',
					'options-general',
					'toplevel_page_ics-calendar',
				))
			) {
			
				// This is a WordPress Playground implementation
				if (r34ics_is_playground()) {
					$r34ics_deferred_admin_notices['r34ics_is_playground'] = array(
						/* translators: 1. HTML tag 2. HTML tag */
						'content' => '<p>' . sprintf(esc_html__('%1$sYou are running ICS Calendar in a WordPress Playground implementation.%2$s Because ICS Calendar is intended to run on a web server, some features may be unavailable or may not function properly.', 'ics-calendar'), '<strong>', '</strong>') . '</p>',
						'status' => 'warning',
						'dismissible' => 'forever',
					);
				}
	
				// Warning about UTC-based timezones
				if (empty(get_option('timezone_string')) || strpos(get_option('timezone_string'), 'UTC') === 0) {
					$r34ics_deferred_admin_notices['r34ics_timezone_utc'] = array(
						/* translators: 1. HTML tag 2. HTML tag 3: Plugin name (do not translate) and HTML tags 4. HTML tag 5. HTML tag 6. HTML tag 7. HTML tag */
						'content' => '<p>' . sprintf(esc_html__('%1$sYour site is currently using a UTC offset-based timezone setting.%2$s This can produce time display errors in the %3$s plugin in locations that observe Daylight Saving Time. Please %4$schange your timezone setting%5$s to the city nearest your location, in the same timezone, for proper time display. See our %6$sdocumentation%7$s for additional information on this issue.', 'ics-calendar'), '<strong>', '</strong>', '<strong>ICS Calendar</strong>', '<a href="' . admin_url('options-general.php#timezone_string') . '">', '</a>', '<a href="https://icscalendar.com/general-wordpress-settings/#timezone" target="_blank">', '</a>') . '</p>',
						'status' => 'error',
						'dismissible' => 1,
					);
				}
	
			}
	
			// Save deferred admin notices
			update_option('r34ics_deferred_admin_notices', $r34ics_deferred_admin_notices);
	
		}
		
		
		public function admin_notices_persist_dismissals() {
			$this->_load_pand();
			if (class_exists('PAnD')) {
				PAnD::init();
			}
		}


		public function admin_page() {
		
			// Run utilities if applicable
			$this->_admin_page_callback_utilities();
	
			// URL tester
			$url_tester_result = $this->_admin_page_callback_url_tester();
		
			// Save admin settings
			$this->_admin_page_callback_save_settings();
	
			// Render template
			include(plugin_dir_path(__FILE__) . 'templates/admin/admin.php');
	
		}
		
		
		public function admin_plugin_action_links($links) {
			if (!class_exists('R34ICS2Pro')) {
				array_unshift(
					$links,
					'<a href="' . admin_url('admin.php?page=ics-calendar#utilities') . '">' . esc_html__('Utilities', 'ics-calendar') . '</a>',
					'<a href="' . admin_url('admin.php?page=ics-calendar#settings') . '">' . esc_html__('Settings', 'ics-calendar') . '</a>',
					'<a href="https://icscalendar.com/user-guide/" target="_blank">' . esc_html__('User Guide', 'ics-calendar') . '</a>',
					'<a href="https://wordpress.org/support/plugin/ics-calendar/" target="_blank">' . esc_html__('Support', 'ics-calendar') . '</a>'
				);
				$links[] = '<a href="https://icscalendar.com/pro/" target="_blank" style="font-weight: bold; color: crimson;">' . esc_html__('Upgrade to PRO!', 'ics-calendar') . '</a>';
			}
			return $links;
		}
		
		
		public function color_key_html($args, $ics_data, $no_toggles=false) {
			if (!empty($args['legendstyle']) && $args['legendstyle'] == 'none') { return null; }
			ob_start();
			if (count((array)$ics_data['feed_titles']) > 1) {
				?>
				<div class="ics-calendar-color-key<?php if (empty($args['legendstyle']) || $args['legendstyle'] == 'inline') { echo ' inline'; } ?>">
					<?php
					if (empty($no_toggles) && count($ics_data['feed_titles']) > 4) {
						$toggle_all_uid = r34ics_uid();
						?>
						<div class="ics-calendar-color-key-header">
							<label for="<?php echo esc_attr($toggle_all_uid); ?>"><input type="checkbox" id="<?php echo esc_attr($toggle_all_uid); ?>" class="ics-calendar-color-key-toggle-all" data-feed-key="ALL" checked="checked" />
							<?php esc_html_e('Show/hide all', 'ics-calendar'); ?>
							</label>
						</div>
						<?php
					}
					foreach ((array)$ics_data['feed_titles'] as $feed_key => $feed_title) {
						$toggle_uid = r34ics_uid();
						?>
						<div class="ics-calendar-color-key-item" data-feed-key="<?php echo intval($feed_key); ?>" data-feed-color="<?php echo !empty($ics_data['colors'][$feed_key]['base']) ? esc_attr($ics_data['colors'][$feed_key]['base']) : ''; ?>">
							<?php
							if (empty($no_toggles)) {
								?>
								<label for="<?php echo esc_attr($toggle_uid); ?>"><input type="checkbox" id="<?php echo esc_attr($toggle_uid); ?>" class="ics-calendar-color-key-toggle" data-feed-key="<?php echo intval($feed_key); ?>" data-feed-color="<?php echo !empty($ics_data['colors'][$feed_key]['base']) ? esc_attr($ics_data['colors'][$feed_key]['base']) : ''; ?>" checked="checked" />
								<?php
							}
							echo wp_kses_post($feed_title ?: '');
							do_action('r34ics_color_key_html_after_feed_title', $feed_key, $args, $ics_data);
							?>
							</label>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			}
			$color_key_content = ob_get_clean();
			return !r34ics_empty_content($color_key_content) ? $color_key_content : null;
		}
	
	
		public function days_of_week($format=null) {
			global $wp_locale;
			$days_of_week = array();
			// Do not abbreviate day names in Arabic (WP_Locale::get_weekday_initial() translations are apparently incorrect)
			if (get_locale() == 'ar' || get_locale() == 'ary') {
				$days_of_week = array(
					0 => $wp_locale->get_weekday(0),
					1 => $wp_locale->get_weekday(1),
					2 => $wp_locale->get_weekday(2),
					3 => $wp_locale->get_weekday(3),
					4 => $wp_locale->get_weekday(4),
					5 => $wp_locale->get_weekday(5),
					6 => $wp_locale->get_weekday(6),
				);
			}
			// Default handling for all other languages
			else {
				switch ($format) {
					case 'min':
						$days_of_week = array(
							0 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(0)),
							1 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(1)),
							2 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(2)),
							3 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(3)),
							4 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(4)),
							5 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(5)),
							6 => $wp_locale->get_weekday_initial($wp_locale->get_weekday(6)),
						);
						break;
					case 'short':
						$days_of_week = array(
							0 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(0)),
							1 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(1)),
							2 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(2)),
							3 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(3)),
							4 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(4)),
							5 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(5)),
							6 => $wp_locale->get_weekday_abbrev($wp_locale->get_weekday(6)),
						);
						break;
					case 'full':
					default:
						$days_of_week = array(
							0 => $wp_locale->get_weekday(0),
							1 => $wp_locale->get_weekday(1),
							2 => $wp_locale->get_weekday(2),
							3 => $wp_locale->get_weekday(3),
							4 => $wp_locale->get_weekday(4),
							5 => $wp_locale->get_weekday(5),
							6 => $wp_locale->get_weekday(6),
						);
						break;
				}
			}
			return $days_of_week;
		}
		
		
		public function days_of_week_map() {
			$map = array_combine($this->days_of_week('full'), $this->days_of_week('short'));
			return $map;
		}
	
	
		public function display_calendar($args) {
	
			// Don't do anything in Block Editor
			if (function_exists('get_current_screen') && $current_screen = get_current_screen()) {
				if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
					return;
				}
			}
			
			// Merge args with defaults
			$args = array_merge($this->shortcode_defaults, $args);
			extract($args);
			
			do_action('r34ics_display_calendar_after_args', $args);
			
			// Early render -- bypass regular parsing process if an external view requires it
			$early_render = apply_filters('r34ics_display_calendar_early_render', false, $view);
			if (!empty($early_render)) {
					// Handle other views externally
					do_action('r34ics_display_calendar_render_template', $view, $args, null);
					return;
			}
					
			// Reset debug messages for this call (Administrator role only)
			$this->debug = current_user_can('manage_options') ? $debug : false;
			if ($this->debug) { $this->debug_messages = array('args' => $args); }
		
			// Adjust memory limit if appropriate
			if ($display_calendar_memory_limit = get_option('r34ics_display_calendar_memory_limit')) {
				// Check against current memory limit
				if (r34ics_memory_limit_mb() < intval($display_calendar_memory_limit)) {
					ini_set('memory_limit', $display_calendar_memory_limit);
				}
			}
			if ($this->debug) {
				$this->debug_messages['Memory limit (MB)'] = r34ics_memory_limit_mb();
			}
			
			// Get ICS data, from transient if possible
			$transient_name = __METHOD__ . '_' . $this->display_calendar_transient_hash($args);
			$loaded_from_transient = null;
			$ics_data = null;
			if (intval($reload) !== 1 && intval($this->debug) < 3) {
				$loaded_from_transient = true;
				$ics_data = get_transient($transient_name);
				if (!empty($ics_data) && $this->debug) {
					$this->debug_messages['Loaded from transient'] = size_format(strlen(serialize($ics_data)), 2);
					$this->debug_messages['Transient name'] = $transient_name;
				}
			}
	
			// No transient ICS data; retrieve ICS file from server
			// Note: Also checking for value of '1' because AJAX requests sometimes return '1' for an as-yet undetermined reason
			if (empty($ics_data) || $ics_data == '1') {
				$loaded_from_transient = false;
				
				// Set basic info parameters
				$ics_data = $this->display_calendar_ics_data_init($args);
					
				// Determine rough date range for parser
				$range = $this->display_calendar_date_range($args);
				
				// Default values for display date range
				$first_date = r34ics_date('Ymd');
				$limit_date = r34ics_date('Ymd', $first_date, null, '+' . intval($limitdays-1) . ' days');
				
				// Set exact display date range, per view
				switch ($view) {
					case 'week':
						if (($limitdays >= 1 && $limitdays <= 7) || !empty($startdate)) {
							if (!empty($startdate) && intval($startdate) > 20000000) {
								$first_date = $startdate;
							}
							else {
								$first_date = r34ics_date('Ymd');
							}
							$first_ts = strtotime($first_date);
							$limit_date = r34ics_date('Ymd', $first_date, null, '+' . intval($limitdays-1) . ' days');
						}
						else {
							$cw1 = r34ics_first_day_of('week');
							$first_date = r34ics_date('Ymd', '@' . $cw1, null, '-7 days');
							$first_ts = strtotime($first_date);
							$limit_date = r34ics_date('Ymd', '@' . $cw1, null, '+13 days');
						}
						break;
					case 'basic':
					case 'list':
						// For $reverse to function properly, $pastdays and $limitdays must be equal
						if (!empty($reverse)) {
							if (!empty($pastdays)) { $limitdays = $pastdays; }
							else { $pastdays = $limitdays; }
						}
						if (!empty($startdate) && intval($startdate) > 20000000) {
							$first_date = $startdate;
						}
						elseif (!empty($pastdays)) {
							$first_date = r34ics_date('Ymd', null, null, '-' . intval($pastdays) . ' days');
						}
						else {
							$first_date = r34ics_date('Ymd');
						}
						$first_ts = strtotime($first_date);
						$limit_date = r34ics_date('Ymd', $first_date, null, '+' . intval($limitdays-1) . ' days');
						break;
					case 'month':
						if (!empty($startdate) && intval($startdate) > 20000000) {
							$first_date_base = $startdate;
						}
						elseif (!empty($pastdays)) {
							$first_date_base = r34ics_date('Ymd', $first_date, null, '-' . intval($pastdays) . ' days');
						}
						else {
							$first_date_base = '';
						}
						$first_date = r34ics_date('Ymd', '@' . r34ics_first_day_of('month', $first_date_base));
						$first_ts = strtotime($first_date);
						$limit_date_base = r34ics_date('Ymd', $first_date, null, '+' . intval($limitdays-1) . ' days');
						$limit_date = r34ics_date('Ymd', '@' . r34ics_last_day_of('month', $limit_date_base));
						break;
					default:
						// Handle other views externally
						$first_date = apply_filters('r34ics_display_calendar_set_first_date', $first_date, $view, $startdate, $pastdays);
						$first_ts = strtotime($first_date);
						$limit_date = apply_filters('r34ics_display_calendar_set_limit_date', $limit_date, $view, $first_ts, $limitdays);
						break;
				}
	
				// Set earliest and latest dates
				switch ($view) {
					case 'basic':
					case 'list':
					case 'month':
						$ics_data['earliest'] = substr(($first_date ?? ''),0,6);
						$ics_data['latest'] = !empty($limitdayscustom) ? r34ics_date('Ym', $first_date, null, '+' . intval($limitdays-1) . ' days') : substr(($limit_date ?? ''),0,6);
						break;
					case 'week':
						$ics_data['earliest'] = $first_date;
						$ics_data['latest'] = !empty($limitdayscustom) ? r34ics_date('Ymd', $first_date, null, '+' . intval($limitdays-1) . ' days') : $limit_date;
						break;
					default:
						// Handle other views externally
						$ics_data['earliest'] = apply_filters('r34ics_display_calendar_set_earliest', $first_date, $view);
						$ics_data['latest'] = apply_filters('r34ics_display_calendar_set_latest', $limit_date, $view, $first_ts, $limitdays, $limitdayscustom);
						break;
				}
	
				// Debugging information
				if ($this->debug) {
					$this->debug_messages['Included date range'] = $first_date . ' to ' . $limit_date;
				}
			
				// Process each individual feed URL
				$single_feed = (count((array)$ics_data['urls']) == 1);
				foreach ((array)$ics_data['urls'] as $feed_key => $url) {
				
					// Get timezone for this feed
					$url_tz = r34ics_get_feed_tz($ics_data, $feed_key);
			
					// Parse feed URL
					$ICal = $this->ical_parse_url($url, $url_tz->getName(), $args, $range);
	
					// Failed to parse feed into an ICal object; continue to next feed (if exists)
					if (!is_object($ICal)) {
						// Display error if debugging is turned on, otherwise just continue
						if ($this->debug) {
							trigger_error(esc_html__('ICS file could not be retrieved or was empty. Please verify URL is correct, and check your php.ini configuration to verify either cURL or allow_url_fopen is available. If you are using spaces to delimit multiple URLs, you may also wish to try using the pipe character instead. Affected URL:', 'ics-calendar') . ' ' . esc_url($url), E_USER_NOTICE);
						}
						continue;
					}
					
					// Update general calendar information
					// Handle 1/true literal values for $title and $description
					if (in_array((string)$title, array('1', 'true'))) {
						$ics_data['title'] = $single_feed ? $ICal->calendarName() : '';
					}
					if (in_array((string)$description, array('1', 'true'))) {
						$ics_data['description'] = $single_feed ? $ICal->calendarDescription() : '';
					}
					$ics_data['timezone'][$url] = $ICal->calendarTimeZone();
					if (is_array($ics_data['feed_titles']) && empty($ics_data['feed_titles'][$feed_key])) {
						$ics_data['feed_titles'][$feed_key] = $ICal->calendarName();
					}
	
					// Debugging information
					if ($this->debug) {
						$this->debug_messages[$url]['Calendar name'] = $ICal->calendarName();
						$this->debug_messages[$url]['Calendar description'] = $ICal->calendarDescription();
						$this->debug_messages[$url]['Calendar time zone'] = $ICal->calendarTimeZone();
						$this->debug_messages[$url]['Default time zone'] = $url_tz->getName();
						$this->debug_messages[$url]['Parsed date range'] = $range['start'] . ' to ' . $range['end'];
						$this->debug_messages[$url]['Filter days after'] = $range['days_after'];
						$this->debug_messages[$url]['Filter days before'] = $range['days_before'];
						if ($ICal->hasEvents() == false) {
							$this->debug_messages[$url]['Errors'][] = 'Calendar contains no events.';
						}
						$current_memory_usage = memory_get_usage();
						if (!isset($this->debug_messages['Peak memory usage']) || $current_memory_usage > $this->debug_messages['Peak memory usage']) {
							$this->debug_messages['Peak memory usage'] = memory_get_usage();
						}
					}
	
					// Process events
					if ($ics_events = $ICal->eventsFromRange($range['start'], $range['end'])) {
					
						// Assemble events
						foreach ((array)$ics_events as $event_key => $event) {
						
							// Allow external filtering of events
							$exclude_event = apply_filters('r34ics_display_calendar_exclude_event', false, $event, $args);
							if (!empty($exclude_event)) { continue; }
							
							// Set timezone(s) for event
							if (!empty($args['eventlocaltime']) || strpos('T', $args['timeformat']) !== false) {
								$event_start_tz = (!empty($event->dtstart_array[0]['TZID']) ? timezone_open($event->dtstart_array[0]['TZID']) : $url_tz);
								$event_end_tz = (!empty($event->dtend_array[0]['TZID']) ? timezone_open($event->dtend_array[0]['TZID']) : $url_tz);
							}
							else {
								$event_start_tz = $event_end_tz = $url_tz;
							}
						
							// Set start and end dates for event
							$dtstart_date = wp_date('Ymd', $event->dtstart_array[2], $event_start_tz);
							// Conditional is for events that are missing DTEND altogether, which should never be the case but has been observed in customer support
							$dtend_date = wp_date('Ymd', ($event->dtend_array[2] ?? $event->dtstart_array[2]), $event_end_tz);
	
							// All-day events
							if (strlen($event->dtstart ?? '') == 8 || (strpos(($event->dtstart ?? ''), 'T000000') !== false && strpos(($event->dtend ?? ''), 'T000000') !== false)) {
								$dtstart_time = '';
								$dtend_time = '';
								$all_day = true;
							}
							// Start/end times
							else {
								$dtstart_time = wp_date('His', $event->dtstart_array[2], $event_start_tz);
								// Conditional is for events that are missing DTEND altogether, which should never be the case but has been observed in customer support
								$dtend_time = wp_date('His', ($event->dtend_array[2] ?? $event->dtstart_array[2]), $event_end_tz);
								$all_day = false;
							}
							
							// Workaround for events in feeds that do not contain an end date/time
							if (empty($dtend_date)) { $dtend_date = isset($dtstart_date) ? $dtstart_date : null; }
							if (empty($dtend_time)) { $dtend_time = isset($dtstart_time) ? $dtstart_time : null; }
							
							// Mask info
							if (!empty($maskinfo)) {
								// Replace event title ("summary") with $maskinfo text string
								$event->summary = $maskinfo;
								// Remove other potentially sensitive information from the event
								$event->additionalProperties = array();
								$event->attach = '';
								$event->attendee = '';
								$event->contact = '';
								$event->description = '';
								$event->geo = '';
								$event->location = '';
								$event->organizer = '';
								$event->resources = '';
								$event->url = '';
								$event->x_alt_desc = '';
							}
							
							// General event item details (regardless of all-day/start/end times)
							// Event description and other details have $maskinfo check in r34ics_has_desc() function
							$event_item = array(
								'attach' => $this->parse_attach_array($event->attach_array, $sametab),
								'categories' => ($event->categories ?: ''),
								'class' => ($event->class ?: ''),
								'contact' => ($event->contact ?: ''),
								'dtend_date' => $dtend_date,
								'dtend_time' => $dtend_time,
								'dtend' => $event->dtend,
								'dtstamp' => $event->dtstamp,
								'dtstart_date' => $dtstart_date,
								'dtstart_time' => $dtstart_time,
								'dtstart' => $event->dtstart,
								'duration' => ($event->duration ?: ''),
								'eventdesc' => $this->_event_field_handling('eventdesc', $event, $args),
								'exdate' => ($event->exdate ?: ''),
								'feed_key' => $feed_key,
								'freebusy' => ($event->freebusy ?: ''),
								'geo' => ($event->geo ?: ''),
								'label' => ($event->summary ?: ''),
								'location' => ($event->location ?: ''),
								'organizer' => ($event->organizer_array ?: ''),
								'recurrence_id' => ($event->recurrence_id ?: ''),
								'resources' => ($event->resources ?: ''),
								'rrule' => ($event->rrule ?: ''),
								'rdate' => ($event->rdate ?: ''),
								'sequence' => ($event->sequence ?: ''),
								'status' => ($event->status ?: ''),
								'transp' => ($event->transp ?: ''),
								'tz_start' => (@$args['eventlocaltime'] ? (@$event->dtstart_array[0]['TZID'] ?: $url_tz) : $url_tz),
								'tz_end' => (@$args['eventlocaltime'] ? (@$event->dtend_array[0]['TZID'] ?: $url_tz) : $url_tz),
								'uid' => ($event->uid ?: ''),
								'url' => empty($args['nolink']) ? ($event->url ?? ($event->uri ?: '')) : '',
							);
							
							// Scrape a URL from the description
							if (empty($args['nolink']) && empty($event_item['url']) && !empty($event->description)) {
								$event_item['url'] = r34ics_scrape_url_from_string($event->description, false, true);
							}
							
							// Multi-day events
							if	(
									$dtend_date != $dtstart_date &&
									// Watch out for events that are NOT multiday, but just extend into the next day, unless extendmultiday is set
									(
										in_array($args['extendmultiday'], array('both', 'overnight')) ||
										!($dtend_date == r34ics_date('Ymd', $dtstart_date, $event_start_tz, '+1 day') && !empty($dtend_time))
									)
								)
							{
							/* @todo Switching to this in 11.3.2.1 introduced issues for more users than it helped; add a setting to make it optional!
							if ($dtend_date != $dtstart_date) {
							*/
								$loop_date = $dtstart_date;
								while ($loop_date <= $dtend_date) {
									// Classified as an all-day event and we've hit the end date -- don't display unless extendmultiday is set
									if ($all_day && $loop_date == $dtend_date && !in_array($args['extendmultiday'], array('both', 'allday'))) {
										break;
									}
									// Multi-day events may be given with end date/time as midnight of the NEXT day (unless extendmultiday is set)
									$effective_end_date =	(!empty($all_day) && empty($dtend_time) && !in_array($args['extendmultiday'], array('both', 'allday')))
															? r34ics_date('Ymd', $dtend_date, $event_end_tz, '-1 day')
															: $dtend_date;
									if ($dtstart_date == $effective_end_date) {
										$ics_data['events'][$dtstart_date]['all-day'][] = apply_filters('r34ics_display_calendar_event_item', $event_item, $event, $args);
										break;
									}
									// Get full date/time range of multi-day event (to be used in displaying multi-day events as single items in list views)
									$event_item['multiday'] = array(
										'start_date' => $dtstart_date,
										'start_time' => $dtstart_time,
										'end_date' => $effective_end_date,
										'end_time' => $dtend_time,
										'all_day' => $all_day,
									);
									if ($loop_date == $dtstart_date) {
										$event_item['multiday']['position'] = 'first';
									}
									elseif ($loop_date == $effective_end_date) {
										$event_item['multiday']['position'] = 'last';
									}
									else {
										$event_item['multiday']['position'] = 'middle';
									}
									$event_item['start'] = !empty($dtstart_time) ? r34ics_time_format($dtstart_time, $timeformat, $event_item['tz_start'], $dtstart_date) : null;
									$event_item['end'] = !empty($dtend_time) ? r34ics_time_format($dtend_time, $timeformat, $event_item['tz_end'], $dtend_date) : null;
									$ics_data['events'][$loop_date]['all-day'][] = apply_filters('r34ics_display_calendar_event_item', $event_item, $event, $args);
									// Increment loop date
									$loop_date = r34ics_date('Ymd', $loop_date, $event_start_tz, '+1 day');
								}
							}
							// All-day events
							elseif ($all_day) {
								$ics_data['events'][$dtstart_date]['all-day'][] = apply_filters('r34ics_display_calendar_event_item', $event_item, $event, $args);
							}
							// Events with start/end times
							else {
								$event_item['start'] = r34ics_time_format($dtstart_time, $timeformat, $event_item['tz_start'], $dtstart_date);
								$event_item['end'] = r34ics_time_format($dtend_time, $timeformat, $event_item['tz_end'], $dtend_date);
								$ics_data['events'][$dtstart_date]['t'.$dtstart_time][] = apply_filters('r34ics_display_calendar_event_item', $event_item, $event, $args);
							}
						}
					}
	
					// Debugging information (must occur outside conditional in case it evaluates to false)
					if ($this->debug >= 2) {
						/* This may output a very large amount of data */
						$this->debug_messages[$url]['ICS Parser Data'] = !isset($ics_events) ? 'NO DATA' : $ics_events;
					}
					$this->debug_messages[$url]['Events parsed'] = !isset($ics_events) ? 0 : count((array)$ics_events);
			
					// If no events, create empty array for today to allow calendars to build
					if (empty($ics_data['events'])) {
						$ics_data['events'] = array(r34ics_date('Ymd') => array());
					}
					
				}
	
				// Sort events and split into year/month/day groups
				ksort($ics_data['events']); // Get all dates in the right order first!
				foreach ((array)$ics_data['events'] as $date => $events) {
				
					// Only reorganize dates that are in the proper date range
					if ($date >= $first_date && $date <= $limit_date) {
					
						// Fix recurrence exceptions
						if (empty($skiprecurrence)) {
							$events = $this->_date_events_fix_recurrence_exceptions($events);
						}
						
						// Get the date's events in order
						$events = $this->_date_events_sort($events, $view);
						
						// Insert the date's events into the year/month/day hierarchical array
						$year = substr($date,0,4);
						$month = substr($date,4,2);
						$day = substr($date,6,2);
						$ym = substr($date,0,6);
						$ics_data['events'][$year][$month][$day] = $events;
						
					}
					
					// Remove the old flat date item from the array
					unset($ics_data['events'][$date]);
				}
				
				// Add empty event arrays, if necessary, to populate dropdowns and grids
				/**
				 * Note: This prevents a simple check for whether or not there are events;
				 * r34ics_is_empty_array() function was added to accommodate this situation
				 * but it is only being used in list view since we do want the grid to
				 * display in the other views, even if empty.
				 */
				for ($i = substr(intval($ics_data['earliest']),0,6); $i <= substr(intval($ics_data['latest']),0,6); $i++) {
					$Y = substr($i,0,4);
					$m = substr($i,4,2);
					if (intval($m) < 1 || intval($m) > 12) { continue; }
					if (!isset($ics_data['events'][$Y][$m])) {
						$ics_data['events'][$Y][$m] = null;
					}
				}
				// Now sort these inserted empty arrays, or it may not work (e.g. if the first actual event is next year)
				foreach (array_keys((array)$ics_data['events']) as $key_year) { ksort($ics_data['events'][$key_year]); }
				ksort($ics_data['events']);
		
				// Write ICS data to transient
				if ($reload != 1) {
					// Allow $reload as an integer > 1 to represent the cache expiration in seconds; otherwise use the default
					$transient_expiration = (intval($reload) > 1) ? intval($reload) : get_option('r34ics_transients_expiration');
					set_transient($transient_name, $ics_data, $transient_expiration);
				}
	
			}
	
			// Debugging
			if (!empty($this->debug)) {
				if (empty($ics_data)) {
					if (!empty($loaded_from_transient)) {
						$this->debug_messages['Errors'][] = 'Unavailable -- loaded from transient; set reload="true" in shortcode to force reload.';
					}
					else {
						$this->debug_messages['Errors'][] = 'Unable to parse ICS data.';
					}
				}
				$current_memory_usage = memory_get_usage();
				if (!isset($this->debug_messages['Peak memory usage']) || $current_memory_usage > $this->debug_messages['Peak memory usage']) {
					$this->debug_messages['Peak memory usage'] = memory_get_usage();
					$this->debug_messages['Peak memory usage'] = size_format($this->debug_messages['Peak memory usage'], 2);
				}
				if ($this->debug >= 2) {
					$this->debug_messages['Plugin Data'] = $ics_data;
				}
				_r34ics_debug($this->debug_messages);
			}
			
			// Remove events with redundant UIDs on the same date
			if (!empty($fixredundantuids)) {
				add_filter('r34ics_display_calendar_filter_ics_data', 'r34ics_scrub_duplicate_uids', 10, 2);
			}
			
			// Allow external customization of ICS data
			$ics_data = apply_filters('r34ics_display_calendar_filter_ics_data', $ics_data, $args);
			
			// If we are only returning an array instead of rendering the output, do that now
			if (!empty($arrayonly)) {
				return $ics_data;
			}
			
			// Actions before rendering template (can include additional template output)
			do_action('r34ics_display_calendar_before_render_template', $view, $args, $ics_data);
	
			// Render template
			switch ($view) {
				case 'basic':
				case 'list':
				case 'month':
				case 'week':
					include(plugin_dir_path(__FILE__) . 'templates/calendar-' . $view . '.php');
					break;
				default:
					// Handle other views externally
					do_action('r34ics_display_calendar_render_template', $view, $args, $ics_data);
			}
			
			// Actions after rendering template (can include additional template output)
			do_action('r34ics_display_calendar_after_render_template', $view, $args, $ics_data);
	
		}
		
		
		// Callback method for display_calendar(); set basic info parameters
		public function display_calendar_ics_data_init($args) {
			$ics_data = array();
			
			// Convert URL into array and iterate
			$ics_data['events'] = array();
			$ics_data['urls'] = r34ics_space_pipe_explode($args['url'] ?? '');
			$ics_data['tz'] = !empty($args['tz']) ? r34ics_space_pipe_explode($args['tz']) : get_option('timezone_string');
			
			// Set general calendar information
			$ics_data['view'] = $args['view'] ?? 'month';
			$ics_data['guid'] = !empty(trim($args['guid'] ?? '')) ? $args['guid'] : r34ics_uid();
			$ics_data['title'] = (isset($args['title']) && r34ics_boolean_check($args['title'])) ? $args['title'] : '';
			$ics_data['description'] = (isset($args['description']) && r34ics_boolean_check($args['description'])) ? $args['description'] : '';
	
			// Set colors and feed titles for color key
			$ics_data['colors'] = apply_filters('r34ics_display_calendar_color_set', (!empty($args['color']) ? r34ics_color_set(r34ics_space_pipe_explode($args['color']), 1, (empty($args['whitetext']) && empty($args['solidcolors']))) : ''), $args);
			$ics_data['tablebg'] = $args['tablebg'] ?? '';
			// @todo Add category support
			$ics_data['feed_titles'] = !empty($args['feedlabel']) ? explode('|', $args['feedlabel']) : array();
			
			// Allow external modification of data array
			$ics_data = apply_filters('r34ics_display_calendar_ics_data_init', $ics_data, $args);
			
			return $ics_data;
		}
		
		
		// Callback method for display_calendar(); determine rough date range for ICS Parser
		public function display_calendar_date_range($args) {
			$range = array();
	
			$range_unit = 32;
			if (strpos($args['view'], 'week') !== false) { $range_unit = 15; }
			if ((!empty($args['startdate']) && intval($args['startdate']) > 20000000)) {
				$range['start'] = r34ics_date('Y/m/d', $args['startdate'], null, '-' . intval($range_unit) . ' days');
				$range['end'] = r34ics_date('Y/m/d', $args['startdate'], null, '+' . intval($args['limitdays'] + $range_unit) . ' days');
			}
			else {
				if (!empty($args['pastdays'])) {
					$range['start'] = r34ics_date('Y/m/d', null, null, '-' . intval($args['pastdays'] + $range_unit) . ' days');
				}
				else {
					$range['start'] = r34ics_date('Y/m/d', null, null, '-' . intval(wp_date('j') + $range_unit) . ' days');
				}
				$range['end'] = r34ics_date('Y/m/d', null, null, '+' . intval($args['limitdays'] + $range_unit) . ' days');
			}
			// Additional filtering of range dates
			$range['start'] = apply_filters('r34ics_display_calendar_range_start', $range['start'], $args);
			$range['end'] = apply_filters('r34ics_display_calendar_range_end', $range['end'], $args);
	
			// Get day counts for ICS Parser's range filters
			$now_dtm = new DateTime();
			$range['days_before'] = $now_dtm->diff(new DateTime($range['start']))->format('%a');
			$range['days_after']= $now_dtm->diff(new DateTime($range['end']))->format('%a');
	
			return $range;
		}
		
		
		// Strip dynamic values from $args to generate hash for transient name
		// Prevents an explosion of separate transient records in the wp_options table
		protected function display_calendar_transient_hash($arr) {
			if (is_array($arr) && !empty($this->shortcode_dynamic_values)) {
				foreach ((array)$this->shortcode_dynamic_values as $key) {
					if (isset($arr[$key])) { unset($arr[$key]); }
				}
			}
			return sha1(serialize($arr));
		}
	
	
		public function editor_button() {
			// Add "Add Calendar" button to the editor
			add_action('media_buttons', function() {
				$current_screen = get_current_screen();
				$display_button = (isset($current_screen->parent_file) && strpos($current_screen->parent_file, 'edit.php') !== false);
				// Allow themes/plugins to disable the button with a filter
				$display_button = apply_filters('r34ics_display_add_calendar_button', $display_button);
				if (!empty($display_button)) {
					// Display button
					include(plugin_dir_path(__FILE__) . 'templates/admin/add-calendar-button.php');
				}
			}, 20);
	
			// Add modal for "Add Calendar"
			add_action('admin_print_footer_scripts', function() {
				$current_screen = get_current_screen();
				$display_button = (isset($current_screen->parent_file) && strpos($current_screen->parent_file, 'edit.php') !== false);
				// Allow themes/plugins to disable the button with a filter
				$display_button = apply_filters('r34ics_display_add_calendar_button', $display_button);
				if (!empty($display_button)) {
					// Include modal template output
					include_once(plugin_dir_path(__FILE__) . 'templates/admin/add-calendar.php');
				}
			}, 10);
		}
	
	
		public function enqueue_scripts() {
			if ($this->scripts_enqueued || empty($this->scripts_registered)) { return; }
		
			// ICS Calendar JS
			wp_enqueue_script('ics-calendar');
			
			// Add inline scripts (dynamic values)
			wp_add_inline_script('ics-calendar', 'var r34ics_ajax_obj = ' . wp_json_encode(array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'r34ics_nonce' => wp_create_nonce('r34ics_nonce'),
			)) . ';');
			wp_add_inline_script('ics-calendar', 'var ics_calendar_i18n = ' . wp_json_encode(array(
				'hide_past_events' => esc_html__('Hide past events', 'ics-calendar'),
				'show_past_events' => esc_html__('Show past events', 'ics-calendar'),
			)) . ';');
			wp_add_inline_script('ics-calendar', 'var r34ics_days_of_week_map = ' . wp_json_encode((array)$this->days_of_week_map()) . ';');
			// Transients expiration is used for AJAX refresh interval, but must be at least 5 minutes
			$transients_expiration = get_option('r34ics_transients_expiration', 3600);
			if ($transients_expiration < 300) { $transients_expiration = 300; }
			wp_add_inline_script('ics-calendar', 'var r34ics_transients_expiration_ms = ' . (intval($transients_expiration) * 1000) . '; var r34ics_ajax_interval;');
	
			// ICS Calendar CSS
			wp_enqueue_style('ics-calendar');
			if (current_user_can('manage_options')) {
				wp_enqueue_style('ics-calendar-debug');
			}
	
			// Add external scripts
			do_action('r34ics_enqueue_external_scripts');
	
			// Set enqueued indicator
			$this->scripts_enqueued = true;
		}
		
		
		public function event_description_html($args, $event, $classes=array(), $has_desc=null) {
			if (empty($args) || empty($event) || (empty($has_desc) && empty($args['eventdl']))) { return ''; }
			ob_start();
	
			// Attachment(s)
			$attachment_is_image = false;
			$show_attachment = false;
			if (!empty($event['attach'])) {
				// Check if attachment is an image URL (for direct display)
				$attachment_is_image = (strpos($event['attach'],'<img') !== false);
				// Determine whether or not to show attachment based on shortcode argument
				switch ($args['attach']) {
					case 'image':
						if ($attachment_is_image) {
							$show_attachment = true;
						}
						break;
					case 'download':
						if (!$attachment_is_image) {
							$show_attachment = true;
						}
						break;
					case '1':
					case 'true':
						$show_attachment = true;
						break;
					case '0':
					case 'false':
						break;
					case '':
					default:
						if (!empty($args['eventdesc'])) {
							$show_attachment = true;
						}
						break;
				}
			}
			// Float attachment in list view
			if ($show_attachment && $attachment_is_image && in_array($args['view'], (array)$this->get_list_style_views())) {
				echo '<div class="attach attach_float">' . wp_kses_post(strip_tags($event['attach'],'<a><img>') ?: '') . '</div>';
			}
			
			// Location
			if (!empty($args['location']) && (!empty($event['location']) || !empty($event['geo']))) {
				if ($args['location'] === 'maplinks') {
					echo '<div class="location">' . wp_kses_post(r34ics_location_map_link($event['location'], ($event['geo'] ?? ''), ($args['mapsource'] ?? 'google')) ?: '') . '</div>';
				}
				elseif (!empty($event['location'])) {
					echo '<div class="location">' . wp_kses_post(r34ics_maybe_make_clickable($event['location']) ?: '') . '</div>';
				}
			}
			
			// Organizer
			if (!empty($args['organizer']) && (!empty($event['organizer']) || !empty($event['contact']))) {
				echo '<div class="organizer">';
				if (!empty($event['organizer'])) { echo '<div>' . wp_kses_post(r34ics_organizer_format($event['organizer']) ?: '') . '</div>'; }
				if (!empty($event['contact'])) { echo '<div>' . esc_html__('Contact:', 'ics-calendar') . ' ' . wp_kses_post(r34ics_maybe_make_clickable($event['contact']) ?: '') . '</div>'; }
				echo '</div>';
			}
			
			// Resources
			if (!empty($args['resources']) && (!empty($event['resources']))) {
				echo '<div class="resources">' . esc_html__('Resources:', 'ics-calendar') . ' ' . wp_kses_post(r34ics_maybe_make_clickable($event['resources']) ?: '') . '</div>';
			}
			
			// Description
			$eventdesc_content = '';
			if (!empty($args['eventdesc'])) {
				if (!empty($event['eventdesc'])) {
					if ($args['view'] != 'month' && $args['view'] != 'week' && intval($args['eventdesc']) > 1) {
						$eventdesc_content .=
							'<div class="descloc_toggle">' .
								'<div class="descloc_toggle_excerpt" aria-hidden="true" title="' . esc_attr__('Click for more...', 'ics-calendar') . '">' .
									r34ics_maybe_make_clickable(wp_trim_words($event['eventdesc'], intval($args['eventdesc']))) .
								'</div>' .
								'<div class="descloc_toggle_full">' . r34ics_filter_the_content(r34ics_maybe_make_clickable($event['eventdesc'])) . '</div>' .
							'</div>';
					}
					else {
						$eventdesc_content .=	r34ics_filter_the_content(r34ics_maybe_make_clickable($event['eventdesc']));
					}
				}
				// Add link if linktitles is false, nolink is false, there *is* a URL, and it's not already in the description itself
				if (empty($args['linktitles']) && empty($args['nolink']) && !empty($event['url']) && strpos(($event['eventdesc'] ?? ''), $event['url']) === false) {
					$eventdesc_content .=
						'<div class="eventdesc eventurl">' .
							'<a href="' . esc_url($event['url']) . '"' . r34ics_sametab_target($args['sametab'], $event['url']) . '>' . esc_url($event['url']) . '</a>' .
						'</div>';
				}
			}
			// Show attachment after description if not list view
			if ($show_attachment && (!$attachment_is_image || !in_array($args['view'], (array)$this->get_list_style_views()))) {
				if (strpos($event['attach'], '<img') !== false) {
					$eventdesc_content .=	'<figure class="attach">' . strip_tags($event['attach'],'<a><img><div>') . '</figure>';
				}
				else {
					$eventdesc_content .=	'<div class="attach">' . strip_tags($event['attach'],'<a><img><div>') . '</div>';
				}
			}
	
			// Append eventdesc if we have anything to show
			if (!r34ics_empty_content($eventdesc_content)) {
				echo '<div class="eventdesc">' . wp_kses_post($eventdesc_content ?: '') . '</div>';
			}
			// Add individual event .ics download link
			if (!empty($args['eventdl'])) {
				echo '<div><button class="r34ics_event_ics_download" data-eventdl-feed-key="' . intval($event['feed_key']) . '" data-eventdl-uid="' . esc_attr($event['uid']) . '" title="' . esc_attr__('Save to your calendar', 'ics-calendar') . '">' . esc_html__('Download', 'ics-calendar') . '</button></div>';
			}
			
			// Action for additional content
			do_action('r34ics_event_description_html', $args, $event, $classes, $has_desc);
			
			// Get buffered output
			$descloc_content = trim(ob_get_clean());
			
			// We check $has_desc to allow parameter to force output even if description itself is empty
			if (!empty($has_desc) || !empty($descloc_content)) {
				ob_start();
				$descloc_class = array_merge(array('descloc'), (array)$classes);
				echo '<' . esc_attr($args['htmltageventdesc']) . ' class="' . esc_attr(implode(' ', $descloc_class)) . '">';
				
				// Include feed label?
				if (
					!empty($args['feedlabelindesc']) &&
					!empty($args['feedlabel_array']) &&
					isset($event['feed_key']) &&
					!empty($args['feedlabel_array'][$event['feed_key']])
				) {
						echo '<div class="descloc_feed_label">' . esc_html($args['feedlabel_array'][$event['feed_key']]) . '</div>';
				}
	
				// Date(s) -- lightbox only, unless multi-day
				if ($args['toggle'] === 'lightbox' || (!empty($event['multiday']) && !in_array($args['view'], (array)$this->get_list_style_views()))) {
					$date_format = r34ics_date_format($args['format'], true);
					echo '<div class="date_in_hover_block">';
					if (!empty($event['multiday'])) {
						$day_label = r34ics_multiday_date_label($date_format, $event, $args);
					}
					else {
						$day_label = r34ics_date($date_format, $event['dtstart_date'], $event['tz_start']);
					}
					echo wp_kses_post($day_label ?: '');
					echo '</div>';
				}
				
				// Should we include the event time and title in the description block?
				$eventdesc_include_time_and_title = (
					in_array('hover_block', $descloc_class) || // It's a hover block
					$args['toggle'] === 'lightbox' || // It's a lightbox
					(!empty($event['multiday']) && !in_array($args['view'], (array)$this->get_list_style_views())) // It's multiday, and not list view
				);
				
				// Filter for additional custom conditions for time and title in description block
				$eventdesc_include_time_and_title = apply_filters('r34ics_eventdesc_include_time_and_title', $eventdesc_include_time_and_title, $args, $event);
				
				// Add this content in hover block or lightbox only, not toggle
				if (!empty($eventdesc_include_time_and_title)) {
	
					// Time(s)
					if (!empty($event['start'])) {
						echo '<div class="time_in_hover_block">' . wp_kses_post($event['start'] ?: '');
						if (!empty($event['end'])) { echo ' &#8211; ' . wp_kses_post($event['end'] ?: ''); }
						echo '</div>';
					}
	
					// Event title
					echo '<div class="title_in_hover_block">' . wp_kses_post($this->event_label_html($args, $event, null) ?: '') . '</div>';
	
				}
	
				// Add recurrence information
				if (empty($args['skiprecurrence']) && !empty($event['rrule'])) {
					echo wp_kses_post(r34ics_recurrence_description($event['rrule'], $args['hiderecurrence']) ?: '');
				}
	
				// Concatenate output and close div
				$descloc_content = ob_get_clean() . $descloc_content . '</' . esc_attr($args['htmltageventdesc']) . '>';
			}
					
			// Filter content
			$descloc_content = apply_filters('r34ics_event_description_html_filter', $descloc_content, $args, $event, $classes, $has_desc);
			
			// Return content
			return !r34ics_empty_content($descloc_content) ? $descloc_content : '';
		}
		
		
		public function event_label_html($args, $event, $classes=array()) {
			if (empty($args) || empty($event)) { return ''; }
			ob_start();
	
			// Set CSS classes
			if (!empty($event['status'])) { $classes[] = strtolower($event['status']); }
			$title_class = array_merge(array('title'), (array)$classes);
	
			// Build event label HTML
			echo '<' . esc_attr($args['htmltageventtitle']) . ' tabindex="0" aria-haspopup="true" class="' . esc_attr(implode(' ', $title_class)) . '">';
			if (!empty($args['linktitles']) && empty($args['nolink']) && !empty($event['url'])) {
				echo '<a href="' . esc_url($event['url']) . '" ' . wp_kses_post(r34ics_sametab_target($args['sametab'], $event['url']) ?: '') . '>';
			}
			echo wp_kses_post(html_entity_decode(str_replace('/', '/<wbr />', $event['label'])) ?: '');
			if (!empty($args['linktitles']) && empty($args['nolink']) && !empty($event['url'])) {
				echo '</a>';
			}
			do_action('r34ics_event_label_html', $args, $event, $classes);
			echo '</' . esc_attr($args['htmltageventtitle']) . '>';
	
			// Append cancelled status
			if (!empty($event['status']) && $event['status'] == 'CANCELLED') {
				echo '<span class="event_status event_status_cancelled">' . esc_html__('Cancelled', 'ics-calendar') . '</span>';
			}
	
			// Get buffered output
			$title_content = ob_get_clean();
	
			// Filter content
			$title_content = apply_filters('r34ics_event_label_html_filter', $title_content, $args, $event, $classes);
	
			// Return content
			return !r34ics_empty_content($title_content) ? $title_content : '';
		}
		
		
		public function event_sublabel_html($args, $event, $classes=array()) {
			if (empty($args) || empty($event) || empty($event['sublabel'])) { return ''; }
			ob_start();
	
			// Set CSS classes
			$sublabel_class = array_merge(array('sublabel'), (array)$classes);
	
			// Build event sub-label HTML
			echo '<span class="' . esc_attr(implode(' ', $sublabel_class)) . '">';
			if (empty($event['start']) && !empty($event['end'])) {
				echo '<span class="carryover">&#10554;</span>';
			}
			echo wp_kses_post(str_replace('/', '/<wbr />', $event['sublabel']) ?: '');
			do_action('r34ics_event_sublabel_html', $args, $event, $classes);
			echo '</span>';
	
			// Get buffered output
			$sublabel_content = ob_get_clean();
	
			// Filter content
			$sublabel_content = apply_filters('r34ics_event_sublabel_html_filter', $sublabel_content, $args, $event, $classes);
	
			// Return content
			return !r34ics_empty_content($sublabel_content) ? $sublabel_content : '';
		}
	
	
		public function first_dow($date=null) {
			return r34ics_date('w', r34ics_date('Ym', $date) . '01');
		}
	
	
		public function get_days_of_week($format=null) {
			$days_of_week = $this->days_of_week($format);
	
			// Shift sequence of days based on site configuration
			$start_of_week = get_option('start_of_week', 0);
			for ($i = 0; $i < $start_of_week; $i++) {
				$day = $days_of_week[$i];
				unset($days_of_week[$i]);
				$days_of_week[$i] = $day;
			}
	
			return $days_of_week;
		}
		
		
		public function get_list_style_views() {
			return $this->list_style_views;
		}
		
		
		public function http_request_host_is_external($external, $host, $url) {
			if ($allowed_hosts = get_option('r34ics_allowed_hosts')) {
				if (in_array($host, (array)$allowed_hosts)) { $external = true; }
			}
			return $external;
		}
		
		
		// Callback method for display_calendar(); run URL contents through parser
		public function ical_parse_url($url, $url_tz_name='UTC', $args=array(), $range=array()) {
			// Retrieve ICS file
			$ics_contents = $this->_url_get_contents(
				$url,
				false,
				(empty($args['reload']) || intval($args['reload']) >= intval(get_option('r34ics_transients_expiration'))),
				($args['basicauth'] ?: ''),
				!empty($args['skipdomainerrors']),
				($args['ua'] ?: '')
			);
			
			// No ICS data present -- bail out now
			if (empty($ics_contents)) { return false; }
							
			// Fix issue with hard line breaks inside DESCRIPTION fields (not included in the documentation because problem needs further research)
			if (!empty($args['linebreakfix'])) {
				$ics_contents = r34ics_line_break_fix($ics_contents);
			}
			
			// Strip embedded images from descriptions (they can blow up the array size and probably won't render properly anyway)
			add_filter('r34ics_display_calendar_preprocess_raw_feed', 'r34ics_raw_feed_strip_embedded_images', 10, 4);
			
			// Strip invalid "DTEND:None" lines from feed (issue with feeds from easyVerein)
			add_filter('r34ics_display_calendar_preprocess_raw_feed', 'r34ics_raw_feed_strip_dtend_none', 10, 4);
			
			// Filter to allow external pre-processing of raw feed contents before parsing
			$ics_contents = apply_filters('r34ics_display_calendar_preprocess_raw_feed', $ics_contents, $range['start'], $range['end'], $args);
	
			// Initialize parser
			if (!$this->parser_loaded) { $this->_load_parser(!empty($args['legacyparser'])); }
			$ICal = new R34ICS2_ICal\ICal('ICal.ics', array(
				'defaultSpan' => intval(ceil(($args['limitdays'] ?? 365) / 365)),
				'defaultTimeZone' => $url_tz_name,
				'disableCharacterReplacement' => true,
				'filterDaysAfter' => $range['days_after'],
				'filterDaysBefore' => $range['days_before'],
				'skipRecurrence' => $args['skiprecurrence'],
			));
			$ICal->initString($ics_contents);
	
			// Return prepared $ICal object
			return $ICal;
		}
	
	
		public function parse_attach_array($attach, $sametab=null) {
			if (empty($attach) || !is_array($attach) || count($attach) % 2 !== 0) { return ''; }
			
			$output = '';
			// Each attachment has two nodes in the array: $n is array, $n+1 is URL string
			for ($n = 0; $n < count($attach); $n = $n+2) {
				// Determine file/URL properties
				$url = $attach[$n+1];
				$mime = isset($attach[$n]['FMTTYPE']) ? $attach[$n]['FMTTYPE'] : null;
				$filename = isset($attach[$n]['FILENAME']) ? $attach[$n]['FILENAME'] : pathinfo($url,PATHINFO_BASENAME);
				$ext = pathinfo($filename,PATHINFO_EXTENSION);
				$clean_filename = sanitize_title(pathinfo($filename,PATHINFO_FILENAME)) . '.' . $ext;
			
				// Validate URL (some feeds may contain local/network file paths instead of properly formed URLs)
				if (!filter_var($url, FILTER_VALIDATE_URL)) {
					continue;
				}
			
				// Google Drive image links have an image MIME type, but require login and don't load the image directly, so we treat them as links
				if (strpos(($url ?? ''), 'https://drive.google.com/') === 0) {
					$output .= '<div><a href="' . esc_url($url) . '"' . r34ics_sametab_target($sametab, $url) . '>' . $filename . '</a></div>';
				}
				
				// Handle images as an image tag (MIME type MUST be passed or this may not actually be a direct image link (e.g. a Google Drive preview link)
				elseif (!empty($mime) && strpos($mime, 'image/') === 0) { 
					$output .= '<div><img src="' . esc_url($url) . '" alt="" style="position: relative; height: auto; width: 100%;" /></div>';
				}
			
				// Handle other files with a MIME type set, and not equal to a text format, as downloads
				elseif (!empty($mime) && strpos($mime, 'text/') === false) {
					$output .= '<div><a href="' . esc_url($url) . '" download="' . rawurlencode($filename) . '" rel="noopener noreferrer nofollow">' . $filename . '</a></div>';
				}
			
				// Handle others (no MIME type, or a "text/" mime type) as clickable links
				else {
					$output .= '<div><a href="' . esc_url($url) . '"' . r34ics_sametab_target($sametab, $url) . '>' . $filename . '</a></div>';
				}
			}
			
			return $output;
		}
		
		
		public function query_vars($qvars) {
			$qvars[] = 'r34ics-feed-key';
			$qvars[] = 'r34ics-urlids';
			$qvars[] = 'r34ics-uid';
			return $qvars;
		}
		
		
		public function r34ics_calendar_classes($ics_calendar_classes=null, $args=array(), $implode=false) {
			
			// Prepare class array
			if (empty($ics_calendar_classes)) { $ics_calendar_classes = array(); }
			elseif (!is_array($ics_calendar_classes)) { $ics_calendar_classes = explode(' ', $ics_calendar_classes); }
		
			// Required CSS classes
			$ics_calendar_classes[] = 'ics-calendar';
			$ics_calendar_classes[] = 'layout-' . $args['view'];
			
			// Conditional CSS classes
			if (!empty($args['combinemultiday'])) { $ics_calendar_classes[] = 'combinemultiday'; }
			if (!empty($args['compact'])) {
				switch ($args['compact']) {
					case 'mobile': $ics_calendar_classes[] = 'r34ics_compact_mobile'; break;
					case 'desktop': $ics_calendar_classes[] = 'r34ics_compact_desktop'; break;
					default: $ics_calendar_classes[] = 'r34ics_compact'; break;
				}
			}
			if (!empty($args['hidetimes'])) { $ics_calendar_classes[] = 'hide_times'; }
			if (!empty($args['monthnav'])) { $ics_calendar_classes[] = 'monthnav-' . esc_attr($args['monthnav']); }
			if (!empty($args['nomobile'])) { $ics_calendar_classes[] = 'nomobile'; }
			if (!empty($args['reverse'])) { $ics_calendar_classes[] = 'reverse'; }
			if ($args['sametab'] == 'all') { $ics_calendar_classes[] = 'sametab'; }
			elseif ($args['sametab'] == 'none') { $ics_calendar_classes[] = 'newtab'; }
			if (!empty($args['solidcolors'])) { $ics_calendar_classes[] = 'solidcolors'; }
			if (!empty($args['stickymonths'])) { $ics_calendar_classes[] = 'stickymonths'; }
			if (!empty($args['toggle'])) {
				$ics_calendar_classes[] = ($args['toggle'] === 'lightbox') ? 'r34ics_toggle r34ics_toggle_lightbox' : 'r34ics_toggle';
			}
			if (!empty($args['url']) && (strpos($args['url'], ' ') !== false || strpos($args['url'], '|') !== false)) { $ics_calendar_classes[] = 'multi-feed'; }
			if (!empty($args['whitetext'])) { $ics_calendar_classes[] = 'whitetext'; }
			
			// View-specific classes
			if (!empty($args['view']) && $args['view'] == 'week') { $ics_calendar_classes[] = 'current_week_only'; }
			
			// Return the CSS classes as a string or an array
			if (!empty($implode)) {
				return implode(' ', $ics_calendar_classes);
			}
			return $ics_calendar_classes;
	
		}
		
		
		public function r34ics_display_add_calendar_button($display_button) {
			if (!empty(get_option('r34ics_display_add_calendar_button_false'))) {
				$display_button = false;
			}
			return $display_button;
		}
		
	
		public function r34ics_display_calendar_after_render_template($view, $args, $ics_data) {
			return;
		}
		
		
		public function r34ics_display_calendar_after_wrapper($view, $args, $ics_data) {
			if (!empty($args['eventdl'])) {
				?>
				</form>
				<?php
			}
			return;
		}
	
	
		public function r34ics_display_calendar_before_render_template($view, $args, $ics_data) {
			return;
		}
		
		
		public function r34ics_display_calendar_before_wrapper($view, $args, $ics_data) {
			if (!empty($args['eventdl'])) {
				?>
				<form method="get" class="r34ics_event_ics_download_form">
				<input type="hidden" name="r34ics-feed-key" value="" />
				<input type="hidden" name="r34ics-uid" value="" />
				<?php
				foreach ((array)$ics_data['urls'] as $feed_key => $feed_url) {
					?>
					<input type="hidden" name="r34ics-urlids[<?php echo esc_attr($feed_key); ?>]" value="<?php echo esc_attr(r34ics_url_uniqid($feed_url)); ?>" />
					<?php
				}
			}
			return;
		}
	
	
		public function r34ics_display_calendar_render_template($view, $args, $ics_data) {
			return;
		}
		
		
		public function r34ics_display_calendar_exclude_event($exclude, $event, $args) {
		
			// For the purposes of this function we let Microsoft's vendor field override the default status...
			// Microsoft includes STATUS:CONFIRMED and X-MICROSOFT-CDO-BUSYSTATUS:TENTATIVE in the same event!
			if (!empty($event->additionalProperties['x_microsoft_cdo_busystatus'])) {
				$event->status = $event->additionalProperties['x_microsoft_cdo_busystatus'];
			}
			
			// Are we filtering the feed by category?
			if (!empty($args['category'])) {
				if (empty($event->categories)) { $exclude = true; }
				else {
					// Prepare category argument array for case-insensitive comparison
					$categories = array_map('r34ics_comparison_string', (array)(explode('|', $args['category'])));
					// Prepare the event's categories for case-insensitive comparison
					$event_categories = array_map('r34ics_comparison_string', (array)(explode(',', $event->categories)));
					// Check for a match
					$match = false;
					foreach ((array)$categories as $category) {
						if (in_array($category, (array)$event_categories)) {
							$match = true; break;
						}
					}
					// If we didn't find a match, exclude the event
					if (empty($match)) { $exclude = true; }
				}
			}
	
			// Don't just set $exclude equal to these expressions; it might evaluate false but true might have been passed in!
			// Are we hiding private events?
			if (!empty($args['hideprivateevents']) && ($event->class == 'PRIVATE' || $event->class == 'CONFIDENTIAL')) {
				$exclude = true;
			}
			// Are we hiding cancelled (or canceled, in case any sources spell it that way) events?
			if (!empty($args['hidecancelledevents']) && ($event->status == 'CANCELLED' || $event->status == 'CANCELED')) {
				$exclude = true;
			}
			// Are we hiding tentative events?
			if (!empty($args['hidetentativeevents']) && ($event->status == 'TENTATIVE')) {
				$exclude = true;
			}
			
			// Is this a recurring event exclusion?
			// @todo Figure out why ics-parser is (at least sometimes) missing this (is it DATE used with date/time events?)
			if (!empty($event->additionalProperties['rrule']) && !empty($event->additionalProperties['exdate_array'][0]['VALUE'])) {
				if (
						($event->additionalProperties['exdate_array'][0]['VALUE'] == 'DATE' && in_array(substr($event->dtstart,0,8), $event->additionalProperties['exdate_array'][1])) ||
						($event->additionalProperties['exdate_array'][0]['VALUE'] == 'DATE-TIME' && in_array($event->dtstart, $event->additionalProperties['exdate_array'][1]))
				) {
					$exclude = true;
				}
			}
				
			return $exclude;
		}
		
		
		public function register_scripts() {
			if ($this->scripts_registered) { return; }
		
			// ICS Calendar JS
			wp_register_script('ics-calendar', plugin_dir_url(__FILE__) . 'assets/script.min.js', array('jquery'), get_option('r34ics_version'), true);
	
			// ICS Calendar CSS
			wp_register_style('ics-calendar', plugin_dir_url(__FILE__) . 'assets/style.min.css', false, get_option('r34ics_version'));
			if (current_user_can('manage_options')) {
				wp_register_style('ics-calendar-debug', plugin_dir_url(__FILE__) . 'assets/debug.css', false, get_option('r34ics_version'));
			}
	
			// Allow additional attributes on inline styles to resolve potential issue with wp_kses() functions
			add_filter('safe_style_css', array(&$this, 'safe_style_css'), 10, 1);
	
			// Add external scripts
			do_action('r34ics_register_external_scripts');
	
			// Set enqueued indicator
			$this->scripts_registered = true;
		}
	
		
		// See: https://wordpress.org/support/topic/problem-with-wp_kses/#post-15293378
		public function safe_style_css($styles) {
			$styles[] = 'display';
			$styles[] = 'opacity';
			return $styles;
		}
	
	
		public function shortcode($atts) {
				
			// Don't do anything in Block Editor
			if (function_exists('get_current_screen') && $current_screen = get_current_screen()) {
				if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
					return;
				}
			}
			
			// Enqueue ICS Calendar's scripts and styles, if needed (method includes check to prevent duplicate enqueuing)
			$this->enqueue_scripts();
	
			// Merge new defaults
			$defaults = $this->shortcode_defaults_merge($atts);
			
			// Workaround for shortcodes that contain HTML in the url attribute
			if (empty($atts['url']) || strpos($atts['url'], '<') !== false) { $atts['url'] = r34ics_shortcode_url_fix($atts); }
			
			// Trim extraneous spaces from all attributes
			foreach ((array)$atts as $key => $value) {
				$atts[$key] = trim($value);
			}
			
			// Extract attributes
			extract(shortcode_atts($defaults, $atts, 'ics_calendar'));
			
			// Begin output buffering
			ob_start();
			
			// Report deprecated attributes
			if (!empty($currentweek) && $this->debug) {
				trigger_error(esc_html__('The "currentweek" shortcode attribute is deprecated. Please use view="week" in your shortcode instead.', 'ics-calendar'), E_USER_DEPRECATED);
			}
			if (!empty($legendinline) && $this->debug) {
				trigger_error(esc_html__('The "legendinline" shortcode attribute is deprecated. Please use legendstyle="inline" in your shortcode instead.', 'ics-calendar'), E_USER_DEPRECATED);
			}
			
			// Assemble display arguments array
			$args = array(
				'ajax' => (get_option('r34ics_ajax_by_default') ?: r34ics_boolean_check($ajax)),
				'arrayonly' => r34ics_boolean_check($arrayonly),
				'attach' => (
					in_array(strtolower($attach), array('0','false','1','true','image','download'))
						? strtolower($attach)
						: ''
					),
				'basicauth' => r34ics_boolean_check($basicauth),
				'category' => $category,
				'color' => $color,
				'columnlabels' => (
					in_array(strtolower($columnlabels), array('full','short','min'))
						? strtolower($columnlabels)
						: (r34ics_boolean_check($nomobile) ? 'short' : '')
					),
				'combinemultiday' => r34ics_boolean_check($combinemultiday),
				'compact' => (
					in_array(strtolower($compact), array('0','false','1','true','mobile', 'desktop'))
						? strtolower($compact)
						: ''
					),
				'count' => abs(intval($count)),
				'curlopts' => false, // Deprecated
				'customoptions' => explode('|', $customoptions),
				'debug' => (
					intval($debug)
						? intval($debug)
						: r34ics_boolean_check($debug)
				),
				'description' => $description,
				'eventdesc' => (
					intval($eventdesc)
						? intval($eventdesc)
						: r34ics_boolean_check($eventdesc)
					),
				// eventdl doesn't support feeds using basic auth
				'eventdl' => (r34ics_boolean_check($eventdl) && !r34ics_boolean_check($basicauth)),
				'eventlocaltime' => r34ics_boolean_check($eventlocaltime),
				'extendmultiday' => (
					in_array(strtolower($extendmultiday), array('both', 'overnight', 'allday'))
						? strtolower($extendmultiday)
						: (r34ics_boolean_check($extendmultiday) ? 'allday' : '')
					),
				'feedlabel' => $feedlabel,
				'feedlabelindesc' => $feedlabelindesc,
				'fixredundantuids' => (
					intval($fixredundantuids)
						? intval($fixredundantuids)
						: r34ics_boolean_check($fixredundantuids)
					),
				'format' => $format,
				'formatmonthyear' => preg_replace('/[^FMmnYy\/\.\-\s]+/', '', ($formatmonthyear ?? '')),
				'fulldateintable' => r34ics_boolean_check($fulldateintable),
				'guid' => (
					!empty($guid)
						? sanitize_title($guid)
						: r34ics_uid()
				),
				'guid_static' => $guid,
				'htmltagdate' => r34ics_allowed_heading_tags_check($htmltagdate, 'h4'),
				'htmltageventdesc' => r34ics_allowed_heading_tags_check($htmltageventdesc, 'div'),
				'htmltageventtitle' => r34ics_allowed_heading_tags_check($htmltageventtitle, 'span'),
				'htmltagmonth' => r34ics_allowed_heading_tags_check($htmltagmonth, 'h3'),
				'htmltagtime' => r34ics_allowed_heading_tags_check($htmltagtime, 'span'),
				'htmltagtitle' => r34ics_allowed_heading_tags_check($htmltagtitle, 'h2'),
				'hidealldayindicator' => r34ics_boolean_check($hidealldayindicator),
				'hidecancelledevents' => r34ics_boolean_check($hidecancelledevents),
				'hideprivateevents' => r34ics_boolean_check($hideprivateevents),
				'hidetentativeevents' => r34ics_boolean_check($hidetentativeevents),
				'hiderecurrence' => r34ics_hiderecurrence_parse($hiderecurrence),
				'hidetimes' => r34ics_boolean_check($hidetimes),
				'legacyparser' => r34ics_boolean_check($legacyparser),
				'legendinline' => false, // Deprecated
				'legendposition' => (
					in_array(strtolower($legendposition), array('above','below'))
						? strtolower($legendposition)
						: null
					),
				'legendstyle' => (
					in_array(strtolower($legendstyle), array('block','inline','none'))
						? strtolower($legendstyle)
						: (r34ics_boolean_check($legendinline) ? 'inline' : null)
					),
				'limitdays' => (
					intval($limitdays) > 0
						? intval($limitdays)
						: $this->limit_days
				),
				'limitdayscustom' => isset($limitdays),
				'linebreakfix' => r34ics_boolean_check($linebreakfix),
				'linktitles' => r34ics_boolean_check($linktitles),
				'location' => (
					in_array(strtolower($location), array('maplinks'))
						? strtolower($location)
						: r34ics_boolean_check($location)
					),
				'mapsource' => (
					in_array(strtolower($mapsource), array('google', 'bing', 'openstreetmap'))
						? strtolower($mapsource)
						: 'google'
				),
				'maskinfo' => $maskinfo,
				'method' => false, // Deprecated
				'monthnav' => (
					in_array(strtolower($monthnav), array('arrows','select','both','compact','none'))
						? strtolower($monthnav)
						: null
					),
				'nolink' => r34ics_boolean_check($nolink),
				'nomobile' => r34ics_boolean_check($nomobile),
				'nomonthheaders' => r34ics_boolean_check($nomonthheaders),
				'nostyle' => r34ics_boolean_check($nostyle),
				'organizer' => r34ics_boolean_check($organizer),
				'pagination' => (
					strtolower($pagination) == 'true'
						? 5 // Default to 5 if input value is literally the text string 'true'
						: abs(intval($pagination))
					),
				'paginationposition' => (
					in_array(strtolower($paginationposition), array('above','below','both'))
						? strtolower($paginationposition)
						: (r34ics_boolean_check($pagination) ? 'above' : null)
					),
				'pastdays' => intval($pastdays),
				'reload' => (
					intval($reload) > 1
						? intval($reload)
						: intval(r34ics_boolean_check($reload))
					),
				'resources' => r34ics_boolean_check($resources),
				'reverse' => (in_array(strtolower($view), (array)$this->get_list_style_views()) && r34ics_boolean_check($reverse)),
				'sametab' => (
					in_array(strtolower($sametab), array('local','all','none'))
						? strtolower($sametab)
						: (r34ics_boolean_check($sametab) ? 'all' : 'local')
					),
				'showendtimes' => r34ics_boolean_check($showendtimes),
				'skip' => intval($skip),
				'skipdomainerrors' => r34ics_boolean_check($skipdomainerrors),
				'skiprecurrence' => r34ics_boolean_check($skiprecurrence),
				'solidcolors' => r34ics_boolean_check($solidcolors),
				'startdate' => (
					$startdate == 'today'
					|| (empty($startdate) && strtolower($view) == 'week' && $limitdays > 7)
						? r34ics_date('Ymd', null, null, '-' . intval($pastdays) . ' days')
						: intval($startdate)
					),
				'startdate_static' => $startdate,
				'stickymonths' => r34ics_boolean_check($stickymonths),
				'tablebg' => r34ics_color_hex_sanitize($tablebg),
				'timeformat' => (
					!empty($timeformat)
						? $timeformat
						: get_option('time_format')
				),
				'title' => $title,
				'toggle' => (
					in_array(strtolower($toggle), array('lightbox'))
						? strtolower($toggle)
						: r34ics_boolean_check($toggle)
					),
				'tz' => $tz,
				'ua' => (
					(!empty($ua) && strlen($ua) > 5)
						? trim(wp_strip_all_tags($ua))
						: r34ics_boolean_check($ua)
				),
				'url' => $url,
				'view' => (in_array(strtolower($view), (array)$this->views) ? strtolower($view) : 'month'),
				'weeknumbers' => r34ics_boolean_check($weeknumbers),
				'whitetext' => r34ics_boolean_check($whitetext),
			);
			
			// Workaround: Adjust limitdays to compensate for pastdays when startdate is current date
			// @todo This is due to the 10.6 change to 'pastdays' default; work out a permanent fix!
			if (get_option('r34ics_use_new_defaults_10_6')) {
				if ((empty($startdate) || strtolower($startdate) == 'today') && intval($pastdays) > intval($limitdays)) {
					$args['limitdays'] = intval($limitdays) + intval($pastdays);
				}
			}
			
			// Allow extension of feed array parameters
			$this->shortcode_feed_array_params = apply_filters('r34ics_shortcode_feed_array_params', $this->shortcode_feed_array_params);
			
			// Apply external filters to $args array
			// IMPORTANT: Any conditionals after this point need to use $args rather than the extracted variables!
			$args = apply_filters('r34ics_display_calendar_args', $args, $atts);
			
			// Explode feed array parameters
			foreach ((array)$this->shortcode_feed_array_params as $param) {
				if (!empty($args[$param]) && strpos($args[$param], '|') !== false) {
					$args[$param . '_array'] = array_map('trim', explode('|', $args[$param]));
				}
			}
			
			// Automatically append T to timeformat if eventlocaltime is true (must come after filters)
			if (!empty($args['eventlocaltime']) && strpos('T', $args['timeformat']) === false) {
				$args['timeformat'] .= ' T';
			}
			
			// Allow custom actions prior to generating the calendar output
			do_action('r34ics_shortcode_before_display_calendar', $args);
			
			// AJAX mode
			if (!empty($args['ajax'])) {
				$args['url'] = r34ics_url_uniqid_array_convert($args['url']);
				$is_list_style = intval(in_array($args['view'], $this->list_style_views));
				$is_list_long = intval($is_list_style && (!empty($args['eventdesc']) || !empty($args['location']) || !empty($args['organizer']) || $args['count'] > 5));
				echo '<div class="r34ics-ajax-container loading" id="' . esc_attr($args['guid']) . '" data-view="' . esc_attr($args['view']) . '" data-view-is-list-style="' . esc_attr($is_list_style) . '" data-view-is-list-long="' . esc_attr($is_list_long) . '" data-args="' . esc_attr(wp_json_encode($args)) . '">&nbsp;</div>';
			}
	
			// Standard mode
			else {
				$this->display_calendar($args);
			}
			
			// Write buffer to output variable
			$output = ob_get_clean();
	
			// Allow custom actions after generating the calendar output
			do_action('r34ics_shortcode_after_display_calendar', $args, $output);
	
			// Return the shortcode output
			return $output;
		}
		
		
		public function shortcode_defaults_merge($atts=array()) {
			// Merge new defaults
			if (get_option('r34ics_use_new_defaults_10_6')) {
				$defaults = array_merge($this->shortcode_defaults, $this->shortcode_defaults_new_10_6);
			}
			else {
				$defaults = $this->shortcode_defaults;
			}
			
			// Filter the current calendar view
			// Note: Fallback value needs to be empty, not 'month'!
			$view = apply_filters('r34ics_calendar_view', (@$atts['view'] ?: ''), $atts);
			
			// Collapse array defaults by view
			foreach ((array)$defaults as $key => $value) {
				if (is_array($value)) {
					if (isset($view) && isset($value[$view])) {
						$defaults[$key] = $value[$view];
					}
					elseif (isset($value['default'])) {
						$defaults[$key] = $value['default'];
					}
				}
			}
			
			// Filter the list
			$defaults = apply_filters('r34ics_shortcode_defaults_merge', $defaults);
			
			// Return array of merged defaults
			return $defaults;
		}
	
	
		public function template_redirect() {
		
			// Individual event ICS download
			if (get_query_var('r34ics-urlids') && get_query_var('r34ics-uid')) {
				$this->_event_ics_download();
			}
	
		}
		
		
		public function wp_settings() {
			// Transient timeout
			// v. 6.11.1 Renamed option from 'r34ics_transient_expiration' to 'r34ics_transient_expiration' so it's not a transient itself
			if (!get_option('r34ics_transients_expiration')) {
				$transients_expiration = get_option('r34ics_transient_expiration') ? get_option('r34ics_transient_expiration') : 3600;
				update_option('r34ics_transients_expiration', $transients_expiration);
				delete_option('r34ics_transient_expiration');
			}
		}
		
		
		protected function _admin_page_callback_utilities() {
			// Prepare deferred admin notices
			global $r34ics_deferred_admin_notices;
			if (empty($r34ics_deferred_admin_notices)) {
				$r34ics_deferred_admin_notices = get_option('r34ics_deferred_admin_notices', array());
			}

			if (isset($_POST['r34ics-purge-calendar-transients-nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['r34ics-purge-calendar-transients-nonce'])), 'r34ics')) {
				$cleared = r34ics_purge_calendar_transients();
				if ($cleared > 0) {
					$r34ics_deferred_admin_notices['r34ics_purge_transients'] = array(
						/* translators: 1: Plugin name (do not translate) */
						'content' => '<p>' . sprintf(esc_html__('Cleared %1$s %2$s transient(s).', 'ics-calendar'), intval($cleared), 'ICS Calendar') . '</p>',
						'status' => 'success',
						'dismissible' => false,
					);
				}
				else {
					$r34ics_deferred_admin_notices['r34ics_purge_transients'] = array(
						/* translators: 1: Plugin name (do not translate) */
						'content' => '<p>' . sprintf(esc_html__('No %1$s transients were found.', 'ics-calendar'), 'ICS Calendar') . '</p>',
						'status' => 'info',
						'dismissible' => false,
					);
				}
			}

			// Save deferred admin notices
			update_option('r34ics_deferred_admin_notices', $r34ics_deferred_admin_notices);
		}
		
		
		protected function _admin_page_callback_url_tester() {
			if (isset($_POST['r34ics-url-tester-nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['r34ics-url-tester-nonce'])), 'r34ics')) {
				$results = array('size' => '', 'status' => '', 'special' => array());
				if ($url_to_test = filter_input(INPUT_POST, 'url_to_test', FILTER_SANITIZE_URL)) {
					$results['url'] = $url_to_test;
					if ($response = $this->_url_get_contents($url_to_test)) {
						do_action('r34ics_url_tester_result_debug', $response);
						$results['size'] = size_format(strlen($response), 2);
						if (strpos($response,'BEGIN:VCALENDAR') === 0) {
							$results['status'] = 'valid';
						}
						else {
							$results['status'] = 'invalid';
						}
					}
					else {
						$results['status'] = 'failed';
					}
				}
				else {
					$results['status'] = 'unknown';
				}
				// Add special status messages
				if (in_array($results['status'], array('failed', 'unknown'))) {
					// URL contains '@' (could be basic auth credentials) and site uses legacy feed request method
					if (strpos($url_to_test, '@') !== false && get_option('r34ics_url_get_contents_legacy_method')) {
						/* translators: 1. Additional translation string and HTML tags 2. Additional translation string and HTML tags */
						$results['special'][] = sprintf(esc_html__('Your feed URL appears to contain HTTP Basic Authentication credentials. Try turning off the %1$s option under the %2$s tab above.', 'ics-calendar'), '<strong style="white-space: nowrap;">' . esc_html__('Use legacy feed request method', 'ics-calendar') . '</strong>', '<a href="#settings">' . esc_html__('Settings', 'ics-calendar') . '</a>');
					}
					// Suggest "Use legacy feed request method" option
					else {
						/* translators: 1. HTML tag 2. HTML tag 3. Additional translation string and HTML tags 4. Additional translation string and HTML tags */
						$results['special'][] = sprintf(esc_html__('If your feed URL works with our online %1$sPreview%2$s tool, try turning on the %3$s option under the %4$s tab above.', 'ics-calendar'), '<a href="https://icscalendar.com/preview/" target="_blank">', '</a>', '<strong style="white-space: nowrap;">' . esc_html__('Use legacy feed request method', 'ics-calendar') . '</strong>', '<a href="#settings">' . esc_html__('Settings', 'ics-calendar') . '</a>');
					}
				}
				// The feed URL appears to be a media library file
				$wp_upload_dir = wp_upload_dir();
				if ($results['status'] == 'valid' && !empty($wp_upload_dir['baseurl']) && strpos($url_to_test, $wp_upload_dir['baseurl']) !== false) {
					/* translators: 1. HTML tag 2. HTML tag 3: Plugin name (do not translate) 4. HTML tag 5. HTML tag */
					$results['special'][] = sprintf(esc_html__('%1$sThis URL points to a file in your Media Library.%2$s While you should be able to use this URL in an %3$s shortcode, it will be a static snapshot of the calendar at the time the file was created, and will not reflect any future updates to the source calendar. Please see our %4$sUser Guide%5$s for instructions on obtaining your correct dynamic feed URL.', 'ics-calendar'), '<strong style="color: crimson;">', '</strong>', 'ICS Calendar', '<a href="https://icscalendar.com/getting-started/#finding-your-ics-feed-url" target="_blank">', '</a>');
				}
				// Append the resolved IP address for the feed's domain
				if (!empty($results['url']) && $url_host = wp_parse_url($results['url'], PHP_URL_HOST)) {
					$url_resolved_ip = gethostbyname($url_host);
					if ($url_resolved_ip == $url_host) {
						/* translators: 1. Dynamic value and HTML tags */
						$results['special'][] = sprintf(esc_html__('Error: Could not resolve IP address for %1$s.', 'ics-calendar'), '<strong>' . esc_html($url_host) . '</strong>');
					}
					else {
						/* translators: 1. Dynamic value and HTML tags */
						$results['special'][] = sprintf(esc_html__('Resolved IP address for %1$s:', 'ics-calendar'), '<strong>' . esc_html($url_host) . '</strong>') . ' ' . esc_html($url_resolved_ip);
					}
				}
				return $results;
			}
		}
		
		
		protected function _admin_page_callback_save_settings() {
			if (current_user_can('manage_options') && isset($_POST['r34ics-settings-nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['r34ics-settings-nonce'])), 'r34ics')) {
				
				// Prepare deferred admin notices
				global $r34ics_deferred_admin_notices;
				if (empty($r34ics_deferred_admin_notices)) {
					$r34ics_deferred_admin_notices = get_option('r34ics_deferred_admin_notices', array());
				}
			
				// transients_expiration
				update_option('r34ics_transients_expiration', (
					isset($_POST['transients_expiration'])
						? intval(sanitize_text_field(wp_unslash($_POST['transients_expiration'])))
						: 3600
				));
				
				// allowed_hosts
				// Need to allow $_POST['allowed_hosts'] to be empty for erasing existing entries
				if (function_exists('r34ics_sanitize_hosts')) {
					update_option('r34ics_allowed_hosts', (
						isset($_POST['allowed_hosts'])
							? r34ics_sanitize_hosts(sanitize_textarea_field(wp_unslash($_POST['allowed_hosts'])))
							: ''
					));
				}
				
				// ajax_by_default
				update_option('r34ics_ajax_by_default', !empty($_POST['ajax_by_default']));
				
				// ajax_bypass_nonce
				update_option('r34ics_ajax_bypass_nonce', !empty($_POST['ajax_bypass_nonce']));
				
				// display_calendar_memory_limit
				update_option('r34ics_display_calendar_memory_limit', (
					isset($_POST['display_calendar_memory_limit'])
						? intval(sanitize_text_field(wp_unslash($_POST['display_calendar_memory_limit']))) . 'M'
						: ini_get('memory_limit')
				));
				
				// display_add_calendar_button_false
				update_option('r34ics_display_add_calendar_button_false', !empty($_POST['display_add_calendar_button_false']));
			
				// url_get_contents_legacy_method
				update_option('r34ics_url_get_contents_legacy_method', !empty($_POST['url_get_contents_legacy_method']));
			
				// use_new_defaults_10_6
				update_option('r34ics_use_new_defaults_10_6', !empty($_POST['use_new_defaults_10_6']));
			
				// load_css_js_on_wp_enqueue_scripts (removed in 10.7.1)
				delete_option('r34ics_load_css_js_on_wp_enqueue_scripts');
				
				$r34ics_deferred_admin_notices['r34ics_settings_updated'] = array(
					/* translators: 1. HTML tag 2. HTML tag */
					'content' => '<p>' . sprintf(esc_html__('%1$sSettings updated.%2$s You may need to refresh the page to see some changes.', 'ics-calendar'), '<strong>', '</strong>') . '</p>',
					'status' => 'success',
					'dismissible' => false,
				);

				// Save deferred admin notices
				update_option('r34ics_deferred_admin_notices', $r34ics_deferred_admin_notices);
			}
		}
		
	
		/**
		 * Note: This handling addresses an issue with, at least, Outlook/Office 365, where an
		 * individual instance that deviates from the recurrence rules appears in the array,
		 * *in addition to* the regular (but, in this instance, incorrect) rules. Each recurrence
		 * in the array can be identified by the 'uid' key. The individual instances that deviate
		 * are identified by 'recurrence_id', which is absent in the event entry that follows the
		 * normal rule. Therefore, we look for instances of 'recurrence_id', find the
		 * corresponding 'uid', and then look through all other events for the date for an
		 * instance that includes 'uid' but *not* 'recurrence_id'.
		 * 
		 * This may also be an issue with the ICS Parser library, in that it does not seem aware
		 * of this handling in Office 365.
		 * 
		 * @todo There has GOT to be a way to improve this!
		 */
		protected function _date_events_fix_recurrence_exceptions($events) {
			$recurrence_exceptions = array();
			foreach ((array)$events as $time => $time_events) {
				foreach ((array)$time_events as $te_event) {
					// This event is an exception!
					if (!empty($te_event['recurrence_id'])) {
						$recurrence_exceptions[$te_event['uid']] = $time;
					}
				}
			}
			if (!empty($recurrence_exceptions)) {
				foreach ((array)$recurrence_exceptions as $re_uid => $re_time) {
					foreach ((array)$events as $time => $time_events) {
						foreach ((array)$time_events as $te_key => $te_event) {
							// This is the "regular" time -- drop it
							if (empty($te_event['recurrence_id']) && $te_event['uid'] == $re_uid) {
								unset($events[$time][$te_key]);
								break(2);
							}
						}
					}
				}
			}
			return $events;
		}
		// Deprecated alias
		protected function _fix_recurrence_exceptions($events) {
			return $this->_date_events_fix_recurrence_exceptions($events);
		}
		
		
		protected function _date_events_sort($events, $view='month') {
			// Sort the event subarrays by time (key)
			ksort($events);
			// Sort each time slot's events alphabetically by the event label (title)
			foreach (array_keys((array)$events) as $time) {
				uasort($events[$time], function($a, $b) use ($view) {
					// If one event is multi-day and the other isn't, always put multi-day first
					if (!empty($a['multiday']) && empty($b['multiday'])) {
						return -1;
					}
					elseif (empty($a['multiday']) && !empty($b['multiday'])) {
						return 1;
					}
					// If both are multi-day...
					elseif (!empty($a['multiday']) && !empty($b['multiday'])) {
						// Do they both start on the same day?
						if ($a['multiday']['start_date'] == $b['multiday']['start_date']) {
							// If this is a list-style view, sort by earliest end date
							if (in_array(($view ?: ''), $this->list_style_views)) {
								return strcmp(($a['multiday']['end_date'] ?? ''), ($b['multiday']['end_date'] ?? ''));
							}
							// If this is not a list-style view, sort by latest end date, for better combinemultiday output
							else {
								return strcmp(($b['multiday']['end_date'] ?? ''), ($a['multiday']['end_date'] ?? ''));
							}
						}
						// ...otherwise, sort by start date
						else {
							return strcmp(($a['multiday']['start_date'] ?? ''), ($b['multiday']['start_date'] ?? ''));
						}
					}
					// If neither is multi-day, sort alphabetically by label
					return strcmp(($a['label'] ?? ''), ($b['label'] ?? ''));
				});
			}
			return $events;
		}


		/**
		 * Encapsulates any complex logic that needs to run when assigning values to the
		 * $event_item array in R34ICS2::display_calendar() and similar functions/methods.
		 */
		protected function _event_field_handling($field, $event, $args=null) {
			$value = '';
			
			// Field-specific logic
			switch ($field) {
			
				case 'eventdesc':
					// Use X_ALT_DESC if it exists
					if (!empty($event->x_alt_desc)) {
						$value = $event->x_alt_desc;
					}
					/**
					 * ALTREP = Alternate Text Representation
					 * https://icalendar.org/iCalendar-RFC-5545/3-2-1-alternate-text-representation.html
					 * At this time we are only aware of this being used by Thunderbird, and it
					 * appears to violate the spec by not wrapping the ALTREP value in quotation
					 * marks; the code here is tailored to Thunderbird's usage.
					 */
					elseif (isset($event->additionalProperties['description_array'][0]['ALTREP'])) {
						if ($event->additionalProperties['description_array'][0]['ALTREP'] == 'data') {
							/**
							 * Split by first comma which is the MIME type, then by first colon, which
							 * delimits between the encoded content and the plain text (apparently)
							 */
							$split1 = explode(',', @$event->additionalProperties['description_array'][1], 2);
							$split2 = explode(':', @$split1[1], 2);
							/**
							 * If MIME type is text/html, we'll take the decoded HTML as the value
							 * and fall back to the non-encoded text, if the decoded value is empty
							 */
							if ($split1[0] == 'text/html') {
								$value = rawurldecode($split2[0] ?: ($split2[1] ?: ''));
							}
							/**
							 * @todo Handle other MIME types; for now, just take the unencoded text if
							 * it exists, otherwise, use the encoded version and hope for the best...
							 */
							else {
								$value = $split2[1] ?: ($split2[0] ?: '');
							}
						}
						/**
						 * @todo Determine how "correct" uses of ALTREP need to be formatted; for now,
						 * assume ics-parser is correctly splitting the value into the array and
						 * use it as-is.
						 */
						else {
							$value = $event->description ?: '';
						}
					}
					// Use standard DESCRIPTION
					else {
						$value = $event->description ?: '';
					}
					break;
			
				default: break; // Do nothing;
			}
			
			// Allow external filtering
			$value = apply_filters('r34ics_event_field_handling', $value, $field, $event, $args);
			
			return $value;
		}
			
	
		protected function _event_ics_download() {
		
			$feed_key = get_query_var('r34ics-feed-key'); // Will often/usually be 0
			$feed_urlids = get_query_var('r34ics-urlids');
			$uid = get_query_var('r34ics-uid');
						
			// Get the correct feed URL
			if ($url = r34ics_uniqid_url($feed_urlids[$feed_key])) {
			
				// Retrieve the feed
				$ics_contents = $this->_url_get_contents($url, false, true);
				
				// Parse ICS contents
				if (!$this->parser_loaded) {
					$this->_load_parser();
				}
				$ICal = new R34ICS2_ICal\ICal('ICal.ics');
				$ICal->initString($ics_contents);
	
				// Free up some memory
				unset($ics_contents);
			
				// Find the event
				if ($ICal->hasEvents() && $ics_events = $ICal->events()) {
			
					foreach ((array)$ics_events as $event_item) {
						if ($event_item->uid == $uid) { break; }
					}
								
					if (is_object($event_item)) {
						$content = array();
						$content[] = 'BEGIN:VCALENDAR';
						$content[] = 'PRODID:-//Room 34 Creative Services LLC//ICS Calendar ' . get_option('r34ics_version') . '//' . strtoupper(substr(get_locale(),0,2));
						$content[] = 'VERSION:2.0';
						$content[] = 'BEGIN:VEVENT';
						if ($event_item->attach) { $content[] = 'ATTACH:' . r34ics_maybe_enfold($event_item->attach, 7); }
						if ($event_item->categories) { $content[] = 'CATEGORIES:' . r34ics_maybe_enfold($event_item->categories, 11); }
						if ($event_item->contact) { $content[] = 'CONTACT:' . $event_item->contact; }
						if ($event_item->created) { $content[] = 'CREATED:' . $event_item->created; }
						if ($event_item->description) { $content[] = 'DESCRIPTION:' . r34ics_maybe_enfold($event_item->description, 12); }
						if ($event_item->dtend) { $content[] = 'DTEND:' . $event_item->dtend; }
						if ($event_item->dtstamp) { $content[] = 'DTSTAMP:' . $event_item->dtstamp; }
						if ($event_item->dtstart) { $content[] = 'DTSTART:' . $event_item->dtstart; }
						if ($event_item->duration) { $content[] = 'DURATION:' . $event_item->duration; }
						if ($event_item->exdate_array) {
							$item_key = key($event_item->exdate_array[0]);
							$content[] = 'EXDATE;' . $item_key . '=' . $event_item->exdate_array[0][$item_key] . ':' . $event_item->exdate_array[1];
						}
						if ($event_item->freebusy) { $content[] = 'FREEBUSY:' . $event_item->freebusy; }
						if ($event_item->geo) { $content[] = 'GEO:' . $event_item->geo; }
						if ($event_item->last_modified) { $content[] = 'LAST-MODIFIED:' . $event_item->last_modified; }
						if ($event_item->location) { $content[] = 'LOCATION:' . r34ics_maybe_enfold($event_item->location, 9); }
						if ($event_item->organizer) { $content[] = 'ORGANIZER:' . r34ics_maybe_enfold($event_item->organizer, 10); }
						if ($event_item->rdate_array) {
							$item_key = key($event_item->rdate_array[0]);
							$content[] = 'RDATE;' . $item_key . '=' . $event_item->rdate_array[0][$item_key] . ':' . $event_item->rdate_array[1];
						}
						if ($event_item->recurrence_id) { $content[] = 'RECURRENCE-ID:' . $event_item->recurrence_id; }
						if ($event_item->resources) { $content[] = 'RESOURCES:' . $event_item->resources; }
						if ($event_item->rrule) { $content[] = 'RRULE:' . $event_item->rrule; }
						if ($event_item->sequence) { $content[] = 'SEQUENCE:' . $event_item->sequence; }
						if ($event_item->status) { $content[] = 'STATUS:' . $event_item->status; }
						if ($event_item->summary) { $content[] = 'SUMMARY:' . r34ics_maybe_enfold($event_item->summary, 8); }
						if ($event_item->transp) { $content[] = 'TRANSP:' . $event_item->transp; }
						if ($event_item->uid) { $content[] = 'UID:' . r34ics_maybe_enfold($event_item->uid, 4); }
						if ($event_item->url) { $content[] = 'URL:' . r34ics_maybe_enfold($event_item->url, 4); }
						$content[] = 'END:VEVENT';
						$content[] = 'END:VCALENDAR';
						$content = implode("\r\n", $content);
						header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
						header('Expires: 0');
						header('Content-Type: text/calendar');
						header('Content-Disposition: attachment; filename="' . sanitize_title(esc_html__('calendar event', 'ics-calendar')) . '.ics"');
						header('Content-Length: ' . strlen($content ?? ''));
						echo wp_kses_post($content ?: '');
						exit;
					}
				}
			}
		}
	
	
		private function _get_version() {
			if (!function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}
			$plugin_data = get_plugin_data(dirname(__FILE__) . '/ics-calendar.php', false, false);
			$version = $plugin_data['Version'];
			// Are we running an embedded version?
			global $r34ics_embedded;
			if (!empty($r34ics_embedded)) { $version .= 'E'; }
			return $version;
		}
	
	
		protected function _load_parser($legacy=false) {
			if (!class_exists('R34ICS2_ICal\ICal')) { include_once($legacy ? $this->ical_legacy_path : $this->ical_path); }
			if (!class_exists('R34ICS2_ICal\Event')) { include_once($legacy ? $this->event_legacy_path : $this->event_path); }
			$this->parser_loaded = true;
			return true;
		}
		
		
	
		protected function _load_pand() {
			if (!class_exists('PAnD')) { include_once($this->pand_path); }
		}


		/**
		 * Retrieve file from remote server (previous cURL/fopen methods replaced with wp_remote_get() in version 11.0.0)
		 */
		protected function _url_get_contents($url, $recursion=0, $use_transients=false, $basicauth=false, $skip_domain_errors=false, $ua=false) {
			// Use legacy method?
			if (get_option('r34ics_url_get_contents_legacy_method')) {
				return $this->_url_get_contents_legacy($url, '', $recursion, null, $use_transients, $basicauth, $skip_domain_errors);
			}
		
			// Report that we are using the new method
			if ($this->debug) { $this->debug_messages[$url]['Method'][] = __METHOD__; }
			
			// Are we at debug level 3 or greater? If so, don't use transients
			if (!empty($this->debug) && $this->debug >= 3) { $use_transients = false; }
	
			// We'll keep track of any domains that didn't return contents
			global $r34ics_url_get_contents_domain_errors;
			if (!isset($r34ics_url_get_contents_domain_errors)) {
				$r34ics_url_get_contents_domain_errors = array();
			}
			
			// Must have a URL
			if (empty($url)) {
				if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'No ICS URL provided.'; }
				return false;
			}
	
			/**
			 * Fix URL protocol
			 * webcal:// is a pseudo-protocol to trigger external client-side handling
			 * https:// is the "real" protocol used to make the server-side connection
			 */
			if (strpos($url, 'webcal://') === 0) { $url = str_replace('webcal://', 'https://', $url); }
	
			/**
			 * Clean up URL
			 * 1. Remove HTML (e.g. if the URL came through as a clickable link wrapped in an <a> tag)
			 * 2. Convert ampersand entities to plain ampersands
			 * 3. Run standard PHP sanitize filter
			 */
			$url = filter_var(str_replace('&amp;', '&', wp_strip_all_tags($url)), FILTER_SANITIZE_URL);
		
			// Parse URL for further validation
			$url_parsed = wp_parse_url($url);
			
			// Bail out now if URL is from a domain we've already failed to access
			if (
				!empty($skip_domain_errors) &&
				!empty($r34ics_url_get_contents_domain_errors) &&
				in_array($url_parsed['host'], $r34ics_url_get_contents_domain_errors)
			) {
				if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Skipped URL because of previous errors with this domain.'; }
				return false;
			}
	
			// HTTP Basic Authentication (must come before wp_http_validate_url())
			$basicauth_credentials = '';
			if (!empty($basicauth) || (!empty($url_parsed['user']) && !empty($url_parsed['pass']))) {
				// Credentials passed in URL
				if (!empty($url_parsed['user']) && !empty($url_parsed['pass'])) {
					$basicauth = true;
					$basicauth_credentials = $url_parsed['user'] . ':' . $url_parsed['pass'];
					// Strip credentials out of URL for request
					$url = str_replace($basicauth_credentials . '@', '', $url);
				}
				// Credentials defined in wp-config.php
				elseif (defined('R34ICS2_FEED_BASICAUTH') && strpos(R34ICS2_FEED_BASICAUTH, ':') !== false) {
					$basicauth_credentials = R34ICS2_FEED_BASICAUTH;
				}
			}
			
			/**
			 * Bail out now if URL fails wp_http_validate_url() check
			 *
			 * Note: For internal network security, this prevents access to URLs that resolve to
			 * private/reserved IP address ranges. "Allow access to these hostnames that resolve
			 * to reserved IP addresses" on the ICS Calendar Settings page allows you to specify
			 * hostnames that should be allowed to override this restriction.
			 */
			if (!wp_http_validate_url($url)) {
				if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'Invalid ICS feed URL.'; }
				return false;
			}
			
			/**
			 * Add URL to our list of valid feed URLs
			 * Gives each URL a unique key, so it can be referenced in forms on the front end without exposing URL
			 */
			r34ics_url_uniqid_update($url);
	
			// Read URL contents from transient if applicable (skip on recursion for obvious reasons)
			$transient_name = __METHOD__ . '_' . sha1($url);
			if (!empty($use_transients) && empty($recursion)) {
				if ($url_contents = get_transient($transient_name)) {
					if ($this->debug) {
						$this->debug_messages[$url]['Load status'] = 'ICS feed URL loaded from transient';
						$this->debug_messages[$url]['Transient name'] = $transient_name;
					}
					return $url_contents;
				}
			}
		
			// Debugging messages
			if ($this->debug) {
				if (empty($url)) { $this->debug_messages[$url]['Errors'][] = 'No URL provided to method ' . __METHOD__ . '().'; }
				else {
					$this->debug_messages[$url]['ICS Feed'] = '<a href="' . $url . '" target="_blank" download="' . pathinfo($url,PATHINFO_BASENAME) . '">DOWNLOAD</a> // <a href="https://icalendar.org/validator.html?url=' . esc_url($url) . '#results" target="_blank">VALIDATE</a>';
				}
			}
	
			$url_contents = null;
			$response_code = null;
	
			// Create array of HTTP headers
			global $wp;
			$locale = get_locale();
			$http_headers = array(
				'Accept' => 'text/calendar;q=0.9,text/*;q=0.8,*/*;q=0.5',
				'Accept-Encoding' => 'gzip',
				'Accept-Language' => str_replace('_', '-', $locale) . ',' . substr($locale,0,2) . ';q=0.9,en-US,en;q=0.8,*;q=0.5',
				'Connection' => 'keep-alive',
				'Host' => wp_parse_url($url, PHP_URL_HOST),
				'Referer' => home_url($wp->request),
				'Sec-Fetch-Dest' => 'document',
				'Sec-Fetch-Mode' => 'navigate',
				'Sec-Fetch-Site' => 'same-origin',
			);
			
			// Add authentication credentials
			if (!empty($basicauth_credentials)) {
				$http_headers['Authorization'] = 'Basic ' . base64_encode($basicauth_credentials);
			}
			
			// Set user agent string
			$user_agent = 'WordPress/' . get_bloginfo('version') . '; ICS Calendar/' . $this->_get_version() . '; ' . get_bloginfo('url');
			$user_agent_real = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Safari/605.1.15';
			// Allow user agent override
			if (!empty($ua)) {
				switch (gettype($ua)) {
					case 'string': $user_agent = trim(wp_strip_all_tags($ua)); break; // Custom UA string provided
					case 'boolean': case 'integer': $user_agent = $user_agent_real; break; // Use our "real" UA string
					default: break;
				}
			}
			
			// Build array of request argments
			$request_args = array(
				'cookies' => array('r34ics' => 1),
				'decompress' => true,
				'headers' => $http_headers,
				'httpversion' => '1.1',
				'method' => 'GET',
				'redirection' => 0,
				'sslcertificates' => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
				'sslverify' => false,
				'timeout' => 30,
				'user-agent' => $user_agent,
			);
	
			// Make the remote request
			$response = wp_remote_get($url, $request_args);
			$url_contents = wp_remote_retrieve_body($response);
			$response_code = wp_remote_retrieve_response_code($response);
			if ($this->debug) {
				$this->debug_messages[$url]['Load status'][] = array(
					'Method' => 'URL loaded via wp_remote_get()',
					'Arguments' => $request_args,
				);
			}
					
			/**
			 * Follow rewrites
			 * If possible, we check for a 301 or 302 response code, falling back on certain text strings contained in the response
			 * Outlook rewrites may include the string '">Found</a>' in the output
			 * Most other feeds (e.g. Google Calendar) will include 'Moved Permanently' in the output
			 */
			if ($recursion < 5 && !empty($response) && (
					$response_code == '301' ||
					$response_code == '302' ||
					stripos(($url_contents ?? ''), '">Found</a>') !== false ||
					stripos(($url_contents ?? ''), 'Moved Permanently') !== false ||
					strpos(($url_contents ?? ''), 'Object moved') !== false
			)) {
				$recursion++;
	
				// Get location from headers
				if ($location = wp_remote_retrieve_header($response, 'Location')) {
						if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Recursively loaded rewrite URL ' . $location; }
						$url_contents = $this->_url_get_contents($location, $recursion, $use_transients, $basicauth, $skip_domain_errors, $ua);
				}
	
				// Fallback: Scrape URL from returned HTML if necessary
				else {
					preg_match('/<(a href|A HREF)="([^"]+)"/', $url_contents, $url_match);
					if (isset($url_match[2])) {
						if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Recursively loaded rewrite URL ' . $url_match[2]; }
						$url_contents = $this->_url_get_contents($url_match[2], $recursion, $use_transients, $basicauth, $skip_domain_errors, $ua);
					}
					else {
						if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'No redirect URL provided by server'; }
					}
				}
			}
	
			// Remember domain errors to skip subsequent requests (+ additional debugging data)
			if (empty($url_contents)) {
				if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'URL contents empty (' . $url . ')'; }
				$r34ics_url_get_contents_domain_errors[] = $url_parsed['host'];
			}
			else {
				if ($this->debug) { $this->debug_messages[$url]['URL contents retrieved'] = strlen($url_contents ?? '') . ' bytes'; }
			}
			
			// Write URL contents to transient
			if (!empty($use_transients) && !empty($url_contents)) {
				$transient_expiration = get_option('r34ics_transients_expiration');
				set_transient($transient_name, $url_contents, $transient_expiration);
			}
		
			return $url_contents;
		}
	
	
		/**
		 * Retrieve file from remote server with fallback methods
		 * Based on: https://stackoverflow.com/a/21177510
		 *
		 * DEPRECATED -- Runs only if the r34ics_legacy_url_get_contents option is set
		 *
		 * Note: This method triggers several error messages in the Plugin Check plugin;
		 * it is only retained for legacy purposes when the _url_get_contents() method --
		 * which *does* use the preferred wp_remote_get() instead of cURL functions --
		 * does not work properly on certain server configurations. We have not been able
		 * to replicate the issues in our test environments, so we resorted to retaining the
		 * old functionality in this legacy method.
		 */
		protected function _url_get_contents_legacy($url, $method='', $recursion=false, $curlopts=null, $use_transients=false, $basicauth=false, $skip_domain_errors=false) {
			$method = (string)$method; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
	
			// Report that we are using the legacy method
			// Reminder: This refers to the class method; it has nothing to do with the $method variable!
			if ($this->debug) { $this->debug_messages[$url]['Method'][] = __METHOD__; }
			
			// Are we at debug level 3 or greater? If so, don't use transients
			if (!empty($this->debug) && $this->debug >= 3) { $use_transients = false; }
	
			// We'll keep track of any domains that didn't return contents
			global $r34ics_url_get_contents_domain_errors;
			if (!isset($r34ics_url_get_contents_domain_errors)) {
				$r34ics_url_get_contents_domain_errors = array();
			}
			
			// Must have a URL
			if (empty($url)) {
				if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'No ICS URL provided.'; }
				return false;
			}
	
			/**
			 * Fix URL protocol
			 * webcal:// is a pseudo-protocol to trigger external client-side handling
			 * https:// is the "real" protocol used to make the server-side connection
			 */
			if (strpos($url, 'webcal://') === 0) { $url = str_replace('webcal://', 'https://', $url); }
	
			/**
			 * Clean up URL
			 * 1. Remove HTML (e.g. if the URL came through as a clickable link wrapped in an <a> tag)
			 * 2. Convert ampersand entities to plain ampersands
			 * 3. Run standard PHP sanitize filter
			 */
			$url = filter_var(str_replace('&amp;', '&', wp_strip_all_tags($url)), FILTER_SANITIZE_URL);
		
			// Parse URL for further validation
			$url_parsed = wp_parse_url($url);
			
			// Bail out now if URL is from a domain we've already failed to access
			if (
				!empty($skip_domain_errors) &&
				!empty($r34ics_url_get_contents_domain_errors) &&
				in_array($url_parsed['host'], $r34ics_url_get_contents_domain_errors)
			) {
				if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Skipped URL because of previous errors with this domain.'; }
				return false;
			}
	
			/**
			 * Bail out now if URL fails wp_http_validate_url() check
			 *
			 * Note: For internal network security, this prevents access to URLs that resolve to
			 * private/reserved IP address ranges. "Allow access to these hostnames that resolve
			 * to reserved IP addresses" on the ICS Calendar Settings page allows you to specify
			 * hostnames that should be allowed to override this restriction.
			 */
			if (!wp_http_validate_url($url)) {
				if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'Invalid ICS feed URL.'; }
				return false;
			}
			
			/**
			 * Add URL to our list of valid feed URLs
			 * Gives each URL a unique key, so it can be referenced in forms on the front end without exposing URL
			 */
			r34ics_url_uniqid_update($url);
	
			// Read URL contents from transient if applicable (skip on recursion for obvious reasons)
			$transient_name = __METHOD__ . '_' . sha1($url);
			if (!empty($use_transients) && empty($recursion)) {
				if ($url_contents = get_transient($transient_name)) {
					if ($this->debug) {
						$this->debug_messages[$url]['Load status'] = 'ICS feed URL loaded from transient';
						$this->debug_messages[$url]['Transient name'] = $transient_name;
					}
					return $url_contents;
				}
			}
		
			// Valid method values
			$valid_methods = array('curl', 'fopen');
			$method = in_array(strtolower($method), $valid_methods) ? strtolower($method) : null;
		
			// Debugging messages
			if ($this->debug) {
				if (empty($url)) { $this->debug_messages[$url]['Errors'][] = 'No URL provided to function ' . __FUNCTION__ . '().'; }
				else {
					$this->debug_messages[$url]['ICS Feed'] = '<a href="' . $url . '" target="_blank" download="' . pathinfo($url,PATHINFO_BASENAME) . '">DOWNLOAD</a> // <a href="https://icalendar.org/validator.html?url=' . esc_url($url) . '#results" target="_blank">VALIDATE</a>';
				}
			}
	
			$url_contents = null;
			$curl_response_code = null;
			$curl_redirect_url = null;
			$user_agent_ics = 'ICS Calendar ' . get_option('r34ics_version') . ' for WordPress';
			$user_agent_real = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.4 Safari/605.1.15';
			$user_agent = (is_array($curlopts) && in_array('useragent', $curlopts)) ? $user_agent_real : $user_agent_ics;
		
			// Attempt to use cURL functions
			if (defined('CURLVERSION_NOW') && function_exists('curl_exec') && (empty($method) || $method == 'curl')) { 
				if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Attempted to load URL via cURL'; }
				$conn = curl_init($url);
				if (file_exists(ABSPATH . 'wp-includes/certificates/ca-bundle.crt')) {
					curl_setopt($conn, CURLOPT_CAINFO, ABSPATH . 'wp-includes/certificates/ca-bundle.crt');
					curl_setopt($conn, CURLOPT_CAPATH, ABSPATH . 'wp-includes/certificates');
				}
				curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($conn, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
				curl_setopt($conn, CURLOPT_MAXREDIRS, 5);
				curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
				
				// Create array of HTTP headers
				$locale = get_locale();
				$http_headers = array(
					'Accept: text/calendar;q=0.9,text/*;q=0.8,*/*;q=0.5',
					'Accept-Encoding: gzip',
					'Accept-Language: ' . str_replace('_', '-', $locale) . ',' . substr($locale,0,2) . ';q=0.9,en-US,en;q=0.8,*;q=0.5',
					'Connection: keep-alive',
					'Host: ' . wp_parse_url($url, PHP_URL_HOST),
					'Sec-Fetch-Dest: document',
					'Sec-Fetch-Mode: navigate',
					'Sec-Fetch-Site: same-origin',
					'User-Agent: ' . $user_agent,
				);
				// Add HTTP headers
				curl_setopt($conn, CURLOPT_HTTPHEADER, $http_headers);
				
				// Add basic authentication
				if (!empty($basicauth) && defined('R34ICS2_FEED_BASICAUTH') && strpos(R34ICS2_FEED_BASICAUTH, ':') !== false) {
					curl_setopt($conn, CURLOPT_USERPWD, R34ICS2_FEED_BASICAUTH); 
				}
				
				// Remember the Let's Encrypt X3 apocalypse! 2021.09.30
				curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
				
				// Set user agent
				curl_setopt($conn, CURLOPT_USERAGENT, $user_agent);
			
				// Set a cookie; *may* help with connections to some services, like CloudFlare
				if (is_array($curlopts) && in_array('cookie', $curlopts)) {
					$cookie_path = r34ics_curl_cookie_path();
					curl_setopt($conn, CURLOPT_COOKIEJAR, $cookie_path);
					curl_setopt($conn, CURLOPT_COOKIEFILE, $cookie_path);	
				}
	
				// Allow external customization of cURL options
				$conn = apply_filters('r34ics_url_get_contents_customize_curl_options', $conn);
				
				// Make the connection!
				$url_contents = (curl_exec($conn));
				$curl_response_code = curl_getinfo($conn, CURLINFO_RESPONSE_CODE);
				$curl_redirect_url = curl_getinfo($conn, CURLINFO_REDIRECT_URL);
				
				// Add debugging messages and close the connection
				if ($this->debug >= 2) {
					$this->debug_messages[$url]['cURL HTTP headers'][] = $http_headers;
					$this->debug_messages[$url]['cURL connection info'][] = curl_getinfo($conn);
				}
				elseif ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'HTTP response: ' . $curl_response_code; }
				curl_close($conn);
			}
	
			// Attempt to use fopen functions if cURL failed
			// Note: cURL is the preferred and most common method; this method offers fewer options and has not been as rigorously tested
			if (ini_get('allow_url_fopen') && (empty($url_contents) || $method == 'fopen' || intval($curl_response_code) >= 400)) {
				if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Attempted to load URL via file_get_contents()'; }
				$context_options = array(
					'http' => array(
						'max_redirects' => 5,
						'timeout' => 5,
						'user_agent' => $user_agent,
					),
					'ssl' => array(
						'verify_peer' => false, // Remember the Let's Encrypt X3 apocalypse! 2021.09.30
					),
				);
				$context = stream_context_create($context_options);
				$url_contents = file_get_contents($url, false, $context);
			}
	
			/**
			 * Follow rewrites (if CURLOPT_FOLLOWLOCATION failed or using fopen)
			 * If possible, we check for a 301 or 302 response code, falling back on certain text strings contained in the response
			 * Outlook rewrites may include the string '">Found</a>' in the output
			 * Most other feeds (e.g. Google Calendar) will include 'Moved Permanently' in the output
			 */
			if (!$recursion && (
					$curl_response_code == '301' ||
					$curl_response_code == '302' ||
					stripos(($url_contents ?? ''), '">Found</a>') !== false ||
					stripos(($url_contents ?? ''), 'Moved Permanently') !== false ||
					strpos(($url_contents ?? ''), 'Object moved') !== false
			)) {
	
				// Use cURL redirect URL if provided
				if (!empty($curl_redirect_url)) {
					if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Recursively loaded URL ' . $curl_redirect_url . ' by following a rewrite returned by the server'; }
					$url_contents = $this->_url_get_contents_legacy($curl_redirect_url, $method, true, $curlopts, $use_transients);
				}
	
				// Scrape URL from returned HTML if necessary
				else {
					preg_match('/<(a href|A HREF)="([^"]+)"/', $url_contents, $url_match);
					if (isset($url_match[2])) {
						if ($this->debug) { $this->debug_messages[$url]['Load status'][] = 'Recursively loaded URL ' . $url_match[2] . ' by following a rewrite returned by the server'; }
						$url_contents = $this->_url_get_contents_legacy($url_match[2], $method, true, $curlopts, $use_transients);
					}
					else {
						if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'No redirect URL provided by server'; }
					}
				}
			}
	
			// Remember domain errors to skip subsequent requests (+ additional debugging data)
			if (empty($url_contents)) {
				if ($this->debug) { $this->debug_messages[$url]['Errors'][] = 'URL contents empty (' . $url . ')'; }
				$r34ics_url_get_contents_domain_errors[] = $url_parsed['host'];
			}
			else {
				if ($this->debug) { $this->debug_messages[$url]['URL contents retrieved'] = strlen($url_contents ?? '') . ' bytes'; }
			}
			
			// Try to decode/inflate URL contents if applicable
			if (false !== ($gzdecoded = @gzdecode($url_contents))) {
				$url_contents = $gzdecoded;
			}
	
			// Write URL contents to transient
			if (!empty($use_transients) && !empty($url_contents)) {
				$transient_expiration = get_option('r34ics_transients_expiration');
				set_transient($transient_name, $url_contents, $transient_expiration);
			}
		
			return $url_contents;
		}
	
	}
	
}
