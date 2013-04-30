<?php
require_once 'required_header_files.php'; 

$order=isset($_GET['order'])?$_GET['order']:'DESC';
$sortorder=isset($_GET["field"])?$_GET["field"]:"added";
$view = isset($_GET['view'])?$_GET['view']:'';
$historyTitle=$view=='all'?' (HISTORY)':'';
$ItemsRunningLow = new ItemRunningLow; 
				
$orderStatus_active = "";  
$added_active = "";  
$status_active =  "";  
$wine_active =  "";  
$orderStatus_active = "";
switch($sortorder){ //set how results will be sorted.
	case "status":
		$ItemsRunningLow->SortByStatus($order);
		$status_active = "class='active'";
		break;
	case "added":
		$ItemsRunningLow->SortByDate($order);
		$added_active = "class='active'";
		break;
	case "orderStatus":
		$ItemsRunningLow->SortByOrderStatus($order);
		$orderStatus_active = "class='active'";
		break;
	case "producer":
		$ItemsRunningLow->SortByProducer($order);
		break;
	case "distributor":
		$ItemsRunningLow->SortByDistributor($order);
		break;
	default:
		$ItemsRunningLow->SortByName($order);
		$wine_active = "class='active'";
}
$items = $ItemsRunningLow->LoadAll($view);
?>
<!-- this file calls in various files , this just holds the items list table -->
<input type=button value='hide' onClick='hideVendors()'>
<input type=button value='hide' onClick='showVendors()'>
<table border="1" BORDERCOLOR="#cccccc" cellspacing="0" cellpadding="2" width="100%" align="center">
			<tr>
			<th align="center" colspan="7">Low Inventory List<?php echo $historyTitle ?></th></tr>
			<tr width=100%> 
			<?php 
				if($order == 'asc')
					$order = 'desc';
				else
					$order = 'asc';
				
				if($order == 'desc')
					$field_order = '<img height="13" width="13" title="sort ascending" alt="sort icon" src="images/arrow-asc.png">';
				else
					$field_order = '<img height="13" width="13" title="sort ascending" alt="sort icon" src="images/arrow-desc.png">';
				
				?>
				 
				<th width="2%">&nbsp;</th> 

				<th width="33%" <?php echo $wine_active; ?>><a href="orderStatusLowItems.php?field=wine&order=<?php echo $order; ?>"><strong>Wine</strong> <?php if($wine_active) echo $field_order; ?></a></th>
				<th width="15%" <?php echo $status_active; ?>><a href="orderStatusLowItems.php?field=status&order=<?php echo $order; ?>"><strong>Status</strong> <?php if($status_active) echo $field_order; ?></a></th>
				<th width="20%" <?php echo $added_active; ?>><a href="orderStatusLowItems.php?field=added&order=<?php echo $order; ?>"><strong>Added</strong> <?php if($added_active) echo $field_order; ?></a></th>

				<th width="25%"  <?php echo $orderStatus_active; ?>><a href="orderStatusLowItems.php?field=orderStatus&order=<?php echo $order; ?>">Order Status  <?php if($orderStatus_active) echo $field_order; ?></a></th>
				<th width="20%">Notes</th>
				<th width="100px"><a href="orderStatusLowItems.php?field=producer"><strong>Producer</strong></a></th>
				<th width="100px"><a href="orderStatusLowItems.php?field=distributor"><strong>Distributor</strong></a></th>
			</tr>  
			
<?php
			$i=1;
			foreach($items as $data){  
				//----date difference start-------
				 $daysAgo = $data->DateDifference();
				//----date difference end------- 

				if($data->get_status()==1){
					$status = "<a href='JavaScript:void(0);' onClick='displayStatus(\"".$data->id."\")'>Running Low</a><br>";
					if($data->get_casesRemain()) $status .="{$data->get_casesRemain()} Cases" ;
					if($data->get_casesRemain() && $data->get_bottlesRemain()) $status.='<br> ';//insert a comma if needed																				
					if($data->get_bottlesRemain()) $status .="{$data->get_bottlesRemain()} Bottles ";

				}else{
					$status = "<a href='JavaScript:void(0);' onClick='displayStatus(\"".$data->id."\")'>Out of Stock</a>";
				} 

				$sameOriginalIDArr = $data->LoadByOriginalID($data->originalID);
				
				$statusDiv = '<div style="display:none" class="statusDisplay" id="'.$data->id.'" title="'.$data->producer.'&nbsp;'.$data->productname.'">
				<table width="100%" border="1"><tr><td>Wine</td><td>created By</td><td>CreatedDate</td><td>Status</td></tr> ';
				foreach($sameOriginalIDArr  as $key=>$val){ 
					if($val->get_status() == '1'){
						 $status_ =  "Running Low <br>";
						if($val->get_casesRemain()) $status_ .="{$val->get_casesRemain()} Cases" ;
						if($status_ && $val->get_bottlesRemain()) $status_.=', ';//insert a comma if needed																				
						if($val->get_bottlesRemain()) $status_ .="{$val->get_bottlesRemain()} Bottles ";

					}else{
						 $status_ = "Out of Stock";
					}
					$statusDiv.= '<tr>
							<td>'.$val->producer.' '.$val->productname.'</td>
							<td>'.$val->createdname.'</td>
							<td>'.$val->createdDate.'</td>
							<td>'.$status_.'</td> 
							</tr>';
				}
				$statusDiv.= '</table></div>';
				

				//----orderstatus div------
				$orderStatusStyle = '';
				if($data->orderStatus == 1){
						$orderStatusStyle = 'class="discontinuedClass"';
						$_orderStatus = 'discontinued';
				}
				if($data->orderStatus == 2){
						$orderStatusStyle = 'class="orderClass"';
						$_orderStatus = 'on order';
				}

				$orderstatus ="<div id='orderStatusDiv".$data->id."'>
						<input type='button' id='discontinued_".$data->id."' value='Discontinued'  onClick='return orderStatus(\"discontinued\",\"".$data->id."\")'  />&nbsp;
						<input type='button' value='On Order'  id='onOrder_".$data->id."' onClick='return orderStatus(\"order\",\"".$data->id."\")' /> </div>";
				
				if($data->orderStatus>0) 
						 $orderstatus = "<div id='orderStatusDiv".$data->id."'>
									".$_orderStatus.'<BR>
									'.$data->orderedDate.'
									<div style="float:right"><a href="#" onClick="removeStatus(\''.urlencode($data->producer).'\',\''.urlencode($data->productname).'\',\''.$data->id.'\')">clear</a></div>
									</div>';

				//----orderstatus div------


				$notes = "<div id='divNotes".$data->id."'>
							<textarea rows='2' cols='25' id='notes".$data->id."'  name='notes".$data->id."' >{$data->orderNotes}</textarea><br><input type='button' value='Save' onClick='saveNotes(".$data->id.")'>
					    </div>"; 

				if(trim($data->orderNotes)){ 
					$notes = '<div id="divNotes'.$data->id.'">
							<div id="notesValue'.$data->id.'">'.$data->orderNotes.'</div>
							<br><div style="float:right"><a href="#" onClick="editNotes(\''.$data->id.'\')">edit</a></div>
						</div>'; 
				}
				$printVintage=$data->vintage>1900?$data->vintage:'';
				echo"<tr id='tr".$data->id."' ".$orderStatusStyle."><td>$i</td>";
				echo"<td><a target='blank' href='/cgi-bin/admin/winemanager/wineform.asp?wineupc=".number_format($data->productID,0,'','')."'>{$data->producer} {$data->productname} {$printVintage}</a>";
				echo' <a onClick="return remove_confirm_order(\''.urlencode($data->producer).'\',\''.urlencode($data->productname).'\',\''.$data->id.'\')" href="JavaScript:void(0);" style="font-size:8pt">Remove</a></td>';

				echo"<td>{$statusDiv}{$status}</td>"; 
				echo"<td>{$daysAgo}<br> <span style='font-size:10pt'>{$data->createdDate}<br>By {$data->createdname}</span></td>";
				echo"<td align='center'>$orderstatus</td>"; 
				echo"<td>{$notes}</td>";
				echo"<td>{$data->producerName}</td>";
				echo"<td>{$data->distributorName}</td></tr>"; 

				$i++;
			}
			if($i==1)
				echo "<tr><td colspan='7' align='center'><br><br><b>No Items found</b></td></tr>";
				?>
			</table>
 