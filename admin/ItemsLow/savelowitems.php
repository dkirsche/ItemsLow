<?php
// this file will call on ajax call----- 
require_once 'required_header_files.php';

	$status=isset($_POST["status"])?$_POST["status"]:-1;   
	$cs=isset($_POST["cs"])?$_POST["cs"]:0;
	$btls=isset($_POST["btls"])?$_POST["btls"]:0;
	$id=isset($_POST["id"])?$_POST["id"]:0;
	$productid=isset($_POST["productid"])?$_POST["productid"]:0;
		

	$ItemsRunningLow = new ItemRunningLow;

	$userid = isset($_SESSION["userid"])?$_SESSION["userid"]:0;   
	//sets the originalid if there is already an active record with this productid.
	$ItemsRunningLow->productID = $productid;
	$ItemsRunningLow->originalID=$ItemsRunningLow->CheckActive();
	//---condition for update functionality---- 
	if($id){
			$updateItemsRunningLow = new ItemRunningLow;
			$updateItems = $updateItemsRunningLow->Load($id);  
			$ItemsRunningLow->originalID = $updateItemsRunningLow->originalID;
	}

	$ItemsRunningLow->createdBy = $userid;
	//$createdDate,
	$ItemsRunningLow->active = '1';
	$ItemsRunningLow->set_status($status,$cs,$btls); 
	//print_r($ItemsRunningLow);exit;
	$ItemsRunningLow->Save();  

	require_once 'lowItemsList.php';
?>  