<?php
/**************************************************************
 *
 * menu class for documentation list admin page 
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-menu.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_admin_menu is available
if(!class_exists('DB_admin_menu')){

	// CREATE A PACKAGE CLASS
	class DB_admin_menu{
		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			return;
		}
		
		/**
		 * add top level menus in wordpress admin page
		 *
		 * @return:	void
		 */
		public function add_toplevel_menu(){

			global $menu;

			// Default menu positioning
			$position = '100.1';

			// If enabled, relocate the plugin menus higher
			if(apply_filters('DB_relocate_menus', __return_true())){

				for($position = '40.1'; $position <= '100.1'; $position += '0.1'){

					// Ensure there is a space before and after each position we are checking, leaving room for our separators.
					$before = $position - '0.1';
					$after  = $position + '0.1';

					// Do the checks for each position. These need to be strings, hence the quotation marks.
					if(isset($menu[ "$position" ])){
						continue;
					}
					if(isset($menu[ "$before" ])){
						continue;
					}
					if(isset($menu[ "$after" ])){
						continue;
					}

					// If we've successfully gotten this far, break the loop. We've found the position we need.
					break;
				}
			}

			// page class
			$page = new DB_page();

			// page ID settings
			global $DB_conf;

			// add top level menu
			add_menu_page(
				__('Documentation Builder', 'truewordpress'), 
				__('Documentation Builder', 'truewordpress'), 
				'administrator', 
				$DB_conf['pages']['list'], 
				null, 
				'dashicons-book-alt', 
				$position
			);

			// add sub menus
			add_submenu_page(
				'DB-admin', 
				__('Documentations', 'truewordpress'),
				__('Documentations', 'truewordpress'), 
				'administrator', 
				$DB_conf['pages']['list'], 
				array($page, 'page_list')
			);

			add_submenu_page(
				'DB-admin',
				__('Add New a Documentation', 'truewordpress'), 
				__('Add New', 'truewordpress'),
				'administrator',
				$DB_conf['pages']['new'],
				array($page, 'page_new')
			);
			add_submenu_page(
				'DB-admin', 
				__('Settings', 'truewordpress'),
				__('Settings', 'truewordpress'), 
				'administrator', 
				$DB_conf['pages']['settings'],
				array($page, 'page_settings')
			);
			add_submenu_page(
				'DB-admin', 
				__('Get Documentation', 'truewordpress'), 
				__('Get Documentation', 'truewordpress'), 
				'administrator', 
				$DB_conf['pages']['export'],
				array($page, 'page_export')
			);

			// Do action allowing extension to add their own toplevel menus
			do_action('DB_add_toplevel_menu', $position);

			// Add the menu separators if menus have been relocated (they are by default). Quotations marks ensure these are strings.
			if(apply_filters('DB_relocate_menus', __return_true())){
				$this->add_menu_separator("$before");
				$this->add_menu_separator("$after");
			}
			
			return;
		}
		
		/**
		 * Create a separator in the admin menus, above and below our plugin menus
		 *
		 * @param  string $position The menu position to insert the separator
		 * @return void
		 */
		protected function add_menu_separator($position = '40.1'){

			global $menu;

			$index = 0;
			foreach($menu as $offset => $section){

				if('separator' == substr($section[2], 0, 9)){
					$index++;
				}

				if($offset >= $position){

					// Quotation marks ensures the position is a string. Integers won't work if we are using decimal values.
					$menu[ "$position" ] = array('', 'read', "separator{$index}", '', 'wp-menu-separator');
					break;
				}				
			}

			ksort($menu);

			return;
		}
	}
}

?>