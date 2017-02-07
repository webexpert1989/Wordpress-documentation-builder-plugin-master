<?php
/**************************************************************
 *
 * export documentation class
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 inc/class-shortcode.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// check DB_export is available
if(!class_exists('DB_export')){

	// CREATE A PACKAGE CLASS
	class DB_export{
		
		// basic data to export doc
		var $doc = '';

		// root directory info
		var $save_dir = '';
		var $save_file = '';
		var $download_file = '';
		var $download_url = '';
		
		// template url
		var $skin = '';
		var $skin_url = '';
		
		// template contents
		var $tpl = '';
		var $tpl_sub = array(
			'header'	=> '',
			'footer'	=> '',
			'menu'		=> '',
			'contents'	=> '',
		);
		
		// generated contents
		var $cnts = '';

		/**
		 * construct for Export Class
		 *
		 * @param: $doc_data - documentation data of Database
		 */
		function __construct($doc_data = ''){
			if(!$doc_data){
				return false;
			}

			//////
			$this->doc = $doc_data;
			$this->skin = $this->doc->skin? $this->doc->skin: 'default';
			$this->skin_url = DB_SKINS.$this->skin.'/';

			$this->save_dir = DB_ALIAS.$this->doc->ID.'/';
			$this->save_file = $this->save_dir.'index.html';

			$this->download_url = DB_DOWNLOAD.$this->doc->ID.'/';			
			$this->download_file = 'documentation_'.date('Y_m_d_H_i_s');

			return;
		}

		/**
		 * get html form contents
		 *
		 * @return string - htmls contents
		 */
		public function get_html_contents($shortcode = false){
						
			// import wordpress skin engine
			$this->get_import();
			
			// get templates
			$this->get_template();

			// get contents
			$this->get_contents($shortcode);
			
			// return html contents
			return $this->cnts;
		}

		/**
		 * get html zip file
		 *
		 * @return string - zip file path
		 */
		public function get_html(){
						
			// get html contents
			$this->get_html_contents();

			$this->cnts = do_shortcode($this->cnts);
			
			// make zip file and return zip file url to download doc file
			return $this->export_zip();
		}

		/**
		 * get text file
		 *
		 * @return string - text file path
		 */
		public function get_txt(){
			// make text file and return text file url to download doc file
			return $this->export_txt();
		}

		/**
		 * get json file
		 *
		 * @return string - json file path
		 */
		public function get_json(){
			// make json file and return json file url to download doc file
			return $this->export_json();
		}

		/**
		 * get xml file
		 *
		 * @return string - xml file path
		 */
		public function get_xml(){
			// make xml file and return xml file url to download doc file
			return $this->export_xml();
		}

		/**
		 * import wordpress skin engine
		 */
		protected function get_import(){
			$import = $this->skin_url.'skin.php';
			if(is_file($import)){
				@include($import);
			}

			return;
		}

		/**
		 * import wordpress shortcode function
		 */
		protected function get_import_shortcode(){
			$import = $this->skin_url.'shortcode.php';
			if(is_file($import)){
				@include($import);
			}

			return;
		}

		/**
		 * get contents
		 */
		protected function get_contents($shortcode = false){
			
			// embed documentation data to main template
			$this->cnts = $this->embed_data($this->tpl);

			// integrating sub templates
			$this->cnts = preg_replace('/\[\[header\]\]/', $this->embed_data($this->tpl_sub['header']), $this->cnts);
			$this->cnts = preg_replace('/\[\[footer\]\]/', $this->embed_data($this->tpl_sub['footer']), $this->cnts);

			$this->embed_menus_chapters();
			$this->cnts = preg_replace('/\[\[menu\]\]/', $this->embed_data($this->tpl_sub['menu']), $this->cnts);
			$this->cnts = preg_replace('/\[\[contents\]\]/', $this->embed_data($this->tpl_sub['contents']), $this->cnts);

			// change assets url
			$this->cnts = preg_replace('/{{doc_assets_root}}/', ($shortcode? DB_PLUGIN_URL.'skins/'.$this->skin.'/': ''), $this->cnts);
			
			return true;
		}
		
		/**
		 * embed documentation data inside short code to template
		 *
		 * @param: $template - template contents
		 */
		protected function embed_data($template = ''){
			$shortcodes = array(
				'/{{doc_logo}}/'	=> $this->doc->doc_logo,
				'/{{doc_name}}/'	=> $this->doc->doc_name,
				'/{{doc_desc}}/'	=> stripslashes(base64_decode($this->doc->doc_desc)),
				'/{{doc_date}}/'	=> $this->doc->date,
				'/{{doc_author}}/'	=> $this->doc->author
			);
			
			foreach($shortcodes as $p => $r){
				$template = preg_replace($p, $r, $template);
			}
			
			return $template;
		}
		
		/**
		 * embed documentation menus and chapters inside short code to template
		 */
		protected function embed_menus_chapters(){
			
			// convert documentation info to object to get menu & chapters html
			$docContents = json_decode(stripslashes(base64_decode($this->doc->doc_content)));

			if(empty($docContents)){
				return false;
			}

			////////////////////////////////////////////////
			// get menus contents
			function get_menus($docInfo, $menu_tpl, $index = ''){

				$menu = '<ul>';
				$i = 0;
				foreach($docInfo as $v){
					$i++;

					// embed datas to menu item template
					$t = preg_replace('/{{menu_index}}/', $index.$i.'.', $menu_tpl);
					$t = preg_replace('/{{menu_link}}/', '#menu-'.$v->id, $t);
					$t = preg_replace('/{{menu_name}}/', $v->label, $t);					
					
					// push menu section
					$menu .= '<li>'.$t;

					if(!empty($v->children)){
						$menu .= get_menus($v->children, $menu_tpl, $index.$i.'.');
					}
					
					/////
					$menu .= '</li>';
				}
				
				$menu .= '</ul>';

				return $menu;
			}

			// get chapters contents
			function get_chapters($docInfo, $contents_tpl, $index = ''){

				$contents = '<article>';
				$i = 0;
				foreach($docInfo as $v){
					$i++;

					// push chapter section html
					$t = preg_replace('/{{chapter_index}}/', $index.$i.'.', $contents_tpl);
					$t = preg_replace('/{{chapter_title}}/', $v->chapter_title, $t);
					$t = preg_replace('/{{chapter_id}}/', 'menu-'.$v->id, $t);
					$t = preg_replace('/{{chapter_desc}}/', $v->chapter_desc, $t);
					$t = preg_replace('/{{chapter_contents}}/', $v->chapter_contents, $t);
					$contents .= '<section>'.$t;

					if(!empty($v->children)){
						$contents .= get_chapters($v->children, $contents_tpl, $index.$i.'.');
					}

					///
					$contents .= '</section>';
				}
				
				$contents .= '</article>';

				////
				return $contents;
			}			
			////////////////////////////////////////////////

			// substitute menus and chapters contents
			$this->tpl_sub['menu'] = get_menus($docContents, $this->tpl_sub['menu']);
			$this->tpl_sub['contents'] = get_chapters($docContents, $this->tpl_sub['contents']);

			//////
			return;
		}
		
		/**
		 * import templates
		 *
		 * @return void
		 */
		protected function get_template(){
			
			// read main template contents
			$this->tpl = DB_global::read_file(DB_SKINS.$this->skin.'/index.tpl');
			
			// read sub templates
			foreach($this->tpl_sub as $k => $c){
				$this->tpl_sub[$k] = DB_global::read_file($this->skin_url.$k.'.tpl');
			}

			return true;
		}
		
		/**
		 * create zip files
		 */
		protected function export_zip(){		
			
			///////////////////////////////////////
			$zip_file = $this->download_file.'.zip';
			
			// create directory
			if(!DB_global::new_dir($this->save_dir)){
				return false;
			}
			
			//////////////////////////////////////

			// create contents directory
			$contents_dir = $this->save_dir.'contents/';
			if(!DB_global::new_dir($contents_dir)){
				return false;
			}
			
			// get contents url from HTML
			$root = content_url().'/';
			preg_match_all('/'.preg_replace('/\//', '\\/', $root).'[^"]+/i', $this->cnts, $contents);

			foreach($contents[0] as $k => $c){
				// create contents file to new contents directory
				$filename = str_replace('/', '_', str_replace($root, '', $c));
				DB_global::new_file($contents_dir.$filename, file_get_contents($c));
				
				$this->cnts = str_replace($c, './contents/'.$filename, $this->cnts);
			}

			////////////////////////////////////
			
			// create index.html file
			if(!DB_global::new_file($this->save_file, $this->cnts)){
				return false;
			}

			// copy assets files
			if(!DB_global::xcopy($this->skin_url.'assets/', $this->save_dir.'assets/')){
				return false;
			}
			if(!@copy($this->skin_url.'style.css', $this->save_dir.'style.css')){
				return false;
			}
			
			// create zip file
			if(!DB_global::zip_archive($this->save_dir, $this->save_dir.$zip_file)){
				return false;
			}
			
			// return download url
			return $this->download_url.$zip_file;
		}
		
		/**
		 * create txt files
		 */
		protected function export_txt(){
			
			// read main template contents
			$this->tpl = DB_global::read_file(DB_SKINS.$this->skin.'/txt.tpl');
			
			// convert documentation info to object to get menu & chapters html
			$docContents = json_decode(stripslashes(base64_decode($this->doc->doc_content)));
			
			////////////////////////////////////////////////
			// get menus contents
			function get_menus($docInfo, $index = ''){

				$menu = "\n";
				
				$i = 0;
				foreach($docInfo as $v){
					$i++;
					
					// push menu section
					$menu .= $index.$i.'.  '.$v->label."\n";

					if(!empty($v->children)){
						$menu .= get_menus($v->children, $index.$i.'.');
					}
					
					/////
					$menu .= "\n";
				}
				
				$menu .= "\n";

				return $menu;
			}

			// get chapters contents
			function get_chapters($docInfo, $index = ''){
				$contents = "\n"."\n";

				$i = 0;
				foreach($docInfo as $v){
					$i++;

					// push chapter section html
					$contents .= $index.$i.'.  '.$v->chapter_title."\n"."\n";
					$contents .= $v->chapter_desc."\n"."\n";
					$contents .= strip_tags($v->chapter_contents)."\n"."\n"."\n";

					if(!empty($v->children)){
						$contents .= get_chapters($v->children, $index.$i.'.');
					}
				}
				
				$contents .= "\n"."\n"."\n";

				////
				return $contents;
			}
			////////////////////////////////////////////////

			$shortcodes = array(
				'/{{doc_logo}}/'		=> $this->doc->doc_logo,
				'/{{doc_name}}/'		=> $this->doc->doc_name,
				'/{{doc_desc}}/'		=> strip_tags(stripslashes(base64_decode($this->doc->doc_desc))),
				'/{{doc_date}}/'		=> $this->doc->date,
				'/{{doc_author}}/'		=> $this->doc->author,
				'/\[\[menu\]\]/'		=> get_menus($docContents),
				'/\[\[contents\]\]/'	=> get_chapters($docContents),
			);
			
			foreach($shortcodes as $p => $r){
				$this->tpl = preg_replace($p, $r, $this->tpl);
			}

			////////////////////////
			$txt_file = $this->download_file.'.txt';

			// create directory
			if(!DB_global::new_dir($this->save_dir)){
				return false;
			}
			
			// create index.html file
			if(!DB_global::new_file($this->save_dir.$txt_file, $this->tpl)){
				return false;
			}
			
			// return download url
			return $this->download_url.$txt_file;
		}
		
		/**
		 * create json files
		 */
		protected function export_json(){
			// fix strip slashes generated by editor
			$this->doc->doc_desc = base64_decode($this->doc->doc_desc);
			$this->doc->doc_content = json_decode(stripslashes(base64_decode($this->doc->doc_content)));

			// json data
			$json = json_encode($this->doc);
			
			////////////////////////
			$json_file = $this->download_file.'.json';

			// create directory
			if(!DB_global::new_dir($this->save_dir)){
				return false;
			}
			
			// create index.html file
			if(!DB_global::new_file($this->save_dir.$json_file, $json)){
				return false;
			}
			
			// return download url
			return $this->download_url.$json_file;
		}
		
		/**
		 * create xml files
		 */
		protected function export_xml(){
			// fix strip slashes generated by editor
			$this->doc->doc_desc = base64_decode($this->doc->doc_desc);
			$this->doc->doc_content = json_decode(stripslashes(base64_decode($this->doc->doc_content)));

			// xml data	
			function arrayCastRecursive($array)
			{
				if (is_array($array)) {
					foreach ($array as $key => $value) {
						if (is_array($value)) {
							$array[$key] = arrayCastRecursive($value);
						}
						if ($value instanceof stdClass) {
							$array[$key] = arrayCastRecursive((array)$value);
						}
					}
				}
				if ($array instanceof stdClass) {
					return arrayCastRecursive((array)$array);
				}
				return $array;
			}

			$doc_array = arrayCastRecursive($this->doc);
			
			////////////////
			// create directory
			if(!DB_global::new_dir($this->save_dir)){
				return false;
			}

			// create xml file
			$xml_file = $this->download_file.'.xml';			
			if(!DB_global::array2xml($doc_array, $this->save_dir.$xml_file)){
				return false;				
			}
			
			// return download url
			return $this->download_url.$xml_file;
		}
	}
}

?>