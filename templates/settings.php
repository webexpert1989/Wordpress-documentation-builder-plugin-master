<?php
/**************************************************************
 *
 * template to make settings
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 templates/settings.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

// documentation settings
function DB_page_settings($skin_style = ''){
/////////////
	global $DB_conf;

	$get = isset($_GET['sub'])? $_GET['sub']: 'general';
	$skin = isset($_GET['skin'])? $_GET['skin']: '';
	?>
		<div class = "wrap">
			<h2>
				<?php echo __('Settings', 'truewordpress'); ?>
				<a href = "admin.php?page=<?php echo $DB_conf['pages']['list']; ?>" class = "add-new-h2">
					<?php echo __('All Documentations', 'truewordpress'); ?>
				</a>
				<a href = "admin.php?page=<?php echo $DB_conf['pages']['new']; ?>" class = "add-new-h2">
					<?php echo __('Add New a Documentation', 'truewordpress'); ?>
				</a>
			</h2>
			
			<div class = "message-wrapper">
				<?php do_action('DB-admin-message'); ?>
			</div>
			
			<div>
				<ul class="subsubsub">
					<li><a href = "admin.php?page=<?php echo $DB_conf['pages']['settings']; ?>&sub=general" <?php echo $get == 'general'? 'class = "current"': ''; ?>>
						<?php echo __('General', 'truewordpress'); ?></a> |
					</li>
					<li><a href = "admin.php?page=<?php echo $DB_conf['pages']['settings']; ?>&sub=skins" <?php echo $get == 'skins'? 'class = "current"': ''; ?>>
						<?php echo __('Skins', 'truewordpress'); ?></a>
					</li>
				</ul>
			</div>

			<div class="clear"></div>

			<div class = "content-wrapper">
					
				<?php switch($get):
					case 'general': ?>
		
						<fieldset class = "edit-settings-wrapper">
							<div class = "edit-settings">
								<?php $DB_settings = get_option('DB_settings'); ?>

								<section>
									<label>
										<span>Rows per a page: </span>
										<input type = "number" id = "rows_per_page" value = "<?php echo $DB_settings['rows_per_page']; ?>" data-default-value = "<?php echo $DB_conf['rows_per_page']; ?>"/>
									</label>
									<label>
										<span>Auto-save interval(Second): </span>
										<input type = "number" id = "auto_save" value = "<?php echo $DB_settings['auto_save']; ?>" data-default-value = "<?php echo $DB_conf['auto_save']; ?>"/>
										<span> <i>(Enter 0 if you don't need auto-save)</i></span>
									</label>
								</section>

								<section class = "edit-action">
									<input type = "button" id = "settings-update" class = "button button-primary button-large" value = "<?php echo __('Save Settings', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you save settings really?', 'truewordpress'); ?>"/>
									<input type = "button" id = "settings-reset" class = "button button-secondary button-large" value = "<?php echo __('Reset Settings', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you reset settings really?', 'truewordpress'); ?>"/>
								</section>

							</div>
						</fieldset>

					<?php break; ?>

					<?php case 'skins': ?>
						<fieldset class = "edit-settings-wrapper">
							<legend><?php echo __('Import New a Skin', 'truewordpress'); ?></legend>
							<div class = "edit-settings">
								<section>
									<form id = "import-new-skin" action = "<?php echo $_SERVER['REQUEST_URI']; ?>" method = "POST" enctype = "multipart/form-data">
										<input type = "file" id = "new_skin" name = "new_skin"/>											
										<input type = "button" id = "settings-skin-import" class = "button button-primary button-large" value = "<?php echo __('Import & Add new Skin', 'truewordpress'); ?>" data-empty-text = "<?php echo __('Please select new skin file correctly.', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you install selected the skin really?', 'truewordpress'); ?>"/>
									</form>	
								</section>
							</div>
						</fieldset>

						<fieldset class = "edit-settings-wrapper">
							<legend><?php echo __('Edit Skin', 'truewordpress'); ?></legend>
							<div class = "edit-settings">

								<section>
									<label>
										<span>Select skin: </span>
										<select id = "sel-skin">
											<?php foreach(DB_settings::get_skins(true) as $k => $v): ?>
												<option value = "<?php echo $k; ?>" <?php echo $k == $skin? 'selected': ''; ?>>
													<?php echo $v['label']; ?>
												</option>
											<?php endforeach; ?>
										</select>
										<input type = "button" id = "settings-skin-select" class = "button button-large" value = "<?php echo __('Select', 'truewordpress'); ?>" data-href = "admin.php?page=<?php echo $DB_conf['pages']['settings']; ?>&sub=skins&skin=" data-error-text = "<?php echo __('Please select skin to edit.', 'truewordpress'); ?>"/>
									</label>
								
								<?php if($skin): ?>
									<label>
										<span>Edit selected skin (style.css): </span>
									</label>
									
									<form id = "code-editor-wrapper">
										<textarea id = "code-editor" rows = "30" data-code-editor = "css"><?php echo $skin_style; ?></textarea>
									</form>
								</section>
								
								
								<section class = "edit-action">									
									<input type = "button" id = "settings-skin-delete" class = "button button-secondary button-large right" value = "<?php echo __('Delete Skin', 'truewordpress'); ?>" data-redirect = "admin.php?page=<?php echo $DB_conf['pages']['settings']; ?>&sub=skins" data-error-text = "<?php echo __('Please select skin to edit.', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you delete selected the skin really?', 'truewordpress'); ?>"/>
									<input type = "button" id = "settings-skin-update" class = "button button-primary button-large right" value = "<?php echo __('Update Skin', 'truewordpress'); ?>" data-error-text = "<?php echo __('Please select skin to edit.', 'truewordpress'); ?>" data-confirm-text = "<?php echo __('Do you update the skin really?', 'truewordpress'); ?>"/>
								<?php endif; ?>

								</section>
							</div>
						</fieldset>
					<?php break; ?>

				<?php endswitch; ?>

			</div>
		</div>
	<?php
}
?>