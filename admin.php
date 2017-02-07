<?php 
/**************************************************************
 *
 * Admin Page
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 admin.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

if(!defined('ABSPATH')){
	exit;
}

// Let's go!
if(class_exists('DB_admin_init')){
	new DB_admin_init();
}

// manula builder class
class DB_admin_init{
	
	/**
	 * Our plugin version
	 *
	 * @var string
	 */
	public static $version = '1.0.1';

	/**
	 * Our plugin file
	 *
	 * @var string
	 */
	public static $plugin_file = __FILE__;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		
		// load settings & install function 
		require_once(dirname(__FILE__).'/settings.php');
		require_once(dirname(__FILE__).'/install.php');

		// Activation and uninstall hooks
		register_activation_hook(__FILE__, array(__CLASS__, 'do_activation'));
		register_uninstall_hook( __FILE__, array(__CLASS__, 'do_uninstall'));

		// Load dependancies
		$this->load_dependancies();

		// Load templates
		$this->load_templates();

		// Setup localization
		$this->set_locale();

		// Define hooks
		$this->define_hooks();

		// Define Ajax
		$this->define_ajax();

		// Define Shortcode
		$this->define_shortcode();

	}

	/**
	 * Activation
	 *
	 * @return void
	 */
	public static function do_activation(){

		global $wp_version;

		// Deactivate the plugin if the WordPress version is below the minimum required.
		if (version_compare($wp_version, '4.0', '<')){
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__(sprintf('Sorry, but your version of WordPress, <strong>%s</strong>, is not supported. The plugin has been deactivated. <a href="%s">Return to the Dashboard.</a>', $wp_version, admin_url()), 'truewordpress'));
			return false;
		}

		// Add options
		add_option('DB_version', self::$version);

		global $DB_settings;
		add_option('DB_settings', $DB_settings);

		// Trigger hooks
		do_action('DB_activate');

	}

	/**
	 * Uninstall
	 *
	 * @return void
	 */
	public static function do_uninstall(){

		// Get the settings
		global $DB_conf;

		// If enabled, remove the plugin data
		if ($DB_conf['remove_data']){

			// Delete all of the datas
			foreach (DB_db::all_docs() as $md){
				DB_db::delete($md->ID);
			}

			// Delete options
			delete_option('DB_version');
			delete_option('DB_settings');

			// Remove data hook
			do_action('DB_remove_data');

		}
			
		// Trigger hooks
		do_action('DB_uninstall');

	}

	/**
	 * Load dependancies
	 *
	 * @return void
	 */
	protected function load_dependancies(){
		
		require_once(DB_INCLUDE.'class-settings.php');
		require_once(DB_INCLUDE.'class-flxziparchive.php');
		require_once(DB_INCLUDE.'class-global.php');
		require_once(DB_INCLUDE.'class-db.php');
		require_once(DB_INCLUDE.'class-ajax.php');
		require_once(DB_INCLUDE.'class-shortcode.php');
		require_once(DB_INCLUDE.'class-export.php');
		require_once(DB_INCLUDE.'class-menu.php');
		require_once(DB_INCLUDE.'class-assets.php');
		require_once(DB_INCLUDE.'class-list.php');
		require_once(DB_INCLUDE.'class-page.php');

	}

	/**
	 * Load templates
	 *
	 * @return void
	 */
	protected function load_templates(){
		
		require_once(DB_TEMPLATES.'new.php');
		require_once(DB_TEMPLATES.'edit.php');
		require_once(DB_TEMPLATES.'list.php');
		require_once(DB_TEMPLATES.'settings.php');
		require_once(DB_TEMPLATES.'export.php');

	}

	/**
	 * Set locale
	 *
	 * @return void
	 */
	protected function set_locale(){

		// Load plugin textdomain
		load_plugin_textdomain('documentation-builder', false, DB_LANGUAGE);

	}

	/**
	 * Define menu hooks
	 *
	 * @return void
	 */
	protected function define_hooks(){

		// Initiate components
		$menu = new DB_admin_menu();
		$assets = new DB_admin_assets();

		/**
		 * Hook everything, "connect all the dots"!
		 *
		 * All of these actions connect the various parts of our plugin together.
		 * The idea behind this is to keep each "component" as separate as possible, decoupled from other components.
		 *
		 * These hooks bridge the gaps.
		 */
		add_action('admin_menu', array($menu, 'add_toplevel_menu'));
		add_action('admin_enqueue_scripts', array($assets, 'add_assets'));

	}

	/**
	 * Define ajax hooks
	 *
	 * @return void
	 */
	protected function define_ajax(){

		// Initiate components
		$ajax = new DB_ajax();
		
		add_action('wp_ajax_edit', array($ajax, 'doc_edit'));
		add_action('wp_ajax_nopriv_edit', array($ajax, 'doc_edit'));

		add_action('wp_ajax_new', array($ajax, 'doc_new'));
		add_action('wp_ajax_nopriv_new', array($ajax, 'doc_new'));

		add_action('wp_ajax_del', array($ajax, 'doc_del'));
		add_action('wp_ajax_nopriv_del', array($ajax, 'doc_del'));

		add_action('wp_ajax_export', array($ajax, 'doc_export'));
		add_action('wp_ajax_nopriv_export', array($ajax, 'doc_export'));

		add_action('wp_ajax_settings', array($ajax, 'doc_settings'));
		add_action('wp_ajax_nopriv_settings', array($ajax, 'doc_settings'));

		add_action('wp_ajax_skin', array($ajax, 'doc_skin'));
		add_action('wp_ajax_nopriv_skin', array($ajax, 'doc_skin'));
	}

	/**
	 * Define shortcode hooks
	 *
	 * @return void
	 */
	protected function define_shortcode(){

		// Initiate components
		$shortcode = new DB_shortcode();
		
		add_shortcode(DB_SHORTCODE, array($shortcode, 'doc_shortcode'));
	}
}

?>