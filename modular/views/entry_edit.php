<h1><?php echo ModularTools::humanReadable($module_slug) ?></h1>
<h2><?php echo __('Edit of entry #'); echo $id; ?></h2>
<form class="modularForm modularBox" action="<?php echo get_url('plugin/modular/entry_save/'.$module_slug); ?>" method="post" enctype="multipart/form-data">
<input name="modular_action" type="hidden" value="edit_entry"/>
<?php $entry = $entry->fetch(PDO::FETCH_ASSOC); ?>
<?php foreach($table as $field_name => $field): ?>
	<?php
	$field['Default'] = $entry[$field_name];;
	
	echo ModularTools::formElement($field);
	
	?>
<?php endforeach; ?>
<input class="submit" name="entry_submit" type="submit" value="<?php echo __('Save'); ?>"/>
</form>