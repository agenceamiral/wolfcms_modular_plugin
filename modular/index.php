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
Plugin::setInfos(array(
   'id'          => 'modular',
    'title'       => 'Modular', 
    'description' => 'Modular is a plugin to easily add tables with a handler for each one. In addition, it allows easy access to data in public views.', 
    'version'     => '0.1.4 beta',
    'license'     => 'GPL',
    'author'      => 'Amiral Agence Web',
    'website'     => 'http://www.agenceamiral.com/',
    'update_url'  => 'http://www.agenceamiral.com/wolf/modular.xml'
));

Plugin::addController('modular', 'Modular', 'page_edit');
Plugin::addJavascript('modular', 'js/jquery-ui-timepicker-addon.js');
Plugin::addJavascript('modular', 'js/modular.js');
// Création des constante propre à modular
if (!defined('MODULAR_ROOT'))  define('MODULAR_ROOT', CORE_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'modular');
if (!defined('MODULAR_HELPER_PATH'))  define('MODULAR_HELPER_PATH', MODULAR_ROOT.DIRECTORY_SEPARATOR.'helpers');
if (!defined('MODULAR_CACHE_FILE_PATH'))  define('MODULAR_CACHE_PATH', MODULAR_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'modular.cache');
//Inclusion des helpers propre à modular
include_once(MODULAR_HELPER_PATH.DIRECTORY_SEPARATOR.'modular_tools.php');

function modular_get($module_slug, $user_options = array()) {
	$default_options = array(
		'fields' => array('*'),
		'conditions' => '',
		'order' => '',
		'limit' => ''
	);
	$options = array_merge($default_options, $user_options);
	
	//Gestion des champs demandé
	$options['fields'] = join(', ', $options['fields']);
	if($options['fields'] != '*') {
		$options['fields'] .= ', id';
	}
	//Gestion des conditions indiqués
	if($options['conditions'] != '') {
		$options['conditions'] = ' WHERE '.$options['conditions'];
	}
	//Gestion de l'order by
	if($options['order'] != '') {
		$options['order'] = ' ORDER BY '.$options['order'];
	}
	//Gestion du paginator
	if($options['limit'] != '') {
		$start = 0;
		if(isset($_GET['page'])) {
			$start = $_GET['page'] * $options['limit'] - $options['limit'];
			if($start < 0) { $start = 0; }
		}
		$options['limit'] = ' LIMIT '.$start.', '.$options['limit'];
	}
	
    $module_slug = modularTools::computerReadable($module_slug);
    
    $pdo = Record::getConnection();
    $query = "SELECT ".$options['fields']." FROM modular_".$module_slug." AS ".$module_slug.$options['conditions'].$options['order'].$options['limit'];
	$entries = Record::query($query);
	
	$data = array();
	
	foreach($entries as $entry){
		$data[$entry['id']] = $entry;
	}
    return $data;
}

function modular_paginate($module_slug, $command = 'complete', $limit = 10) {
	$module_slug = modularTools::computerReadable($module_slug);
	$pdo = Record::getConnection();
	
	$total_of_record_obj = Record::query('SELECT COUNT(*) AS total FROM modular_'.$module_slug);
	$total_of_record = $total_of_record_obj->fetch(PDO::FETCH_ASSOC); $total_of_record = $total_of_record['total'];
	$actual_page = 1;
	if(isset($_GET['page'])) {
		$actual_page = $_GET['page'];
	}
	$actual_last = $actual_page * $limit;
	
	if(strpos($_SERVER["REQUEST_URI"], 'page='.$actual_page) !== false) {
		$first_part = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], 'page='.$actual_page));
	}
	else {
		$first_part = $_SERVER["REQUEST_URI"].'?';
	}
	
	if(substr($first_part, 1, 1) == '?') {
		$first_part = substr($first_part, 2);
	}
	
	$first_part =  substr($first_part, 1);
	
	switch($command) {
		case 'next':
			if($total_of_record > $actual_last) {
				$html = '<span class="modular_next modular_paginate"><a href="'.get_url($first_part.'page='.($actual_page+1)).'">'.__('Next').'</a></span>';
			}
			else {
				$html = '<span class="modular_next modular_disabled modular_paginate">'.__('Next').'</span>';
			}
			
			break;
			
		case 'prev':
			if($actual_last - $limit*$limit >= 0) {
				$html = '<span class="modular_prev modular_paginate"><a href="'.get_url($first_part.'page='.($actual_page-1)).'">'.__('Prev.').'</a></span>';
			}
			else {
				$html = '<span class="modular_prev modular_disabled modular_paginate">'.__('Prev.').'</span>';
			}
			
			break;
	}
	return $html;
}
?>