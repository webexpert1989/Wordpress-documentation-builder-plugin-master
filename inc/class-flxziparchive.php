<?php
/**************************************************************
 *
 * ZIP library
 *
 * @package			 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-flxziparchive.php
 * @author	 		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

if(!class_exists('ZipArchive')){
	die('sorry, your PHP version haven`t ZipArchive class.');
}

// check FlxZipArchive is available
if(!class_exists('FlxZipArchive')){

	class FlxZipArchive extends ZipArchive{
		
		/**
		 * create sub directory to zip file
		 *
		 * @param:  string  $location	- sub directory location
		 * @param:  string  $name		- sub directory name
		 * @return: void
		 */
		public function addDir($location, $name){
			$this->addEmptyDir($name);
			$this->addDirDo($location, $name);

			return;
		} 		
		
		/**
		 * add directory to zip file
		 *
		 * @param:  string  $location	- sub directory location
		 * @param:  string  $name		- sub directory name
		 * @return: void
		 */
		private function addDirDo($location, $name){
			$name .= '/';
			$location .= '/';

			$dir = opendir ($location);
			while ($file = readdir($dir)){
				if($file == '.' || $file == '..'){
					continue;
				}

				$do = (filetype($location . $file) == 'dir')? 'addDir': 'addFile';
				$this->$do($location.$file, $name.$file);
			}

			return;
		} 
	}
}



?>