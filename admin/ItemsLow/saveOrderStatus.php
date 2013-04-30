<?php
// this file will call on ajax call----- 
require_once 'required_header_files.php';

	$userid = isset($_SESSION["userid"])?$_SESSION["userid"]:0;  
 
 $ItemsRunningLow = new ItemRunningLow;
 $itemLowId = isset($_POST["id"])?$_POST["id"]:0;
 $who=isset($_POST["who"])?$_POST["who"]:0;
 //$notes=isset($_POST["notes"])?$_POST["notes"]:"";
 $updateItems = $ItemsRunningLow->Load($itemLowId);   

 if($who == 'discontinued')
	$_orderStatus = 1;
 else
	$_orderStatus = 2; 
 
 $ItemsRunningLow->orderStatus=$_orderStatus;
 $ItemsRunningLow->set_orderStatus($userid);  


//----orderstatus div------ 
if($ItemsRunningLow->orderStatus == 1)
		$_orderStatus = 'discontinued';
if($ItemsRunningLow->orderStatus == 2)
		$_orderStatus = 'on order'; 

if($ItemsRunningLow->orderStatus) 
	  $orderstatus = $_orderStatus.'<BR>'.$ItemsRunningLow->orderedDate.'<div style="float:right"><a href="#" onClick="removeStatus(\''.urlencode($ItemsRunningLow->producer).'\',\''.urlencode($ItemsRunningLow->productname).'\',\''.$ItemsRunningLow->id.'\')">clear</a></div>';

//----orderstatus div------ 
echo $orderstatus;  
?>  