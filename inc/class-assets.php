<?php
/**************************************************************
 *
 * load assets class for documentation list admin page 
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-assets.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_admin_assets is available
if(!class_exists('DB_admin_assets')){

	// CREATE A PACKAGE CLASS
	class DB_admin_assets{
		
		
		/**
		 * constuct
		 * @return:	void
		 */
		function __construct(){
			return;
		}
		
		
		/**
		 * add javascript & css files to wordpress
		 *
		 * @return:	void
		 */
		public function add_assets(){
			
			$this->add_js();
			$this->add_css();

			return;
		}
				
		/**
		 * register & active javascript files
		 *
		 * @return:	void
		 */
		protected function add_js(){

			wp_register_script('DB_lib_touch_punch', DB_ASSETS.'lib/jquery.ui.touch-punch.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
			wp_enqueue_script('DB_lib_touch_punch');
			wp_register_script('DB_lib_nestedSortable_js', DB_ASSETS.'lib/jquery.mjs.nestedSortable-fixed.js');
			wp_enqueue_script('DB_lib_nestedSortable_js');

			wp_register_script('DB_plugin_ace_js', DB_ASSETS.'lib/jquery-ace/ace/ace.js');
			wp_register_script('DB_plugin_ace_js_theme', DB_ASSETS.'lib/jquery-ace/ace/theme-chrome.js');
			wp_register_script('DB_plugin_ace_js_mode', DB_ASSETS.'lib/jquery-ace/ace/mode-css.js');
			wp_register_script('DB_plugin_ace_js_jquery', DB_ASSETS.'lib/jquery-ace/jquery-ace.js');
			wp_enqueue_script('DB_plugin_ace_js');
			wp_enqueue_script('DB_plugin_ace_js_theme');
			wp_enqueue_script('DB_plugin_ace_js_mode');
			wp_enqueue_script('DB_plugin_ace_js_jquery');

			wp_register_script('DB_plugin_sortable_js', DB_ASSETS.'js/sortable.js');
			wp_enqueue_script('DB_plugin_sortable_js');
			wp_register_script('DB_js', DB_ASSETS.'js/db-js.js');
			wp_enqueue_script('DB_js');
			
			wp_localize_script('DB_js', 'db_var', 
				array(
					//To use this variable in javascript use "youruniquejs_vars.ajaxurl"
					'ajaxurl' => admin_url('admin-ajax.php'),
					//To use this variable in javascript use "youruniquejs_vars.the_issue_key"
					'the_issue_key' => $the_issue_key,
				) 
			); 

			return;
		}
		
		/**
		 * register & active css files
		 *
		 * @return:	void
		 */
		protected function add_css(){

			wp_register_style('DB_css', DB_ASSETS.'css/db-style.css'); 
			wp_enqueue_style('DB_css');

			return;

		}
	}
}

?>