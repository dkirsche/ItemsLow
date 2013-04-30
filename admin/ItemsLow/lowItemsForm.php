<?php
require_once 'required_header_files.php';

//this file calls in various files , this just holds the add/update form for low items  

$ItemsRunningLow = new ItemRunningLow; 
$edit_items = 0;// this bool tracks whether we are editing a current record or if this is a new record.
$producers = $ItemsRunningLow->get_producers();  
$id=isset($_GET['id'])?$_GET['id'] :0;
$op=isset($_GET['op'])?$_GET['op'] :0;

if($id && $op == 'update'){
	$edit_items = $ItemsRunningLow->Load($id);  
} 

?>  

<div style="text-align:center;padding:0px 0px;"><h2>Add Item to Low Inventory</h2> </div> 
		 <form method="post" name="frmlowitem" id="frmlowitem" action='lowitemAdd.php'> 
		 <input type="hidden"  name="productid" value="<?php echo $ItemsRunningLow->productID;?>">
		 <input type="hidden"  name="id"  id="id" value="<?php echo $ItemsRunningLow->id;?>">  
		 <table cellpadding="5"><tr>
		 <td>Producer</td>
		 <td> 
		 <input type="text" name="producer" id="producer" <?php if($edit_items) echo 'disabled'; ?> onBlur="getProducers()"  onKeyPress="decode_producer()"  value="<?php echo $ItemsRunningLow->producer;?>"/>   
		 </td>
		 </tr> 
		 <tr>
		 <td>Product Name</td>
		 <td><div id="cproduct_name"> 
			 <?php 
				 if($id){
						 $products = $ItemsRunningLow->get_products($ItemsRunningLow->producer);
						 $product_options = '<option>Select Product</option> ';
						 foreach($products  as $key=>$val){  
							 if($ItemsRunningLow->productID == $val['productid'])
								$product_options .='<option value="'.$val['productid'].'" selected >'.$val['product_name'].'</option>';
							 else
								 $product_options .='<option value="'.$val['productid'].'">'.$val['product_name'].'</option>';
						 }
				 }else{
					   $product_options = '<option>Add Producer First</option>';
				 }
			 ?> 
			<select name="product_name" id="product_name"  <?php if($edit_items) echo 'disabled'; ?> ><?php echo $product_options ;?></select>
			</div></td>
		 </tr> 
		 <tr>
		 <td> 
				<?php
				$status_out_of_stock = '';
					if($ItemsRunningLow->get_status() == 2)
						$status_out_of_stock = 'checked'; 
				?>
				<input type="radio" id="status_out_of_stock"  name="status"  value=2 <?php echo $status_out_of_stock; ?> />Out of Stock</td>
			<td>
				<?php
					$running_low = '';
					if($ItemsRunningLow->get_status() == 1)
						$running_low = 'checked'; 
				?>
				<input type="radio" id="status_running_low"  name="status" value=1 <?php echo $running_low; ?>/>Running Low</td>
		 </tr> 
		 <tr>
		  <td></td>
		 <td>
			<input type="Number" value='<?php echo $ItemsRunningLow->get_casesRemain();?>' id="cs" name="cs" size="3" maxlength=3/>Cases &nbsp;&nbsp;&nbsp;<input type="Number" value='<?php echo $ItemsRunningLow->get_bottlesRemain();?>' id="btls"  name="btls" size="3" maxlength=3/>Bottles</td> 
		 </tr>  
		 <tr>
		  <td><input type="button" id="Submit" value='Submit' onClick="submitItems()"></td>
		 </tr>
		</table>
		</form> 
		<br> 