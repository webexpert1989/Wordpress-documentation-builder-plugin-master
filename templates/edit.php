<?php
/**************************************************************
 *
 * template to add new documentation documentation or edit documentation documentation
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 templates/md-edit.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/


// new documentation
function DB_page_edit($doc_data = object){

	/////////////
	$doc_data->doc_desc = stripslashes(base64_decode($doc_data->doc_desc));
	$doc_data->doc_content = base64_decode($doc_data->doc_content);

	///////////
	global $DB_conf;

	$DB_settings = get_option('DB_settings');
	
	///
	$settings = new DB_settings();

	?>
		<div class = "wrap">
			<h2>
				<?php printf(__('Edit Documentation `%s`', 'truewordpress'), $doc_data->doc_name); ?>
				<a id = "doc-list-page" href = "admin.php?page=<?php echo $DB_conf['pages']['list']; ?>" class = "add-new-h2">
					<?php echo __('All Documentations', 'truewordpress'); ?>
				</a>
			</h2>

			<div class = "message-wrapper">
				<?php do_action('DB-admin-message'); ?>
			</div>
			
			<div class = "content-wrapper">
			
				<fieldset class = "edit-settings-wrapper">
					<legend><?php echo __('Settings', 'truewordpress'); ?></legend>

					<div class = "edit-settings">
						<section>
							<label for = "doc-skin"><?php echo __('Skins', 'truewordpress'); ?>: </label>
							<select id = "doc-skin">
								<?php foreach(DB_settings::get_skins() as $k => $v): ?>
									<option value = "<?php echo $k; ?>" <?php echo $k == $doc_data->skin? 'selected': ''; ?>>
										<?php echo $v['label']; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</section>

						<section>
							<label for = "doc-logo"><?php echo __('Documentation Logo', 'truewordpress'); ?>: </label>
							<div class = "field-wrapper">
								<input type = "text" id = "doc-logo" value = "<?php echo $doc_data->doc_logo; ?>"/>
								<input type = "button" id = "upload-doc-logo" class = "button-secondary" value = "<?php echo __('Upload Logo', 'truewordpress'); ?>"/>
								<?php if($doc_data->doc_logo): ?>
									<img id = "doc-logo-preview" src = "<?php echo $doc_data->doc_logo; ?>"/>
								<?php endif; ?>
							</div>
						</section>
						<section>
							<label for = "doc-title"><?php echo __('Documentation Title', 'truewordpress'); ?>: </label>
							<input type = "text" id = "doc-title" maxlength = 200 value = "<?php echo $doc_data->doc_name; ?>"/>
						</section>
						<section>
							<label><?php echo __('Documentation Description', 'truewordpress'); ?>: </label>
							<div class = "doc-desc-editor">
								<?php wp_editor($doc_data->doc_desc, 'doc-desc', array('editor_height' => 300 , 'media_buttons' => true, 'teeny' => false)); ?>
							</div>
						</section>
					</div>

					<div class = "edit-action">
						<input type = "button" id = "documentation-remove" class = "button button-secondary button-large right" value = "<?php echo __('Remove Documentation', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you remove this documentation really?', 'truewordpress'); ?>"/>
						<input type = "button" id = "documentation-update" class = "button button-primary button-large right" value = "<?php echo __('Save Documentation', 'truewordpress'); ?>" data-auto-save = "<?php echo $DB_settings['auto_save']; ?>"/>
					</div>
				</fieldset>
				
				<fieldset class = "edit-documentation-wrapper">
					<legend><?php echo __('Documentation', 'truewordpress'); ?></legend>

					<div class = "edit-menu-wrapper">
						
						<div id = "documentation-menus" class = "sortable-wrapper">
							<div class = "sortable-search">
								<label for = "sortable-search"><?php echo __('Search Menus', 'truewordpress'); ?>: </label>
								<input type = "text" id = "sortable-search" maxlength = 200/>
							</div>

							<ul class = "sortable" data-empty = "<?php echo __('Empty Menus', 'truewordpress'); ?>" data-new-menu-text = "<?php echo __('New Menu', 'truewordpress'); ?>">
								<div class = "sortable-empty"><?php echo __('Empty Menus', 'truewordpress'); ?></div>
							</ul>
							
							<div class = "sortable-action">
								<input type = "button" data-action = "sortable-new" class = "button button-primary button-large right" value = "<?php echo __('Add New', 'truewordpress'); ?>"/>
							</div>
						</div>
						
						<?php if($doc_data->doc_content): ?>
							<script>
								(function($){
									docInfo = $.parseJSON("<?php echo $doc_data->doc_content; ?>");
								})(jQuery);								
							</script>
						<?php endif; ?>

					</div>

					<div class = "edit-doc-wrapper">
						<div class = "edit-doc">
							<section>
								<label for = "doc-menu-name"><?php echo __('Menu Name', 'truewordpress'); ?>: </label>
								<input type = "text" id = "doc-menu-name" maxlength = 200/>
							</section>
							<section>
								<label for = "doc-chapter-title"><?php echo __('Chapter Title', 'truewordpress'); ?>: </label>
								<input type = "text" id = "doc-chapter-title" maxlength = 200/>
							</section>
							<section>
								<label for = "doc-chapter-desc"><?php echo __('Chapter Description', 'truewordpress'); ?>: </label>
								<textarea id = "doc-chapter-desc" rows = 5></textarea>
							</section>
							<section>
								<label><?php echo __('Chapter Contents', 'truewordpress'); ?>: </label>
								<div class = "doc-contents-editor">
									<?php wp_editor('<p></p>', 'doc-chapter-contents', array('editor_height' => 500 , 'media_buttons' => true, 'teeny' => false)); ?>
								</div>
							</section>
						</div>
					</div>

				</fieldset>		
				
			</div>

			<input type = "hidden" id = "doc-id" value = "<?php echo $doc_data->ID; ?>"/>
		</div>
	<?php
}
?>