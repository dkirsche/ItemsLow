<?php
require_once 'required_header_files.php';
$view=isset($_GET['view'])?$_GET['view']:'';
?>
<HTML>
<HEAD>
<TITLE>Manage Low Inventory</TITLE> 
 <link href="style.css" rel="stylesheet" type="text/css"/>  
</HEAD> 
<BODY>  
<div class="container" style='padding:10px 10px;'>  
	<a href='../menuinventory.php'>< Inventory Menu</a><br>
	<div style="clear:both"><br></div>
<?php if($view == 'all'){ ?>
	<div style="float:left;"><a href='orderstatuslowitems.php?view=active'>show current list</a></div>
	<?php }else{ ?>
	<div style="float:left;"><a href='orderstatuslowitems.php?view=all'>show history</a></div>
	<?php } ?>
	<div id="orderLowItemlist" style="width:100%">
		<?php require_once 'orderStatuslowItemsList.php'; ?> 
	</div>

</div>
<?php
require_once 'footer.php';
?>