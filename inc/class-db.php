<?php
/**************************************************************
 *
 * DB class for documentation buider plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-db.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_global is available
if(!class_exists('DB_db')){

	// CREATE A PACKAGE CLASS
	class DB_db{
		
		// db object
		var $db;

		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			global $wpdb;
			
			$this->db = $wpdb;
			return;
		}
		
		/**
		 * make error log
		 *
		 * @return:	void
		 */
		protected function logger($error = ''){
			if(WP_DEBUG_LOG){
				if ( is_array($error) || is_object($error) ){
					error_log(print_r($error, true));
				} else {
					error_log($error);
				}
			}
			return;
		}

		/**
		 * check current table
		 *
		 * @param:	string  $table_name - table name
		 * @return:	void
		 */
		protected function check_table($table_name = null){
			if(empty($table_name)){
				$message = sprintf(__('Table name is not specified when the method "%s" call.', CDBT), __FUNCTION__);
				$this->logger($message);
				return false;
			}

			$result = $this->db->get_var($this->db->prepare("SHOW TABLES LIKE %s", $table_name));

			return $table_name === $result;
		}
		
		/**
		 * get all datas
		 *
		 * @param:	string  $table - table ID, conf: configuration table, default: doc table
		 * @param:	string  $s     - search MySql query
		 * @return:	object array  Returns results on success, false on failure
		 */
		public function all($table = '', $s = ''){

			$query = '';
			switch($table){
				case 'conf':
					$query = 'SELECT * FROM `'.DB_DB_CONF.'` WHERE 1 AND';
					break;
				default:
					$query = 'SELECT * FROM `'.DB_DB_DOCS.'` WHERE status = 1 AND ';
					break;
			}

			$query .= $s? '('.$s.')': '1';
			return $this->db->get_results($query);
		}
				
		/**
		 * get all docs info only
		 *
		 * @param:	string  $table - table ID, conf: configuration table, default: doc table
		 * @param:	string  $s     - search MySql query
		 * @return:	int     Returns total rows number on success, false on failure
		 */
		public function total($table = '', $s = ''){
			
			$query = '';
			switch($table){
				case 'conf':
					$query = 'SELECT COUNT(*) FROM `'.DB_DB_CONF.'` WHERE 1 AND';
					break;
				default:
					$query = 'SELECT COUNT(*) FROM `'.DB_DB_DOCS.'` WHERE status = 1 AND ';
					break;
			}
			
			$query .= $s? '('.$s.')': '1';
			return  $this->db->get_var($query);
		}

		/**
		 * get one row
		 *
		 * @param:	string  $table - table ID, conf: configuration table, default: doc table
		 * @param:	int     $id    - row ID
		 * @return:	object  Returns row object on success, false on failure
		 */
		public function row($table = '', $id = -1){

			$query = '';
			switch($table){
				case 'conf':
					$query = 'SELECT * FROM `'.DB_DB_CONF.'` WHERE 1 AND';
					break;
				default:
					$query = 'SELECT * FROM `'.DB_DB_DOCS.'` WHERE status = 1 AND ';
					break;
			}

			$query .= '(ID = "'.$id.'")';
			return $this->db->get_row($query);
		}

		/**
		 * insert data to table
		 *
		 * @param:	array   $d - insert data, 'table': table name, 'data': data array, 'format': field's type
		 * @return:	int     Returns added last row ID on success, false on failure
		 */
		public function	insert($d = array()){

			if(!empty($d) && $this->check_table($d['table'])){
				$this->db->insert( 
					$d['table'], 
					$d['data'], 
					(isset($d['format'])? $d['format']: null)
				);

				return $this->db->insert_id? $this->db->insert_id: false;
			}

			return false;
		}

		/**
		 * update data
		 *
		 * @param:	array   $d - insert data, 'table': table name, 'data': data array, 'where': where query, 'format': field's type, 'where_format': where fields type
		 * @return:	bool    Returns true on success, false on failure
		 */
		public function	update($d = array()){

			if(!empty($d) && $this->check_table($d['table'])){

				return $this->db->update( 
					$d['table'], 
					$d['data'], 
					$d['where'],
					(isset($d['format'])? $d['format']: null),
					(isset($d['where_format'])? $d['where_format']: null)
				);
			}

			return false;
		}

		/**
		 * delete data
		 *
		 * @param:	array   $d - insert data, 'table': table name, 'where': where query, 'where_format': where fields type
		 * @return:	bool    Returns true on success, false on failure
		 */
		public function	delete($d = array()){

			if(!empty($d) && $this->check_table($d['table'])){

				return $this->db->delete( 
					$d['table'], 
					$d['where'],
					(isset($d['where_format'])? $d['where_format']: null)
				);
			}

			return false;
		}
	}
}

?>