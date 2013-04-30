<?php
// this file will call on ajax call for remove item----- 
require_once 'required_header_files.php';

	$ItemsRunningLow = new ItemRunningLow; 
	$userid = isset($_SESSION["userid"])?$_SESSION["userid"]:0;  
 
	$lowItemId = isset($_GET['id'])?$_GET['id']:0;   
 	
	$remove_item = $ItemsRunningLow->Load($lowItemId);  
	$ItemsRunningLow->orderStatus=0;  
	$ItemsRunningLow->set_orderStatus($userid);    
 	
	echo  "<input type='button' id='discontinued_".$ItemsRunningLow->id."' value='Discontinued'  onClick='return orderStatus(\"discontinued\",\"".$ItemsRunningLow->id."\")'  />&nbsp;
						<input type='button' value='On Order'  id='onOrder_".$ItemsRunningLow->id."' onClick='return orderStatus(\"order\",\"".$ItemsRunningLow->id."\")' /> ";
	exit;
?>