<?php
require_once 'required_header_files.php';

$ItemsRunningLow = new ItemRunningLow;
 

if(isset($_GET['getProducersByLetters']) && isset($_GET['letters'])){
	$letters = $_GET['letters'];
	$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
	
	$producers = $ItemsRunningLow->get_producers($letters);  

	//$res = mysql_query("select ID,countryName from ajax_countries where countryName like '".$letters."%'") or die(mysql_error());

	foreach($producers as $val){
		echo $inf["ID"]."###".$val."|";
	}	
}
 
?>
