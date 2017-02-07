<?php
/**************************************************************
 *
 * page driving class for documentation list admin page 
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-menu.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_page is available
if(!class_exists('DB_page')){

	// CREATE A PACKAGE CLASS
	class DB_page{
		
		// custom database class object
		var $get_db;
		
		// global page settings class object
		var $get_pages;

		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			$this->get_db = new DB_db();
			
			global $DB_conf;
			$this->get_pages = $DB_conf['pages'];

			return;
		}
		
		/**
		 * list page
		 *
		 * @return:	void
		 */
		public function page_list(){
			switch($_REQUEST['action']){
				case 'edit':
					$this->page_list_edit();
					break;

				case 'export':
					DB_global::redirect('admin.php?page='.$this->get_pages['export'].'&doc='.$_REQUEST['doc']);
					break;

				case 'del':
					$this->page_list_del();
					break;

				default:
					$this->page_list_view();
					break;
			}
			
			return;
		}
		
		/**
		 * list page - list view items
		 *
		 * @return:	void
		 */
		protected function page_list_view(){
			// search MySql query
			$s = isset($_GET['s'])? ' doc_name like "%'.strtolower($_GET['s']).'%" OR doc_desc like "%'.strtolower($_GET['s']).'%"': '';
			
			// print list table
			DB_page_list($this->get_db->all(null, $s), $this->get_db->total(null, $s));

			return;
		}
				
		/**
		 * list page - edit
		 *
		 * @return:	void
		 */
		protected function page_list_edit(){
			// doc ID to edit doc
			$doc_id = isset($_REQUEST['doc'])? $_REQUEST['doc']: -1;
			
			// get form datas
			$doc_data = $this->get_db->row(null, $doc_id);
			
			if(empty($doc_data)){
				DB_global::admin_notice(array('error' => __('Sorry, could`t find detail info to edit documentation!', 'truewordpress')));
				
				// display list table after deleted doc
				$this->page_list_view();

			} else {
				// print form
				DB_page_edit($doc_data);
			}
			
			return;
		}
		
		/**
		 * list page - delete items
		 *
		 * @return:	void
		 */
		protected function page_list_del(){
			// doc ID to delete doc
			$doc_ids = isset($_REQUEST['doc'])? $_REQUEST['doc']: -1;

			if(is_array($doc_ids)){
				foreach($doc_ids as $id){
					$this->del_list_item($id);
				}
			} else {
				$this->del_list_item($doc_ids);
			}

			// display list table after deleted doc
			$this->page_list_view();

			return;
		}

		/**
		 * delete one item & row from database
		 *
		 * @param:	int  $doc_id - row ID
		 * @return:	void
		 */
		protected function del_list_item($doc_id){
			$doc_data = $this->get_db->row(null, $doc_id);

			if($doc_data){
				// delete row
				if($this->get_db->delete(array(
					'table' => DB_DB_DOCS,
					'where' => array('ID' => $doc_data->ID)
				))){
					DB_global::admin_notice(array('updated' => __('Documentation `'.$doc_data->doc_name.'` is deleted successfully!', 'truewordpress')));
				} else {				
					DB_global::admin_notice(array('error' => __('Sorry, couldn`t delete documentation correctly, Try again later!', 'truewordpress')));
				}
			} else {
				DB_global::admin_notice(array('error' => __('no documentation!', 'truewordpress')));
			}

			return;
		}

		/**
		 * add new page
		 *
		 * @return:	void
		 */
		public function page_new(){
			if(!isset($_GET['sub']) || $_GET['sub'] != 'import'){
				DB_global::admin_notice(array('update-nag' => __('Please save the documentation first of all and edit, if you want to use auto-save function. <br/> Thank you!', 'truewordpress')));
			}
			
			// upload new doc
			if(isset($_FILES['new_doc']) && $_FILES['new_doc']){
				DB_settings::import_new_doc($_FILES['new_doc']);
			}

			DB_page_new();

			return;
		}

		/**
		 * settings page
		 *
		 * @return:	void
		 */
		public function page_settings(){
			// upload new skin
			if(isset($_FILES['new_skin']) && $_FILES['new_skin']){
				DB_settings::import_new_skin($_FILES['new_skin']);
			}
			
			// get skin style.css file contents to edit skin
			if(isset($_GET['skin']) && $_GET['skin']){
				$skin_style = DB_settings::get_skin_style($_GET['skin']);
			}

			DB_page_settings($skin_style);

			return;
		}

		/**
		 * export page
		 *
		 * @return:	void
		 */
		public function page_export(){
			// doc ID to delete doc
			$doc_id = isset($_REQUEST['doc'])? $_REQUEST['doc']: -1;

			if($doc_id > -1){
				DB_page_export($this->get_db->row(null, $doc_id));
			} else {
				DB_global::redirect('admin.php?page='.$this->get_pages['list']);
			}

			return;
		}
	}
}

?>