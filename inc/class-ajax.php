<?php
/**************************************************************
 *
 * ajax response class for documentation buider plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-ajax.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @info: error code - 100x: GET & POST errors, no fields
 *                     200x: UPDATE faild errors
 *                     300x: INSERT faild errors
 *                     400x: DELETE faild errors
 *                     500x: DOWNLOAD ZIP FILE errors
 *                     900x: SELECT faild errors
 *
 **************************************************************/


// check DB_ajax is available
if(!class_exists('DB_ajax')){

	// CREATE A PACKAGE CLASS
	class DB_ajax{

		// DB database object
		var $db_obj = '';
		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			$this->db_obj = new DB_db;

			return;
		}
					
		/**
		 * update doc 
		 *
		 * @return:	void
		 */
		public function doc_edit(){ 

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1002,
					'error_txt' => __('no POST fields!', 'truewordpress'),
				));
				exit;
			}
			$post = $_POST;

			/////////////
			$the_issue_key = $post['the_issue_key'];

			// get editor info
			global $current_user;
			get_currentuserinfo();

			// insert data to DB
			if($this->db_obj->update(array(
				'table' => DB_DB_DOCS,
				'data' => array(
					'doc_logo' =>		$post['logo'],
					'doc_name' =>		$post['title'],
					'doc_desc' =>		base64_encode($post['desc']),
					'doc_shortcode' =>	DB_shortcode::get_doc_shortcode($post['id']),
					'doc_content' =>	base64_encode($post['doc']),
					'date' =>			date('Y-m-d H:i:s'),
					'author' =>			$current_user->user_login,
					'skin' =>			$post['skin'],
					'status' =>			true
				),
				'where' => array(
					'ID' => $post['id']
				)
			))){
				// succss
				echo json_encode(array(
					'success' => true,
					'success_txt' => __('Documentation is updated successfully!', 'truewordpress'),
					'doc_id'  => $post['id']
				));
			} else {
				// not udpated
				echo json_encode(array(
					'error' => 2001,
					'error_txt' => __('Sorry, couldn`t update documentation correctly, Try again later!', 'truewordpress')	
				));			}
			
			exit;
		}
				
		/**
		 * add new doc 
		 *
		 * @return:	void
		 */
		public function doc_new(){

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1003,
					'error_txt' => __('no POST fields!', 'truewordpress'),
				));
				exit;
			}
			$post = $_POST;
			
			/////////////
			$the_issue_key = $post['the_issue_key'];

			// get editor info
			global $current_user;
			get_currentuserinfo();

			// insert data to DB
			$insert_id = $this->db_obj->insert(array(
				'table' => DB_DB_DOCS,
				'data' => array(
					'doc_logo' =>		$post['logo'],
					'doc_name' =>		$post['title'],
					'doc_desc' =>		base64_encode($post['desc']),
					'doc_shortcode' =>	0,
					'doc_content' =>	base64_encode($post['doc']),
					'date' =>			date('Y-m-d H:i:s'),
					'author' =>			$current_user->user_login,
					'skin' =>			$post['skin'],
					'status' =>			false
				)
			));
			
			// insert new doc data to table
			if(!$insert_id){
				echo json_encode(array(
					'error' => 3001,	
					'error_txt' => __('Sorry, couldn`t save new documentation correctly, Try again later!', 'truewordpress')	
				));
				exit;
			}
			
			// update shortcode field by new doc id
			if($this->db_obj->update(array(
				'table' => DB_DB_DOCS,
				'data' => array(
					'doc_shortcode' =>	DB_shortcode::get_doc_shortcode($insert_id),
					'status' =>			true
				),
				'where' => array(
					'ID' => $insert_id
				)
			))){
				// succss
				echo json_encode(array(
					'success' => true,
					'success_txt' => __('New documentation is saved successfully!', 'truewordpress'),
					'doc_id'  => $insert_id
				));
			} else {
				// not udpated
				echo json_encode(array(
					'error' => 2002,	
					'error_txt' => __('Sorry, couldn`t save new documentation correctly, Try again later!', 'truewordpress')	
				));
			}

			//////////
			exit;
		}
		
		/**
		 * delete doc 
		 *
		 * @return:	void
		 */
		public function doc_del(){ 

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1004,
					'error_txt' => __('no POST fields!', 'truewordpress'),
				));
				exit;
			}
			$post = $_POST;
			
			/////////////
			$the_issue_key = $post['the_issue_key'];

			// delete doc
			if($this->db_obj->delete(array(
				'table' => DB_DB_DOCS,
				'where' => array(
					'ID' => $post['id']
				)
			))){
				// succss
				echo json_encode(array(
					'success' => true,
					'success_txt' => __('Documentation is deleted successfully!', 'truewordpress'),
					'doc_id'  => $insert_id
				));
			} else {
				// not deleted
				echo json_encode(array(
					'error' => 4002,	
					'error_txt' => __('Sorry, couldn`t delete documentation correctly, Try again later!', 'truewordpress')	
				));
			}
			exit;
		}

		
		/**
		 * download doc with zip file type
		 *
		 * @return:	void
		 */
		public function doc_export(){ 

			// check POST fields
			if(empty($_POST) || !$_POST['id']){
				// succss
				echo json_encode(array(
					'error' => 1005,
					'error_txt' => __('no POST fields!', 'truewordpress'),
				));
				exit;
			}
			$doc_id = $_POST['id'];
			$export_type = isset($_POST['type'])? 'get_'.$_POST['type']: 'get_zip';
			
			/////////////
			$the_issue_key = $post['the_issue_key'];

			// get zip class
			$file = new DB_export($this->db_obj->row(null, $doc_id));
			$file_path = $file->$export_type();

			if($file_path){
				// succss			
				echo json_encode(array(
					'success' => true,
					'success_txt' => $file_path
				));
			} else {				
				// not deleted
				echo json_encode(array(
					'error' => 5002,	
					'error_txt' => __('Sorry, couldn`t create documentation correctly, Try again later!', 'truewordpress')	
				));
			}
			exit;
		}

		
		/**
		 * settings
		 *
		 * @return:	void
		 */
		public function doc_settings(){ 

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1006,
					'error_txt' => __('no POST fields!', 'truewordpress'),
				));
				exit;
			}
			$post = $_POST;
			
			/////////////
			$the_issue_key = $post['the_issue_key'];

			// settings
			global $DB_conf;

			$DB_settings = get_option('DB_settings');

			$noAction = false;
			switch($post['type']){
				case 'reset':
					$DB_settings['rows_per_page'] = $DB_conf['rows_per_page'];
					$DB_settings['auto_save'] = $DB_conf['auto_save'];

					break;

				case 'save':
					if((int)$post['rows_per_page'] == $DB_settings['rows_per_page'] && $DB_settings['auto_save'] == (int)$post['auto_save']){
						$noAction = true;
					} else {
						$DB_settings['rows_per_page'] = (int)$post['rows_per_page']? (int)$post['rows_per_page']: $DB_conf['rows_per_page'];
						$DB_settings['auto_save'] = (int)$post['auto_save'];
					}

					break;

				default:
					echo json_encode(array(
						'error' => 1006,
						'error_txt' => __('no POST fields!', 'truewordpress'),
					));
					exit;

					break;
			}

			///////////
			if($noAction || update_option('DB_settings', $DB_settings)){
				// succss			
				echo json_encode(array(
					'success' => true,
					'success_txt' => __('Settings is saved successfully!', 'truewordpress'),
				));
			} else {				
				// not deleted
				echo json_encode(array(
					'error' => 6002,	
					'error_txt' => __('Sorry, couldn`t update settings correctly, Try again later!', 'truewordpress')	
				));
			}
			exit;
		}

		
		/**
		 * skin settings
		 *
		 * @return:	void
		 */
		public function doc_skin(){ 

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1007,
					'error_txt' => __('no POST fields!', 'truewordpress'),
				));
				exit;
			}
			$post = $_POST;
			
			/////////////
			$the_issue_key = $post['the_issue_key'];

			// settings
			switch($post['type']){
				case 'update':
					if(DB_settings::update_skin($post['skin'], $post['contents'])){
						// succss			
						echo json_encode(array(
							'success' => true,
							'success_txt' => __('Seleted skin is updated successfully!', 'truewordpress'),
						));
					} else {				
						// not deleted
						echo json_encode(array(
							'error' => 7002,	
							'error_txt' => __('Sorry, couldn`t update selected skin correctly, Try again later!', 'truewordpress')	
						));
					}
					
					break;

				case 'delete':
					if(DB_settings::delete_skin($post['skin'])){
						// succss			
						echo json_encode(array(
							'success' => true,
							'success_txt' => __('Seleted skin is deleted successfully!', 'truewordpress'),
						));
					} else {				
						// not deleted
						echo json_encode(array(
							'error' => 7002,	
							'error_txt' => __('Sorry, couldn`t delete selected skin correctly, Try again later!', 'truewordpress')	
						));
					}
					break;

				default:
					echo json_encode(array(
						'error' => 1007,
						'error_txt' => __('no POST fields!', 'truewordpress'),
					));
					break;
			}
			exit;
		}
	}
}

?>