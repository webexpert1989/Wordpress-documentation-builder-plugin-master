<?php
/**************************************************************
 *
 * settings class for documentation buider plugin
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-settings.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_settings is available
if(!class_exists('DB_settings')){

	// CREATE A PACKAGE CLASS
	class DB_settings{
				
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			return;
		}
		
		/**
		 * get skins
		 *
		 * @return:	array Result - skins info array
		 */
		public function get_skins($noDefault = false){ 
			$readed = self::read_skins();

			if($noDefault){
				unset($readed['default']);
			}

			// reutrn result
			return $readed;
		}

		/**
		 * read skins files
		 *
		 * @return:	array Result - skins array
		 */
		public function read_skins(){
			$dirs = array_diff(scandir(DB_SKINS), array('.','..')); 
			
			$skins = array();
			foreach ($dirs as $dir){ 
				$info_file = DB_SKINS.$dir.'/style.css';
				if(is_file($info_file)){
					$info = explode("\n", file_get_contents($info_file));
					$skin_name = trim(str_replace('name:', '', str_replace('*', '', $info[1])));

					$skins[$dir] = array(
						'label' => $skin_name
					);
				} else {				
					$skins[$dir] = array(
						'label' => $dir
					);
				}
			}

			////
			return $skins;
		}

		/**
		 * import new skins files
		 *
		 * @return:	bool, true if success
		 */
		public function import_new_skin($file = array()){
			$path_parts = pathinfo($file['name']);
			$extension = $path_parts['extension'];

			if($extension != 'zip'){
				DB_global::admin_notice(array('error' => __('Sorry, upload correct skin file.', 'truewordpress')));
				return false;
			} else {
				$skin_file = DB_SKINS.$file['name'];
				move_uploaded_file($file['tmp_name'], $skin_file);

				if(is_file($skin_file)){
					WP_filesystem();

					if(@unzip_file($skin_file, DB_SKINS)){
						unlink($skin_file);

						DB_global::admin_notice(array('updated' => __('Installed new skin successfully.', 'truewordpress')));
						return true;
					} else {
						DB_global::admin_notice(array('error' => __('Sorry, couldn`t install new skin correclty, Try again later!', 'truewordpress')));
						return false;
					}
				} else {
					DB_global::admin_notice(array('error' => __('Sorry, couldn`t upload new skin correclty, Try again later!', 'truewordpress')));
					return false;
				}
			}

			return;
		}

		/**
		 * get contents of style.css of selected skin
		 *
		 * @return:	string, file contents if success
		 */
		public function get_skin_style($skin = ''){
			return DB_global::read_file(DB_SKINS.$skin.'/style.css');
		}

		/**
		 * delete skin
		 *
		 * @return:	bool, true if deleted correctly
		 */
		public function delete_skin($skin = ''){

			$db = new DB_db();
			if($db->total(null, ' `skin` = "'.$skin.'"')){
				if(!$db->update(array(
					'table' => DB_DB_DOCS,
					'data' => array(
						'skin' => 'default',
					),
					'where' => array(
						'skin' => $skin
					)
				))){
					return false;
				}
			}

			DB_global::delTree(DB_SKINS.$skin);

			return !is_dir(DB_SKINS.$skin);
		}

		/**
		 * update skin
		 *
		 * @return:	bool, true if updated correctly
		 */
		public function update_skin($skin = '', $contents = ''){

			if(DB_global::new_file(DB_SKINS.$skin.'/style.css', $contents)){
				return true;
			} else {
				return false;
			}
		}

		/**
		 * import new doc
		 *
		 * @return:	bool, true if success
		 */
		public function import_new_doc($file = array()){
			$path_parts = pathinfo($file['name']);
			$extension = $path_parts['extension'];
			
			$doc = "";
			switch($extension){
				case 'json':
					$doc = json_decode(file_get_contents($file['tmp_name']));
					$doc->doc_content = ''.base64_encode(addslashes(json_encode($doc->doc_content)));
					break;

				default:
					DB_global::admin_notice(array('error' => __('Sorry, upload *.json or *.xml file to add new documentation.', 'truewordpress')));
					return false;
					break;
			}

			// get editor info
			global $current_user;
			get_currentuserinfo();

			// insert data to DB
			$db_db = new DB_db();
			$insert_id = $db_db->insert(array(
				'table' => DB_DB_DOCS,
				'data' => array(
					'doc_logo' =>		$doc->doc_logo,
					'doc_name' =>		$doc->doc_name,
					'doc_desc' =>		base64_encode($doc->doc_desc),
					'doc_shortcode' =>	0,
					'doc_content' =>	$doc->doc_content,
					'date' =>			date('Y-m-d H:i:s'),
					'author' =>			$current_user->user_login,
					'skin' =>			$doc->skin,
					'status' =>			false
				)
			));
			
			// insert new doc data to table
			if(!$insert_id){
				DB_global::admin_notice(array('error' => __('Sorry, couldn`t save new documentation correctly, Try again later!', 'truewordpress')));
				return false;
			}
			
			// update shortcode field by new doc id
			if($db_db->update(array(
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
				DB_global::admin_notice(array('updated' => __('New documentation is saved successfully!', 'truewordpress')));
				return true;
			} else {
				// not udpated
				DB_global::admin_notice(array('error' => __('Sorry, couldn`t save new documentation correctly, Try again later!', 'truewordpress')));
				return false;
			}

			return true;
		}

		/**
		 * convert xml object to object array
		 * remove 'item' if key have 'item' string, and push new array
		 *
		 * @return:	array, object array if success
		 */
		public function xml2array($xmlObject, $out = array()){
			foreach((array) $xmlObject as $index => $node){
				$out[$index] = (is_object($node))? self::xml2array($node): $node;
			}

			return $out;
		}
	}
}

  
?>