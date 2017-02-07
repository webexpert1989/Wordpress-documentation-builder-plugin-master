<?php
/**************************************************************
 *
 * global class for documentation buider plugin
 *
 * @package			 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-global.php
 * @author	 		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_global is available
if(!class_exists('DB_global')){

	// CREATE A PACKAGE CLASS
	class DB_global{
		
		// constuct
		function __construct(){
			return;
		}
		

		/**
		 * remove directory and sub directory & files
		 * 
		 * @param:  string  $dir - directory url
		 * @return: bool    Returns true on success, false on failure
		 */
		public function delTree($dir){ 
			$files = array_diff(scandir($dir), array('.','..')); 
			foreach ($files as $file){ 
				if(is_dir($dir.'/'.$file)){
					self::delTree($dir.'/'.$file);
				} else {
					unlink($dir.'/'.$file); 
				}
			} 
			return rmdir($dir); 
		}


		/**
		 * Copy a file, or recursively copy a folder and its contents
		 *
		 * @param:	string  $source -		Source path
		 * @param:	string  $dest -		    Destination path
		 * @param:	string  $permissions -  New folder creation permissions
		 * @return:	bool    Returns true on success, false on failure
		 */
		public function xcopy($source, $dest, $permissions = 0755){
			
			/// repeat
			function xcopy_repeat($source, $dest, $permissions = 0755){
				// Check for symlinks
				if (is_link($source)) {
					return symlink(readlink($source), $dest);
				}

				// Simple copy for a file
				if (is_file($source)) {
					return copy($source, $dest);
				}

				// Make destination directory
				if (!is_dir($dest)) {
					mkdir($dest, $permissions);
				}

				// Loop through the folder
				$dir = dir($source);
				while (false !== $entry = $dir->read()) {
					// Skip pointers
					if ($entry == '.' || $entry == '..') {
						continue;
					}

					// Deep copy directories
					xcopy_repeat($source.'/'.$entry, $dest.'/'.$entry, $permissions);
				}

				// Clean up
				$dir->close();
			}
			///////////
			xcopy_repeat($source, $dest, $permissions = 0755);
			
			////////////
			return true;
		}
		

		/**
		 * create zip file from directory
		 *
		 * @param:  string  $rootPath -    Source path
		 * @param:  string  $archiveName - new zip file name
		 * @return:	bool	Returns true on success, false on failure
		 */
		public function zip_archive($zip_path = '', $zip_file = 'zip_file.zip'){
			
			$za = new FlxZipArchive;
			$res = $za->open($zip_file, ZipArchive::CREATE);
			if($res === TRUE) 
			{
				$za->addDir($zip_path, basename($zip_path));
				return $za->close();
			}
			else{
				return false;
			}
		}

		
		/**
		 * create directory
		 *
		 * @param:  string  $dirPath - new directory path
		 * @return: bool    Returns true on success, false on failure
		 */
		public function new_dir($dirPath = ''){

			if(is_dir($dirPath)){
				self::delTree($dirPath);
			}
			
			mkdir($dirPath, 0755, true);
			
			return is_dir($dirPath);
		}

		
		/**
		 * create file
		 *
		 * @param:  string  $filePath -      new file path
		 * @param:  string  $file_contents - new file contents
		 * @return:	bool    Returns true on success, false on failure
		 */
		public function new_file($filePath = '', $file_contents = ''){

			$f = @fopen($filePath, "w");
			if(!$f){
				return false;
			}

			fwrite($f, $file_contents);
			fclose($f);

			// close the zip file
			return is_file($filePath);
		}


		/**
		 * read file as url
		 *
		 * @param:	string  $file - file alias URL
		 * @return:	string  Returns file content on success, false on failure
		 */
		public function read_file($file){
			if(!is_file($file)){
				return false;
			}

			// get file contents
			return file_get_contents($file);
		}


		/**
		 * convert array to XML
		 *
		 * @param:	array   $array - array to convert
		 * @param:	string  $node_name - root node tag name
		 * @return:	string  Returns xml string on success, false on failure
		 */
		public function array2xml($data = array(), $xml_path = '', $node_name = 'truewordpress'){

			function array_to_xml($data, &$xml_data){
				foreach($data as $key => $value){
					if(is_array($value)){
						if(is_numeric($key)){
							$key = 'item'; //dealing with <0/>..<n/> issues
						}
						$subnode = $xml_data->addChild($key);
						array_to_xml($value, $subnode);
					} else {
						$xml_data->addChild($key, htmlspecialchars($value));
					}
				 }
			}

			// creating object of SimpleXMLElement
			$xml_data = new SimpleXMLElement('<?xml version="1.0"?><'.$node_name.'></'.$node_name.'>');

			// function call to convert array to xml
			array_to_xml($data, $xml_data);

			//saving generated xml file; 
			return $xml_data->asXML($xml_path);
		}
		

		/**
		 * display notice in admin section
		 *
		 * @param:	array  $n - notice array
		 * @return:	void
		 */
		public function admin_notice($n = array()){
			if(empty($n)){
				return false;
			}
			
			$msg = '';

			// notice type: update-nag, updated, error
			foreach($n as $k => $v){
				$msg .= '
					<div class = "'.$k.' notice is-dismissible">
						<p>'.$v.'</p>

						<button type = "button" class = "notice-dismiss">
							<span class = "screen-reader-text">Dismiss this notice.</span>
						</button>
					</div>
				';
			}
				
			// print messagaes
			echo $msg;

			return;
		}
		

		/**
		 * redirect to another url
		 *
		 * @param:	string  $url - redirect url
		 * @return:	void
		 */
		public function redirect($url = ''){
			echo '<script>window.location.href = "'.$url.'"</script>';

			return;
		}

	}
}

?>