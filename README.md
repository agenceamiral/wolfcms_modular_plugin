Modular Wolf CMS Plugin
=====
http://agenceamiral.com/labs/wolfcms_modular

Modular is a plugin for Wolf CMS that allow usage of custom data tables and fields within the pages of the CMS.
It provides a simple interface for creating data tables and custom fields from the control panel. 
It also provides an easy way for the editors to create and edit entries. 

It currently support 7 types of custom fields :

* Text field
* Text area
* Checkbox
* Options list
* Date & time
* Date
* Time


Installation
===================================

Put the "modular" directory in wolf/plugins


Usage
===================================

Let's say you create an event table with date, title, description and "active" custom fields

Example code :

<?php 

	$active_events = modular_get('events', array('conditions' => 'active = 1','order'=>'date ASC')); 
	
	foreach($active_events as $event) {
		echo $event['date'].'<br/>';
		echo $event['title'].'<br/>';
		echo $event['descrition'].'<br/>';
	}
?>