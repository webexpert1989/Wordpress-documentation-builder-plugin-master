<?php
/**************************************************************
 *
 * template to manage documentation documentation list
 *
 * @package  		 TRUEWordpress Documentation Builder Plugin
 * @Version			 1.0.1
 * @file	 		 templates/list.php
 * @author   		 TRUEWordpress Team
 * @Author Link 	 http://truewordpress.com
 * @license	 		 GNU General Public License
 * @license url: 	 http://www.gnu.org/licenses/gpl-3.0.html
 **************************************************************/

// documentation list
function DB_page_list($items = array(), $total = 0, $s = ''){
/////////////
	global $DB_conf;

	$list_table = new DB_admin_list($items, $total);
	$list_table->prepare_items();

	?>
		<div class = "wrap">
			<h2>
				<?php echo __('All Documentations', 'truewordpress'); ?>
				<a href = "admin.php?page=<?php echo $DB_conf['pages']['new']; ?>" class = "add-new-h2">
					<?php echo __('Add New a Documentation', 'truewordpress'); ?>
				</a>

				<?php if($s): ?>
					<span class = "subtitle"><?php printf(__('Search results for &#8220;%s&#8221;', 'truewordpress'), esc_attr($s)); ?></span>
				<?php endif; ?>
			</h2>

			<form id = "documentations-search" method = "get">
				<input type = "hidden" name = "page" value = "<?php echo esc_attr($_REQUEST['page']); ?>" />
				<?php $list_table->search_box(__('Search builded Documentations', 'truewordpress'), $DB_conf['pages']['list']); ?>

				<!-- display table -->
				<?php $list_table->display(); ?>

			</form>
		</div>
	<?php
}
?>