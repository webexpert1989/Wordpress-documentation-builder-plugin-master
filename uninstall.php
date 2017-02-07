<?php 
/**************************************************************
 *
 * Uninstall Plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 uninstall.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

require_once(dirname(__FILE__).'/settings.php');
require_once(DB_INCLUDE.'global.php');

//---------
if (!defined('WP_UNINSTALL_PLUGIN')){
	exit();
}

// Hook for uninstall
DB_delete_plugin();
function DB_delete_plugin(){
	// remove directory
	DB_global::delTree(DB_ALIAS);

	// remove tables in DB
	global $wpdb;
	$wpdb->query('DROP TABLE `'.DB_DB_DOCS.'`, `'.DB_DB_CONF.'`;');
	
	delete_option('DB_DB_VERSION');
}
?>