<?php
require_once 'required_header_files.php';
$view=isset($_GET['view'])?$_GET['view']:'';
?>
<!DOCTYPE html> 
<HTML>
<HEAD>
<TITLE>Low Inventory List</TITLE> 
  <link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/> 
  <link href="style.css" rel="stylesheet" type="text/css"/>  
</HEAD> 
<BODY>  
<div class="container" style='padding:10px 10px;'>
	<a href='../menuinventory.php'>< Inventory Menu</a><br>
	<div style="width:50%;margin:auto;" id="formDiv">
		 <?php require_once 'lowItemsForm.php'; ?>
	</div>

	<div style="float:right;padding-right:50px;"><a href='lowitems.php'>Add Item</a></div>

	<?php if($view == 'all'){ ?>
	<div style="float:left;"><a href='lowitems.php?view=active'>show current list</a></div>
	<?php }else{ ?>
	<div style="float:left;"><a href='lowitems.php?view=all'>show history</a></div>
	<?php } ?>

	<div style="clear:both"><br></div>

	<div id="lowitemlist" style="width:100%">
		<?php require_once 'lowItemsList.php'; ?> 
	</div>
	&nbsp;<BR>&nbsp;<BR>&nbsp;<BR>
</div>
<script type="text/javascript" src="jquery/jquery-1.4.2.js"></script>   
<script type="text/javascript" src="jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery/jquery-impromptu.3.1.min.js"></script> 
<script type="text/javascript" src="jquery/lowItemsJsFunctions.js"></script>  

<?php
 $producers = $ItemsRunningLow->get_producers();  
 $autocomplete = '';
 $producers1 = '';
 foreach($producers as $val){
	$autocomplete.= '"'.addslashes($val).'", ';
 }
?>
<script>
 $(document).ready(function() { 
     $("input#producer").autocomplete({
    source: [<?php echo $autocomplete ?>]
}); }); 

</script>
</BODY>
</HTML>