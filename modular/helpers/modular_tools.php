<?php
/**
* Modular. A Custom Fields Plugin for Wolf CMS 
* http://agenceamiral.com/labs/wolfcms_modular
*
* Copyright (c) 2011 Amiral Agence Web <info@agenceamiral.com>
*
* Licensed under the MIT license.
* http://agenceamiral.com/labs/MIT-license.txt
*
* --------------------------------------------------------------------
* @package Plugins
* @subpackage modular
*
* @author Eric Forgues <eric@agenceamiral.com>
* @version v0.1
*
*/
class ModularTools {
    public static function humanReadable($string) {
        return ucwords(str_replace('_', ' ', $string));
    }
    
    public static function computerReadable($string) {
        return ModularTools::wd_remove_accents(strtolower($string));
    }
    
    public static function formElement($field_structure, $field_only = false) {
    	$class = ''; //to delete
    	//ID - hidden
    	if($field_structure['Field'] == 'id'){
    		$class = 'id';
    		$formElement =  '<input type="hidden" value="'.$field_structure['Default'].'" name="'.$field_structure['Field'].'"/>';
    	}
    	//Textarea
    	elseif(strpos($field_structure['Type'],'text') !== false){
    		$class = 'textarea';
    		if($field_structure['Comment'] == 'wysiwyg') {
    		    $formElement =  '<textarea id="'.$field_structure['Field'].'_wysiwyg" name="'.$field_structure['Field'].'">'.$field_structure['Default'].'</textarea>';
    		    $formElement .= "<script type=\"text/javascript\">CKEDITOR.replace( '".$field_structure['Field']."_wysiwyg', { filebrowserBrowseUrl : '".PLUGINS_URI."ckeditor/filemanager/index.html' } );</script>";
    		}
    		else {
    			$formElement =  '<textarea name="'.$field_structure['Field'].'">'.$field_structure['Default'].'</textarea>';
    		}
    	}
    	//Datetime
    	elseif(strpos($field_structure['Type'], 'datetime') !== false){
    		$class = 'text datetime';
       		$formElement =  '<input class="datetimepicker" type="text" value="'.$field_structure['Default'].'" name="'.$field_structure['Field'].'"/>';
    	}
    	//Date
    	elseif(strpos($field_structure['Type'], 'date') !== false){
    		$class = 'text date';
    		$formElement =  '<input class="datepicker" type="text" value="'.$field_structure['Default'].'" name="'.$field_structure['Field'].'"/>';
    	}
    	//Time
    	elseif(strpos($field_structure['Type'], 'time') !== false){
    		$class = 'text time';
    		$formElement =  '<input class="timepicker" type="text" value="'.$field_structure['Default'].'" name="'.$field_structure['Field'].'"/>';
    	}
    	//Checkbox
    	elseif(strpos($field_structure['Type'], 'tinyint(1)') !== false){
    		$class = 'checkbox';
    		$checked = '';
    		if($field_structure['Default']) {
    			$checked = ' checked="checked"';
    		}
    		$formElement =  '<input type="checkbox" name="'.$field_structure['Field'].'"'.$checked.'/>';
    	}
    	//Select
    	elseif(strpos($field_structure['Type'], 'enum') !== false){
    		$class = 'select';
    		$options = explode("','",substr($field_structure['Type'], 6, -2));    		
    		$formElement =  '<select name="'.$field_structure['Field'].'">';
 			foreach($options as $option) {
 				$option = str_replace("''", "'", $option);
 				$selected = '';
 				if($option == $field_structure['Default']) { $selected = 'selected="selected"'; }
 				$formElement .= '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
 			}
    		$formElement .= '</select>';
    	}
    	// Text
    	else {
    		$class = 'text';
    		if($field_structure['Comment'] == 'file_upload') {
    			$formElement =  '<input type="file" name="'.$field_structure['Field'].'" value="'.$field_structure['Default'].'" />';
    		}
    		else {
    			$formElement =  '<input type="text" name="'.$field_structure['Field'].'" value="'.$field_structure['Default'].'" />';
    		}
    			
    	}
    	
    	if($field_structure['Field'] != 'id' && !$field_only) {
    	  	$formElement = '<div class="element '.$class.'"><label for="'.$field_structure['Field'].'">'.ModularTools::humanReadable($field_structure['Field']).'</label>'.$formElement.'</div>';	
    	}
    	
        return $formElement;
    }
    
    // Fonction prise sur ce blog http://www.weirdog.com/blog/php/supprimer-les-accents-des-caracteres-accentues.html
    public static function wd_remove_accents($str, $charset='utf-8')
	{
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    $str = preg_replace('#(?: )#', '_', $str); //Pour les espaces
	    $str = preg_replace('#(?:\'|\")#', '', $str); //Pour les ' et "
	    $str = preg_replace('#(?:\:|\.)#', '', $str); //Pour les . et :
	    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	    
	    return $str;
	}
	
	public static function generateCache() {
        //Cherche les tables créé par modular
        $wolf_tables = Record::query('SHOW TABLES');
        $tables = array();
        foreach($wolf_tables as $wolf_table) {
        	if (substr($wolf_table[0], 0, 8) == 'modular_') {
        		//Enregistre les champs de la table dans une var au nom de la table
        		$fields = Record::query('SHOW FULL COLUMNS FROM '.$wolf_table[0]);
        		foreach($fields as $field) {
        			$tables[substr($wolf_table[0], 8)][$field['Field']] = $field;
        		}
        	}
        }
        // Enregistre la liste des modules dans la cache
		$modularCacheFile = fopen(MODULAR_CACHE_FILE_PATH, 'w') or die("Can't open cache file - Please run chmod 777 on wolf/plugins/modular/cache");
		fwrite($modularCacheFile, serialize($tables));
		fclose($modularCacheFile); 
    }
    
    public static function readCache() {
    	// Va chercher la liste des modules de modular dans la cache
      	$tables = array();
        if(file_exists(MODULAR_CACHE_FILE_PATH)) {
        	$modularCacheFile = fopen(MODULAR_CACHE_FILE_PATH, 'r') or die("Can't open cache file - Please run chmod 777 on wolf/plugins/modular/cache");
			$tables = unserialize(fread($modularCacheFile, filesize(MODULAR_CACHE_FILE_PATH)));
			fclose($modularCacheFile);
        }
        return $tables;
    }
}
?>