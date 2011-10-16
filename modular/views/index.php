<h1><?php echo __('Modular'); ?></h1>
<h2><?php echo __('Modules list'); ?></h2>
<?php if(AuthUser::hasPermission('layout_add')): ?>
<a class="modularAddLink slide_activator" rel="#add_module" href="#"><img src="/wolf/admin/images/plus.png" alt="module-icon"><?php echo __('Add module'); ?></a>
<div id="add_module" class="add_field slide close">
	<form class="modularForm modularBox" action="<?php echo get_url('plugin/modular/module_add'); ?>" method="post">
	<input name="modular_action" type="hidden" value="add_module"/>
	<div class="edit_columns">
	    	<!-- Libellé du champs -->
	    	<div class="element text">
	    		<label for="new_module"><?php echo __('Module name'); ?></label>
	    		<input type="text" name="new_module"/>
	    	</div>
	    <input class="submit" name="modular_submit" type="submit" value="<?php echo __('Add'); ?>"/>
	   	</form>
	</div>
</div>
<?php endif; ?>
<ul class="index">
<?php foreach($tables as $table_name => $table_fields): ?>
  <li class="node odd">
    <img align="middle" src="/wolf/plugins/modular/images/module.png" alt="module-icon">
    <a href="<?php echo get_url('plugin/modular/module_view/'.$table_name); ?>"><?php echo ModularTools::humanReadable($table_name) ?></a>
    <img align="middle" alt="Drag and Drop" src="/wolf/admin/images/drag.gif" class="handle" style="display: none;">
    <?php if(AuthUser::hasPermission('layout_add')): ?>
    <div class="remove">
     <a href="<?php echo get_url('plugin/modular/module_edit/'.$table_name); ?>" class="edit"><img title="Edit module" alt="edit module icon" src="/wolf/plugins/modular/images/icon-edit.png"></a>
    <a onclick="return confirm('<?php echo __('Are you sure you want to delete this module') ?> <?php echo ModularTools::humanReadable($table_name) ?> <?php echo __('and all the data it is connected') ?>?');" href="<?php echo get_url('plugin/modular/module_delete/'.$table_name); ?>" class="remove"><img title="Delete module" alt="delete module icon" src="/wolf/admin/images/icon-remove.gif"></a>
    </div>
    <?php endif; ?>
  </li>
<?php endforeach; ?>
</ul>
