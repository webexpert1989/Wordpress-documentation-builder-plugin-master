<?php 
/**************************************************************
 *
 * Configuration Plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 settings.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

/***
 * Plugin DB settings
 */
global $wpdb;

define('DB_DB_DOCS', $wpdb->prefix.'db_docs');
define('DB_DB_CONF', $wpdb->prefix.'db_conf');

define('DB_DB_VERSION', '1.0.1');


/***
 * alias & download root url
 */
$uploadInfo = wp_upload_dir();
define('DB_ALIAS', $uploadInfo['basedir'].'/db/');
define('DB_DOWNLOAD', $uploadInfo['baseurl'].'/db/');


/***
 * plugin directories
 */

// plugin root dir
define('DB_URL', dirname(__FILE__).'/');

// include dir
define('DB_LANGUAGE', DB_URL.'lang/');
define('DB_INCLUDE', DB_URL.'inc/');
define('DB_TEMPLATES', DB_URL.'templates/');
define('DB_SKINS', DB_URL.'skins/');

// load assets dir
define('DB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DB_ASSETS', DB_PLUGIN_URL.'assets/');


/***
 * plugin settings
 */

global $DB_conf;
$DB_conf = array(

	// system conf
	'remove_data'   => false,		// if this is true, remove all datas of database table until uninstalling plugin	
	'pages'			=> array(		// page ID
		'list'		=> 'DB-admin',
		'new'		=> 'DB-new',
		'settings'	=> 'DB-settings',
		'export'	=> 'DB-export',
	),

	// default value for custom settings
	'rows_per_page'			=> 20,	// max rows number of list table page
	'auto_save'				=> 300,	// save the documentation automatically every 10 seconds (default is 10 seconds)
);

//////
global $DB_settings;
$DB_settings = array(
	'rows_per_page'			=> 20,	// max rows number of list table page
	'auto_save'				=> 300,	// save the documentation automatically every 10 seconds (default is 10 seconds)
);

/***
 * shortcode prefix
 */

define('DB_SHORTCODE', 'DB_doc');

?>