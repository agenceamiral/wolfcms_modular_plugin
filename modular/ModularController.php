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
class ModularController extends PluginController {
    function __construct() {
        AuthUser::load();
        if ( ! AuthUser::isLoggedIn()) {
            redirect(get_url('login'));
        }
        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/modular/views/sidebar'));
        // Création des constante propre à modular
        if (!defined('MODULAR_ROOT'))  define('MODULAR_ROOT', PLUGINS_ROOT.DIRECTORY_SEPARATOR.'modular');
        if (!defined('MODULAR_HELPER_PATH'))  define('MODULAR_HELPER_PATH', MODULAR_ROOT.DIRECTORY_SEPARATOR.'helpers');
        if (!defined('MODULAR_CACHE_FILE_PATH'))  define('MODULAR_CACHE_FILE_PATH', MODULAR_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'modular.cache');
        //Inclusion des helpers propre à modular
    	include_once(MODULAR_HELPER_PATH.DIRECTORY_SEPARATOR.'modular_tools.php');
    }
 
    function index() {		
        // Va chercher la liste des modules de modular dans la cache
      	$tables = modularTools::readCache();
         
        // Transmet les infos des tables à la view et l'affiche        
        $this->display('modular/views/index', array(
        'tables' => $tables
        ));
    }
    
    // Fonction relative au module
    function module_view($module_slug) {
    	$pdo = Record::getConnection();
		$entries = Record::query("SELECT * FROM `modular_".$module_slug."`");
		// Va chercher la liste des modules de modular dans la cache
      	$tables = modularTools::readCache();
        
        // Transmet les entrés de la table à la view et l'affiche 
     	$this->display('modular/views/module_view', array(
     	'table' => $tables[$module_slug],
        'entries' => $entries,
        'module_slug' => $module_slug
        ));
    }
    
    function module_add() {
    	$pdo = Record::getConnection();
    	$data = $_POST;
    	$module_slug = modularTools::computerReadable($data['new_module']);
    	$db_name = explode(';', substr(DB_DSN, 13)); $db_name = $db_name[0];
    	
    	$added_tables = Record::query("CREATE TABLE `".$db_name."`.`modular_".$module_slug."` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = MyISAM;");
    	
    	// Réécriture du cache suite au modification
        modularTools::generateCache();
		
		//Redirection	
    	header('Location: '.get_url('plugin/modular/module_edit/'.$module_slug));
    }
    
    
    function module_edit($module_slug) {
    	// Va chercher la liste des modules de modular dans la cache
      	$tables = modularTools::readCache();

        // Transmet les infos de la table à la view et l'affiche 
     	$this->display('modular/views/module_edit', array(
        'table' => $tables[$module_slug],
        'module_slug' => $module_slug
        ));
    }
    
    function module_save($module_slug) {
    	$pdo = Record::getConnection();
		//S'assure que c'est bien du mySQL
	  	if ('mysql' == $pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
	    	$data = $_POST;
	    	switch ($data['modular_action']) {
	    		//Lors de l'ajout d'une nouvelle colonne
	    		case 'add_column':
	    			$table_name = 'modular_'.$module_slug;
	    			$default = ''; 
					if($data['new_type'] != 'text' && $data['new_no_default'] == false) {
						switch($data['new_type']) {
							case 'tinyint(1)':
								if($data['new_default'] == 'on') {
									$default = " DEFAULT '1'";
								}
								else {
									$default = " DEFAULT '0'";
								}
	    						break;
							default:
								$default = " DEFAULT '". str_replace("'", "''", $data['new_default'])."'";
								break; 
						}
					}  
					
					// Ajoute les options au type enum, et met le type en UPPERCASE
					if($data['new_type'] == 'enum') {
						$options =  explode("\r\n", $data['new_options']);
						$options_list = "";
						foreach($options as $option) {
							$options_list .= "'".str_replace("'", "''", $option)."',";
						}
						
						// Retire la ligne vide ainsi que la virgule de trop
						$options_list = substr($options_list, 0, -1);
						
						$data['new_type'] = 'ENUM('.$options_list.')';
					}
					else {
						$data['new_type'] = strtoupper($data['new_type']);
					}
					$added_column = Record::query("ALTER TABLE `".$table_name."` ADD `".modularTools::computerReadable($data['new_label'])."` ".$data['new_type']." NULL".$default);
	    			break;
	    			
	    			
	    			
	    		// Lors d'édition de colonne existante
	    		case 'edit_column':
	    			// Va chercher la liste des modules de modular dans la cache
      				$tables = modularTools::readCache();
			        
	    			$table_name = 'modular_'.$module_slug;
	    			
	    			// Crée le SQL et l'exécute pour chaque colonne
	    			foreach($tables[$module_slug] as $field_name => $field) {
	    				$comment = '';
	    				$default = '';
	    				if($field_name != 'id') {	 
	    					if($data[$field_name.'_type'] != 'text' && $data[$field_name.'_no_default'] == false) {
	    						switch($data[$field_name.'_type']) {
	    							case 'tinyint(1)':
	    								if($data[$field_name.'_default'] == 'on') {
	    									$default = " DEFAULT '1'";
	    								}
	    								else {
	    									$default = " DEFAULT '0'";
	    								}
	    								break;
	    							default:
	    								$default = " DEFAULT '". str_replace("'", "''", $data[$field_name.'_default'])."'";
	    								break; 
	    						}
	    					}  
	    					
	    					//Ajoute l'option file_upload
    						if($data[$field_name.'_type'] == 'varchar(255)' && $data[$field_name.'_file_upload'] == 'on') {
    							$comment = 'file_upload';
    						}
    						//Ajoute l'option wysiwyg
    						if($data[$field_name.'_type'] == 'text' && $data[$field_name.'_wysiwyg'] == 'on') {
    							$comment = 'wysiwyg';
    						}
	    					
	    					// Ajoute les options au type enum, et met le type en UPPERCASE
	    					if($data[$field_name.'_type'] == 'enum') {
	    						$options =  explode("\r\n", $data[$field_name.'_options']);
	    						$options_list = "";
	    						foreach($options as $option) {
	    							$options_list .= "'".str_replace("'", "''", $option)."',";
	    						}
	    						
	    						// Retire la ligne vide ainsi que la virgule de trop
	    						$options_list = substr($options_list, 0, -1);
	    						
	    						$data[$field_name.'_type'] = 'ENUM('.$options_list.')';
	    					}
	    					else {
	    						$data[$field_name.'_type'] = strtoupper($data[$field_name.'_type']);
	    					}
	    					 				
	    					 $edited_column = Record::query("ALTER TABLE `".$table_name."` CHANGE `".$field_name."` `".modularTools::computerReadable($data[$field_name.'_label'])."` ".$data[$field_name.'_type']." NULL".$default." COMMENT '".$comment."'");
	    				}	
	    			}
	    			break;
	    	}
    	
			// Réécriture du cache suite au modification
        	modularTools::generateCache();
        }
        //Redirection	
    	header('Location: '.get_url('plugin/modular/module_edit/'.$module_slug));
    }
    
    function module_delete_column($module_slug, $column) {
    	$pdo = Record::getConnection();
    	$delete_table = Record::query('ALTER TABLE `modular_'.$module_slug.'` DROP `'.$column.'`');
    	
    	// Réécriture du cache suite au modification
        modularTools::generateCache();
    	//Redirection	
    	header('Location: '.get_url('plugin/modular/module_edit/'.$module_slug));
    }
    
    function module_delete($module_slug) {
    	$pdo = Record::getConnection();
    	$deleted_table = Record::query('DROP TABLE `modular_'.$module_slug.'`');
    	
    	// Réécriture du cache suite au modification
        modularTools::generateCache();
		
		//Redirection	
    	header('Location: '.get_url('plugin/modular/index'));
    }
    
    
    // Fonction relative au entrée
    function entry_add($module_slug) {
    	// Va chercher la liste des modules de modular dans la cache
      	$tables = modularTools::readCache();

         // Transmet les infos de la table à la view et l'affiche 
     	$this->display('modular/views/entry_add', array(
        'table' => $tables[$module_slug],
        'module_slug' => $module_slug
        ));
    }
    
    function entry_edit($module_slug, $id) {
    	$pdo = Record::getConnection();
		$entry = Record::query("SELECT * FROM `modular_".$module_slug."` WHERE `modular_".$module_slug."`.`id` = ".$id);
    	
    	// Va chercher la liste des modules de modular dans la cache
      	$tables = modularTools::readCache();
		
         // Transmet les infos de la table à la view et l'affiche 
     	$this->display('modular/views/entry_edit', array(
        'table' => $tables[$module_slug],
        'entry' => $entry,
        'module_slug' => $module_slug,
        'id' => $id
        ));
    }
    
    function entry_save($module_slug) {
    	$pdo = Record::getConnection();
    	$data = $_POST;
    	// Va chercher la liste des modules de modular dans la cache
      	$tables = modularTools::readCache();
        $table = $tables[$module_slug];
    	$db_name = explode(';', substr(DB_DSN, 13)); $db_name = $db_name[0];
    	
    	switch($data['modular_action']) {
    		case 'add_entry':
    			$field_list = '';
		    	$value_list = '';
		    	foreach($table as $field_name => $field) {
		    		if(isset($data[$field_name])) {
		    			$field_list .= '`'.$field_name.'`, ';
			    		switch($field['Type']) {
							case 'tinyint(1)':
								if($data[$field_name] == 'on') {
									$data[$field_name] = '1';
								}
								else {
									$data[$field_name] = '0';
								}
	    						break;
							default:
								if($data[$field_name] == '') {
					    			$data[$field_name] = 'NULL';
					    		}
					    		else {
					    			$data[$field_name] = "'".str_replace("'", "''", $data[$field_name])."'";
					    		}
								break; 
						}
						$value_list .= $data[$field_name].', ';
					}
					elseif($_FILES[$field_name]['tmp_name'] != '') {
						$field_list .= '`'.$field_name.'`, ';
			    		//Si file upload, uploader le fichier
			    		if($field['Type'] == 'varchar(255)' && $field['Comment'] == 'file_upload') {
								$modular_public_dir_path = CMS_ROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'modular';
								$module_dir_path = $modular_public_dir_path.DIRECTORY_SEPARATOR.$module_slug;
								$target_path = $module_dir_path.DIRECTORY_SEPARATOR.basename($_FILES[$field_name]['name']);
								if(!is_dir($modular_public_dir_path)) {
									mkdir($modular_public_dir_path);
								}
								if(!is_dir($module_dir_path)) {
									mkdir($module_dir_path);
								}
								move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_path);
								
								$data[$field_name] = "'".URI_PUBLIC.'public/modular/'.$module_slug.'/'.basename($_FILES[$field_name]['name'])."'";
							}
						$value_list .= $data[$field_name].', ';
			    	}
		    	}
		    	$field_list = substr($field_list, 0, -2);
		    	$value_list = substr($value_list, 0, -2);
		    	$added_entry = Record::query("INSERT INTO `".$db_name."`.`modular_".$module_slug."` (".$field_list.") VALUES (".$value_list.")");
    			break;
    		case 'edit_entry':
    			$edited_value_list = '';
		    	foreach($table as $field_name => $field) {
		    		if(isset($data[$field_name])) {
		    			$edited_value_list .= '`'.$field_name.'` = ';
			    		if($field_name != 'id') {
			    			switch($field['Type']) {
								case 'tinyint(1)':
									if($data[$field_name] == 'on') {
										$data[$field_name] = '1';
									}
									else {
										$data[$field_name] = '0';
									}
		    						break;
								default:
									if($data[$field_name] == '') {
						    			$data[$field_name] = 'NULL';
						    		}
						    		else {
						    			$data[$field_name] = "'".str_replace("'", "''", $data[$field_name])."'";
						    		}
									break; 
							}
			    		}
			    		$edited_value_list .= $data[$field_name].', ';
			    	}
			    	elseif($_FILES[$field_name]['tmp_name'] != '') {
			    		$edited_value_list .= '`'.$field_name.'` = ';
			    		//Si file upload, uploader le fichier
			    		if($field['Type'] == 'varchar(255)' && $field['Comment'] == 'file_upload') {
								$modular_public_dir_path = CMS_ROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'modular';
								$module_dir_path = $modular_public_dir_path.DIRECTORY_SEPARATOR.$module_slug;
								$target_path = $module_dir_path.DIRECTORY_SEPARATOR.basename($_FILES[$field_name]['name']);
								if(!is_dir($modular_public_dir_path)) {
									mkdir($modular_public_dir_path);
								}
								if(!is_dir($module_dir_path)) {
									mkdir($module_dir_path);
								}
								move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_path);
								
								$data[$field_name] = "'".URI_PUBLIC.'public/modular/'.$module_slug.'/'.basename($_FILES[$field_name]['name'])."'";
							}
						$edited_value_list .= $data[$field_name].', ';
			    	}
		    	}
		    	print_r($edited_value_list);
		    	$edited_value_list = substr($edited_value_list, 0, -2);
				$edited_entry = Record::query("UPDATE `".$db_name."`.`modular_".$module_slug."` SET ".$edited_value_list." WHERE `modular_".$module_slug."`.`id` =".$data['id'].";");
    			break;
    	}
    	//Redirection	
    	header('Location: '.get_url('plugin/modular/module_view/'.$module_slug));
    }
    
    function entry_delete($module_slug, $id) {
    	$pdo = Record::getConnection();
    	$db_name = explode(';', substr(DB_DSN, 13)); $db_name = $db_name[0];
    	$deleted_entry = Record::query("DELETE FROM `".$db_name."`.`modular_".$module_slug."` WHERE `modular_".$module_slug."`.`id` = ".$id);
    	//Redirection	
    	header('Location: '.get_url('plugin/modular/module_view/'.$module_slug));
    }
}
?>