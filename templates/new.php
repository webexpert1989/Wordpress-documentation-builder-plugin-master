<?php
/**************************************************************
 *
 * template to add new documentation documentation or edit documentation documentation
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 templates/new.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

// new documentation
function DB_page_new(){
	/////////////
	global $DB_conf;

	$sub = isset($_GET['sub'])? $_GET['sub']: 'new';
	?>
		<div class = "wrap">
			<h2>
				<?php echo __('Add New Documentation', 'truewordpress'); ?>

				<?php if($sub == 'import'): ?>
					<a id = "doc-new-edit" href = "admin.php?page=<?php echo $DB_conf['pages']['new']; ?>" class = "add-new-h2">
						<?php echo __('Edit new Documentations', 'truewordpress'); ?>
					</a>
				<?php else: ?>
					<a id = "doc-import" href = "admin.php?page=<?php echo $DB_conf['pages']['new']; ?>&sub=import" class = "add-new-h2">
						<?php echo __('Import Documentations', 'truewordpress'); ?>
					</a>
				<?php endif; ?>
				<a id = "doc-list-page" href = "admin.php?page=<?php echo $DB_conf['pages']['list']; ?>" class = "add-new-h2">
					<?php echo __('All Documentations', 'truewordpress'); ?>
				</a>
			</h2>

			<div class = "message-wrapper">
				<?php do_action('DB-admin-message'); ?>
			</div>
			
			<div class = "content-wrapper">
				
				<?php switch($sub):
					case 'import': ?>

						<fieldset class = "edit-settings-wrapper">
							<legend><?php echo __('Import New a Documentation', 'truewordpress'); ?></legend>
							<div class = "edit-settings">
								<section>
									<form id = "import-new-doc" action = "<?php echo $_SERVER['REQUEST_URI']; ?>" method = "POST" enctype = "multipart/form-data">
										<input type = "file" id = "new_doc" name = "new_doc"/>											
										<input type = "button" id = "settings-doc-import" class = "button button-primary button-large" value = "<?php echo __('Import & Add a Documentation', 'truewordpress'); ?>" data-empty-text = "<?php echo __('Please choose  documentation file correctly.', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you add selected the documentation really?', 'truewordpress'); ?>"/>
									</form>	
								</section>
							</div>
						</fieldset>

					<?php break; ?>

					<?php default: ?>

						<fieldset class = "edit-settings-wrapper">
							<legend><?php echo __('Settings', 'truewordpress'); ?></legend>

							<div class = "edit-settings">
								<section>
									<label for = "doc-skin"><?php echo __('Skins', 'truewordpress'); ?>: </label>
									<select id = "doc-skin">
										<?php foreach(DB_settings::get_skins() as $k => $v): ?>
											<option value = "<?php echo $k; ?>"><?php echo $v['label']; ?></option>
										<?php endforeach; ?>
									</select>
								</section>

								<section>
									<label for = "doc-logo"><?php echo __('Documentation Logo', 'truewordpress'); ?>: </label>
									<div class = "field-wrapper">
										<input type = "text" id = "doc-logo"/>
										<input type = "button" id = "upload-doc-logo" class = "button-secondary" value = "<?php echo __('Upload Logo', 'truewordpress'); ?>"/>
									</div>
								</section>
								<section>
									<label for = "doc-title"><?php echo __('Documentation Title', 'truewordpress'); ?>: </label>
									<input type = "text" id = "doc-title" maxlength = 200/>
								</section>
								<section>
									<label><?php echo __('Documentation Description', 'truewordpress'); ?>: </label>
									<div class = "doc-desc-editor">
										<?php wp_editor('<p></p>', 'doc-desc', array('editor_height' => 300, 'media_buttons' => true, 'teeny' => false)); ?>
									</div>
								</section>
							</div>

							<div class = "edit-action">
								<input type = "button" id = "documentation-save" class = "button button-primary button-large right" value = "<?php echo __('Save Documentation', 'truewordpress'); ?>"/>
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
										<div class = "sortable-empty"><?php echo __('New Menu', 'truewordpress'); ?></div>
									</ul>

									<div class = "sortable-action">
										<input type = "button" data-action = "sortable-new" class = "button button-primary button-large right" value = "<?php echo __('Add New', 'truewordpress'); ?>"/>
									</div>
								</div>

								
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

					<?php break; ?>
		
				<?php endswitch; ?>
				
			</div>

			<input type = "hidden" id = "doc-id" value = "<?php echo $doc_id; ?>"/>
		</div>
	<?php
}
?>