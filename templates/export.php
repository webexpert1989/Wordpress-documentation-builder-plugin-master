<?php
/**************************************************************
 *
 * template to export created documentation documentation
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 templates/export.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

// export documentation
function DB_page_export($doc_data = object){
/////////////
	global $DB_conf;

	$list_table = new DB_admin_list();
	$list_table->prepare_items();

	?>
		<div class = "wrap">
			<h2>
				<?php printf(__('Get Documentation `%s`', 'truewordpress'), $doc_data->doc_name); ?>
				<a href = "admin.php?page=<?php echo $DB_conf['pages']['list']; ?>" class = "add-new-h2">
					<?php echo __('All Documentations', 'truewordpress'); ?>
				</a>
			</h2>
			
			<div class = "message-wrapper">
				<?php do_action('DB-admin-message'); ?>
			</div>

			<div class = "content-wrapper">
				<fieldset>
					<legend><?php echo __('Select file type to export documentation.', 'truewordpress'); ?></legend>
					
					<div class = "field-wrapper">
						<label>
							<input type = "radio" name = "export-type" value = "html" checked/>
							<span><?php echo __('HTML', 'truewordpress'); ?></span>
						</label>
						<label>
							<input type = "radio" name = "export-type" value = "txt"/>
							<span><?php echo __('Text', 'truewordpress'); ?></span>
						</label>
						<label>
							<input type = "radio" name = "export-type" value = "xml"/>
							<span><?php echo __('XML', 'truewordpress'); ?></span>
						</label>
						<label>
							<input type = "radio" name = "export-type" value = "json"/>
							<span><?php echo __('JSON', 'truewordpress'); ?></span>
						</label>

						<div class = "edit-action">
							<a id = "documentation-export" href = "#" data-id = "<?php echo $doc_data->ID; ?>" class = "button button-primary button-large left"><?php echo __('Get Documentation', 'truewordpress'); ?></a>
						</div>
					</div>
					
				</fieldset>
			</div>
		</div>
	<?php
}
?>