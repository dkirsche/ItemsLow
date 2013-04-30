<?php
// this file will call on ajax call for remove item----- 
require_once 'required_header_files.php';

	$ItemsRunningLow = new ItemRunningLow; 
	$userid = isset($_SESSION["userid"])?$_SESSION["userid"]:0;  
 
	$lowItemId = isset($_POST['id'])?$_POST['id']:0;   
 	$_orderNotes = isset($_POST['notes'])?$_POST['notes']:0;  

	$remove_item = $ItemsRunningLow->Load($lowItemId);  
	$ItemsRunningLow->orderNotes=$_orderNotes;  

	$ItemsRunningLow->set_orderStatus($userid);    
	$items = $ItemsRunningLow->Load($lowItemId);
	
	echo  '<div id="notesValue'.$ItemsRunningLow->id.'">'.$ItemsRunningLow->orderNotes.'</div>
							<br><div style="float:right"><a href="#" onClick="editNotes(\''.$ItemsRunningLow->id.'\')">edit</a></div>';
?>