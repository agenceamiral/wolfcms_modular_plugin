<h1><?php echo modularTools::humanReadable($module_slug); ?></h1>
<h2><?php echo __('Entry list'); ?></h2>
<a class="modularAddLink" href="<?php echo get_url('plugin/modular/entry_add/'.$module_slug); ?>"><img src="/wolf/admin/images/plus.png" alt="module-icon"><?php echo __('Add entry') ?></a>
<table class="modular_entries">
<?php 
$i = 1;
foreach($table as $column_name => $column): ?>
	<?php 
  		if($i < 5):
  		 ?>
  	<th><?php echo modularTools::humanReadable($column_name); ?></th>
  	<?php 
  		endif;
  		$i++;
  		?>
<?php endforeach; ?>
	<th class="action"><?php __('Actions') ?></th>
<?php
foreach($entries as $entry):
$class = null;
if ($i++ % 2 != 0) {
	$class = ' class="alt"';
}
?>
  <tr<?php echo $class; ?>>
  	<?php
  	$i = 1;
  	foreach($table as $column_name => $column): ?>
  		<?php 
  		if($i < 5): ?>
  		<td>
  		<?php 
  		if($column['Type'] == 'tinyint(1)') {
  			if($entry[$column_name]) {
  				echo 'Oui';
  			}
  			else {
  				echo 'Non';
  			}
  		}
  		else {
  			echo $entry[$column_name];
  		}
  		?>
  		</td>
  		<?php 
  		endif;
  		$i++;
  		?>
  	<?php endforeach; ?>
  		<td class="action"><a href="<?php echo get_url('plugin/modular/entry_edit/'.$module_slug.'/'.$entry['id']); ?>" class="edit"><img title="Edit module" alt="edit module icon" src="/wolf/plugins/modular/images/icon-edit.png"></a>
    <a onclick="return confirm('<?php echo __("Êtes-vous sûr de vouloir supprimer l'entrée #") ?><?php echo $entry['id'] ?> <?php echo __('and all the data it is connected') ?>?');" href="<?php echo get_url('plugin/modular/entry_delete/'.$module_slug.'/'.$entry['id']); ?>" class="remove"><img title="Delete module" alt="delete module icon" src="/wolf/admin/images/icon-remove.gif"></a></td>
  </tr>
<?php endforeach; ?>
</table>
