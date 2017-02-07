<?php
/**************************************************************
 *
 * shortcode class for documentation list admin page 
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-shortcode.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_shortcode is available
if(!class_exists('DB_shortcode')){

	// CREATE A PACKAGE CLASS
	class DB_shortcode{
		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			return;
		}

		/**
		 * make shortcode by row ID
		 *
		 * @param:	int  $id - row id of table
		 * @return:	string  Returns shortcode string on success, false on failure 
		 */
		public function get_doc_shortcode($id = 0){
			if($id){
				return '['.DB_SHORTCODE.' id='.$id.']';
			} else {
				return '';
			}
		}

		/**
		 * get documentation html code to display in the page from entered shortcode
		 *
		 * @param:	array   $attr - shortcode attributes
		 * @param:	string  $content - shortcode contents
		 * @return:	string  Returns html on success, false on failure 
		 */
		public function doc_shortcode($attr, $content = null){
			if(empty($attr['id'])){
				DB_global::admin_notice(array('error' => __('Sorry, could`t get documentation ID to get documentation form!', 'truewordpress')));
				return false;
			}

			// get doc info
			$get_db = new DB_db();
			$doc_data = $get_db->row(null, $attr['id']);
			
			if(empty($doc_data)){
				DB_global::admin_notice(array('error' => __('Sorry, could`t find detail info to get documentation form!', 'truewordpress')));
				return false;
			} else {
				$doc_contents = new DB_export($doc_data);
				print_r(do_shortcode($doc_contents->get_html_contents(true)));
			}
		}
	}
}

?>