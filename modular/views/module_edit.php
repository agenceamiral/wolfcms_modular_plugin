<h1><?php echo ModularTools::humanReadable($module_slug) ?></h1>
<h2><?php echo __('Field list'); ?></h2>

<a class="modularAddLink slide_activator" rel="#add_column" href="#"><img src="/wolf/admin/images/plus.png" alt="module-icon"><?php echo __('Add field'); ?></a>
<div id="add_column" class="add_field slide close">
	<form class="modularForm modularBox" action="<?php echo get_url('plugin/modular/module_save/'.$module_slug); ?>" method="post">
	<input name="modular_action" type="hidden" value="add_column"/>
	<div class="edit_columns">
	    	<!-- Libellé du champs -->
	    	<div class="element text">
	    		<label for="new_label"><?php echo __('Label'); ?></label>
	    		<input type="text" name="new_label"/>
	    	</div>
	    	
	    	<!-- Type de champs -->
	    	<div class="element select">
	    		<label for="new_type"><?php echo __('Type'); ?></label>
	    		<select class="select_type" name="new_type">
	    			<option value="varchar(255)"><?php echo __('Text field'); ?></option>
	    			<option value="text"><?php echo __('Text area'); ?></option>
	    			<option value="tinyint(1)"><?php echo __('Checkbox'); ?></option>
	    			<option value="enum"><?php echo __('Options list'); ?></option>
	    			<option value="datetime"><?php echo __('Date & time'); ?></option>
	    			<option value="date"><?php echo __('Date'); ?></option>
	    			<option value="time"><?php echo __('Time'); ?></option>
	    		</select>
	    	</div>
	    		
    		<!-- Options pour le select (si nécessaire) -->
    		<div class="element textarea options" style="display:none;">
	    		<label for="new_options"><?php echo __('Options') ?> <span class="advice"><?php echo __('(Enter one option per line)') ?></label>
	    		<textarea class="options" name="new_options"></textarea>
	    	</div>
	    	
	    	<!-- File upload pour le input (si nécessaire) -->
    		<div class="element checkbox upload">
    			<label for="new_file_upload"><?php echo __('File upload') ?></label>
    			<input name="new_file_upload" type="checkbox"/>
    		</div>
    		
    		<!-- Traitement de texte pour le textarea (si nécessaire) -->
    		<div class="element checkbox wysiwyg"  style="display:none;">
    			<label for="new_wysiwyg"><?php echo __('WYSIWYG') ?></label>
    			<input name="new_wysiwyg" type="checkbox" checked="checked"/>
    		</div>
	    	
	    	<!-- Valeur pas défaut -->
	    	<div class="element text default_hide">
	    		<label for="new_default"><?php echo __('Default value') ?></label>
	    		<input class="default_generator default_text" type="text"/>
	    		<input class="default_generator default_checkbox" style="display:none;" type="checkbox"/>
	    		<select class="default_generator default_select" style="display:none;"></select>
	    		<input class="default" type="hidden" name="new_default"/>
	    		<div class="element checkbox">
	    			<label for="new_no_default"><?php echo __('No default value') ?></label>
	    			<input name="new_no_default" type="checkbox" checked="checked"/>
	    		</div>

	    	</div>
	    </div>
	  <input class="submit" name="modular_submit" type="submit" value="<?php echo __('Add') ?>"/>
	  </form>
</div>
<?php if(count($table) > 1): ?>
<form class="modularForm" action="<?php echo get_url('plugin/modular/module_save/'.$module_slug); ?>" method="post">
<input name="modular_action" type="hidden" value="edit_column"/>
<ul class="index">
<?php foreach($table as $field_name => $field): ?>
	<?php if($field_name != 'id'): ?>
	  <li class="node odd">
	    <img align="middle" src="/wolf/admin/images/expand.png" alt="module-icon">
	    <a class="slide_activator" rel="#<?php echo $field_name; ?>" href="#"><?php echo ModularTools::humanReadable($field_name) ?></a>
	    <img align="middle" alt="Drag and Drop" src="/wolf/admin/images/drag.gif" class="handle" style="display: none;">
	    <div class="remove">
	    <a onclick="return confirm('<?php echo __('Êtes-vous sûr de vouloir supprimer la colonne') ?> <?php echo ModularTools::humanReadable($field_name) ?> <?php echo __('and all the data it is connected') ?>?');" href="<?php echo get_url('plugin/modular/module_delete_column/'.$module_slug.'/'.$field_name); ?>" class="remove"><img title="Delete column" alt="delete column icon" src="/wolf/admin/images/icon-remove.gif"></a>
	    </div>
	    <div id="<?php echo $field_name; ?>" class="modularBox edit_columns slide close">
	    	<!-- Libellé du champs -->
	    	<div class="element text">
	    		<label for="<?php echo $field_name ?>_label"><?php echo __('Label') ?></label>
	    		<input type="text" name="<?php echo $field_name ?>_label" value="<?php echo ModularTools::humanReadable($field_name) ?>" />
	    	</div>
	    	
	    	<!-- Type de champs -->
	    	<div class="element select">
	    		<label for="<?php echo $field_name ?>_type"><?php echo __('Type') ?></label>
	    		<select class="select_type" name="<?php echo $field_name ?>_type">
	    			<option value="varchar(255)"<?php if ($field['Type'] == 'varchar(255)') echo ' selected="selected"'; ?>><?php echo __('Text field') ?></option>
	    			<option value="text"<?php if ($field['Type'] == 'text') echo ' selected="selected"'; ?>><?php echo __('Text area') ?></option>
	    			<option value="tinyint(1)"<?php if ($field['Type'] == 'tinyint(1)') echo ' selected="selected"'; ?>><?php echo __('Checkbox') ?></option>
	    			<option value="enum"<?php if (substr($field['Type'], 0, 4) == 'enum') echo ' selected="selected"'; ?>><?php echo __('Options list') ?></option>
	    			<option value="datetime"<?php if ($field['Type'] == 'datetime') echo ' selected="selected"'; ?>><?php echo __('Date & time') ?></option>
	    			<option value="date"<?php if ($field['Type'] == 'date') echo ' selected="selected"'; ?>><?php echo __('Date') ?></option>
	    			<option value="time"<?php if ($field['Type'] == 'time') echo ' selected="selected"'; ?>><?php echo __('Time') ?></option>
	    		</select>
	    	</div>
	    		
    		<!-- Options pour le select (si nécessaire) -->
    		<div class="element textarea options"<?php if(substr($field['Type'], 0, 4) != 'enum') echo ' style="display:none;"'; ?>>
	    		<label for="<?php echo $field_name ?>_options"><?php echo __('Options') ?> <span class="advice"><?php echo __('(Enter one option per line)') ?></label>
	    		<textarea class="options" name="<?php echo $field_name ?>_options"><?php
	    			if(substr($field['Type'], 0, 4) == 'enum') {
	    				$options = explode("','",substr($field['Type'], 6, -2));
	    				$i = 1;
		    			foreach($options as $option) {
			 				$option = str_replace("''", "'", $option);
			 				if($i < count($options)) {
			 					echo $option."\r\n";
			 				}
			 				else {
			 					echo $option;
			 				}
			 				$i++;
			 			}
	    			}
	    		?></textarea>
	    	</div>
	    	
	    	<!-- File upload pour le input (si nécessaire) -->
    		<div class="element checkbox upload"<?php if ($field['Type'] != 'varchar(255)') echo ' style="display:none"'; ?>>
    			<label for="<?php echo $field_name ?>_file_upload"><?php echo __('File upload') ?></label>
    			<input name="<?php echo $field_name ?>_file_upload" type="checkbox"<?php if($field['Comment'] == 'file_upload'){ echo ' checked="checked"'; } ?>/>
    		</div>
    		
    		<!-- Traitement de texte pour le textarea (si nécessaire) -->
    		<div class="element checkbox wysiwyg"<?php if ($field['Type'] != 'text') echo ' style="display:none"'; ?>>
    			<label for="<?php echo $field_name ?>_wysiwyg"><?php echo __('WYSIWYG') ?></label>
    			<input name="<?php echo $field_name ?>_wysiwyg" type="checkbox"<?php if($field['Comment'] == 'wysiwyg'){ echo ' checked="checked"'; } ?>/>
    		</div>
	    	
	    	<!-- Valeur pas défaut -->
	    	<div class="element <?php 
	    		if ($field['Type'] == 'tinyint(1)'){echo 'checkbox';} elseif(substr($field['Type'], 0, 4) == 'enum'){echo 'select';} else{echo 'text';}  ?> default_hide">
	    		<label for="<?php echo $field_name ?>_default"><?php echo __('Default value') ?></label>
	    		<?php 
	    		$picker='';
	    		if ($field['Type'] == 'datetime') {
	    			$picker = ' datetimepicker';
	    		} 
	    		if ($field['Type'] == 'date') {
	    			$picker = ' datepicker';
	    		} 
	    		if ($field['Type'] == 'time') {
	    			$picker = ' timepicker';
	    		}
	    		?>
	    		<input class="default_generator default_text<?php echo $picker ?>" value="<?php echo $field['Default'] ?>" <?php if ($field['Type'] == 'tinyint(1)' || substr($field['Type'], 0, 4) == 'enum') echo ' style="display:none"'; ?> type="text"/>
	    		<input class="default_generator default_checkbox" <?php 
	    		if ($field['Type'] != 'tinyint(1)') {
	    			echo ' style="display:none"';
	    		}
	    		elseif($field['Default']) {
	    			echo 'checked="checked"';
	    		}
	    		?> type="checkbox"/>
	    		<select class="default_generator default_select" <?php if (substr($field['Type'], 0, 4) != 'enum') echo ' style="display:none"'; ?>><?php 
	    		if (substr($field['Type'], 0, 4) == 'enum'){
	    			$options = explode("','",substr($field['Type'], 6, -2));
	    			foreach($options as $option) {
		 				$option = str_replace("''", "'", $option);
		 				$selected = '';
		 				if($option == $field['Default']) { $selected = 'selected="selected"'; }
		 				echo '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
		 			}
	    		} 
	    		?></select>
	    		<input class="default" type="hidden" name="<?php echo $field_name ?>_default" value="<?php 
	    		if($field['Type'] == 'tinyint(1)') {
	    			if($field['Default']) {
	    				echo 'on';
	    			}
	    		}
	    		else {
	    			echo $field['Default'];
	    		} 
	    		?>" />
	    		<div class="element checkbox">
	    			<label for="<?php echo $field_name ?>_no_default"><?php echo __('No default value') ?></label>
	    			<input name="<?php echo $field_name ?>_no_default" type="checkbox" <?php if($field['Default'] == '') echo 'checked="checked"'; ?>/>
	    		</div>

	    	</div>
	    </div>
	  </li>
	 <?php endif; ?>
<?php endforeach; ?>
</ul>
<input class="submit" name="modular_submit" type="submit" value="<?php echo __('Save') ?>"/>
</form>
<?php endif;?>