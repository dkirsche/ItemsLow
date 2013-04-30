<?php
// this file will call on ajax call for remove item----- 
require_once 'required_header_files.php'; 

	$ItemsRunningLow = new ItemRunningLow; 

	$lowItemId = isset($_GET['id'])?$_GET['id']:0;   
	$remove_item = $ItemsRunningLow->Load($lowItemId);  
	$ItemsRunningLow->active = 0;
	$ItemsRunningLow->set_status(0,'','');
	$ItemsRunningLow->Save();  
 
	$items = $ItemsRunningLow->LoadAll();  
	require_once 'lowItemsList.php';
?>