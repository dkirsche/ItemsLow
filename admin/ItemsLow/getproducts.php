<?php
require_once 'required_header_files.php';
  
  $ItemsRunningLow = new ItemRunningLow;
  
  $producer_name=isset($_GET['q'])?$_GET['q']:0;  
  $producer = stripslashes($producer_name);
  $products = $ItemsRunningLow->get_products($producer); 
?> 

<select name="product_name" id="product_name">  
<option>Select Product</option> 
<?php
	foreach($products  as $key=>$val){  
 		echo '<option value="'.$val['productid'].'">'.$val['product_name'].'</option>';
	}
?>
</select> 