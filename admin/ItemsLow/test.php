<?php
require_once 'c:/inetpub/wwwroot/cgi-bin/IncludeCode/configDB.php';
require_once 'c:/inetpub/wwwroot/cgi-bin/IncludeCode/Classes/ItemLowClass.php'; 
$ItemsRunningLow = new ItemRunningLow; 
$producers = $ItemsRunningLow->get_producers(); 
?>
<HTML>
<HEAD>
<TITLE>
</TITLE>
<script type="text/javascript" src="jquery/jquery-1.4.2.js"></script>  
</HEAD>

<BODY>  
<div style="width:900px;margin:auto;background-color:#ccc">
 
	 <div style="width:50%;margin:auto;">
		 <div style="text-align:center"><h2>Items Low in Stock</h2> </div>
		 <form method="post" name="frmlowitem" id="frmlowitem" action='lowitemAdd.php'>
		 <table cellpadding="5"><tr>
		 <td>Producer</td>
		 <td>
		 
		 <select name="producer" id="producer"  >
		 	<option>Select Producer</option>
		 	<option>Producer1</option>					
		 	<option>Producer2</option>					
		 	<option>Producer3</option>					
		 	<option>Producer4</option>					
		 </select></td>
		 </tr> 
		 <tr>
		 <td>Product Name</td>
		 <td><div id="cproduct_name">
				<select name="product_name" id="product_name" > <option>Select Producer from top First</option></select>
			</div></td>
		 </tr> 
		
		</table>
		</form> 
		<br><br>&nbsp;<br>
	  </div>


  
	
	&nbsp;<BR>&nbsp;<BR>&nbsp;<BR>
</div>

 
 

<script type="text/javascript"> 
	$.ajaxSetup ({
		cache: false
	});
	var ajax_load = "loading..."; 
 	var loadUrl = "getproducts.php";
	$("#producer").change(function(){
		val = $('select#producer option:selected').val(); 
  		$("#cproduct_name").html(ajax_load);
	});   
	 
</script> 
</BODY>
</HTML>