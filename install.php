<?php 
/**************************************************************
 *
 * Install Plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 install.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

require_once(dirname(__FILE__).'/settings.php');

// install database for Order Contact
register_activation_hook(__FILE__, 'DB_install');

function DB_install(){
	//////
	global $wpdb;
	
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');

	$sql = '		
		CREATE TABLE IF NOT EXISTS `'.DB_DB_DOCS.'` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `doc_logo` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
		  `doc_name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
		  `doc_desc` LONGBLOB NOT NULL,
		  `doc_shortcode` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
		  `doc_content` LONGBLOB NOT NULL,
		  `date` DATETIME NOT NULL,
		  `author` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
		  `skin` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
		  `status` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
	';
	dbDelta($sql);

	$sql = '
		CREATE TABLE IF NOT EXISTS `'.DB_DB_CONF.'` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `skin_name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
		  `skin_data` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
	';
	dbDelta($sql);
	
	/////
	add_option('DB_DB_VERSION', DB_DB_VERSION);

	// make contents folders
	if(!is_dir(DB_ALIAS)) mkdir(DB_ALIAS, 0755);

	chmod(DB_ALIAS, 0755);
}

// install start
function DB_update_db_check(){
    if (get_site_option('DB_DB_VERSION') != DB_DB_VERSION){
        DB_install();
    }
}
add_action('plugins_loaded', 'DB_update_db_check');
?>