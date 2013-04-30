<?php
require_once 'required_header_files.php';
$order=isset($_GET['order'])?$_GET['order']:'desc';
$sortorder=isset($_GET['field'])?$_GET['field']:'added';
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

<table border="1" cellspacing="0" cellpadding="4" width="100%" align="center"   BORDERCOLOR="#cccccc">
			<tr>
			<th colspan="6">Low Inventory List<?php echo $historyTitle ?></th></tr>
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
				<th width="2%"><strong></strong></th>
				
				<th width="30%" <?php echo $wine_active; ?>><a href="lowitems.php?field=wine&order=<?php echo $order; ?>"><strong>Wine</strong> <?php if($wine_active) echo $field_order; ?></a></th>
				<th width="13%" <?php echo $status_active; ?>><a href="lowitems.php?field=status&order=<?php echo $order; ?>"><strong>Status</strong> <?php if($status_active) echo $field_order; ?></a></th>
				<th width="19%" <?php echo $added_active; ?>><a href="lowitems.php?field=added&order=<?php echo $order; ?>"><strong>Added</strong> <?php if($added_active) echo $field_order; ?></a></th>
				<th width="10%"><strong>By</strong></th>
				<th width="28%"><strong>Notes</strong></th>
				
			</tr>  
			
<?php
			$i=1;
			foreach($items as $data){  
				//----date difference start-------
				 $daysAgo = $data->dateDifference($data->createdDate);
				//----date difference end------- 
				
				if($data->get_status()==1){
					$status = "<a href='JavaScript:void(0);' onClick='displayStatus(\"".$data->id."\")'>Running Low</a> <br>";
					if($data->get_casesRemain()) $status .="{$data->get_casesRemain()}&nbsp;Cases" ;
					if($data->get_casesRemain() && $data->get_bottlesRemain()) $status.=', ';//insert a comma if needed																				
					if($data->get_bottlesRemain()) $status .="{$data->get_bottlesRemain()}&nbsp;Bottles ";
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
						if($val->get_casesRemain() && $val->get_bottlesRemain()) $status_.=', ';//insert a comma if needed																				
						if($val->get_bottlesRemain()) $status_ .="{$val->get_bottlesRemain()} Bottles ";

					}else{
						 $status_ = "Out of Stock";
					}
					
					$statusDiv.= '<tr>
							<td>'.$val->producer.' '.$val->productname." ".$val->vintage.'</td>
							<td>'.$val->createdname.'</td>
							<td>'.$val->createdDate.'</td>
							<td>'.$status_.'</td>
							</tr>';
				}
				$statusDiv.= '</table></div>';
				
				$orderStatusStyle = '';
				if($data->orderStatus == 1){
						$orderStatusStyle = 'class="discontinuedClass"';
 				}
				if($data->orderStatus == 2){
						$orderStatusStyle = 'class="orderClass"';
 				}
				$printVintage=$data->vintage>1900?$data->vintage:'';
				echo"<tr ".$orderStatusStyle."><td>$i</td>";
				echo"<td ".$wine_active.">{$data->producer} {$data->productname} {$printVintage}<br>".
						'<a onClick="return itemUpdate(\''.$data->id.'\')"  href="JavaScript:void(0);">Update</a> / 
						<a onClick="return remove_confirm(\''.urlencode($data->producer).'\',\''.urlencode($data->productname).'\',\''.$data->id.'\')" href="JavaScript:void(0);">Remove</a></td>';
				echo"<td ".$status_active.">$statusDiv{$status}</td>"; 
				echo"<td ".$added_active.">{$daysAgo}<br> <span style='font-size:10pt'>({$data->createdDate})</span></td>";
				echo"<td>{$data->createdname}</td>"; 
				echo"<td>{$data->orderNotes}</td></tr>"; 
				$i++;
			}
			if($i==1)
				echo "<tr><td colspan='7' align='center'><br><br><b>No Items found</b></td></tr>";
				?>
			</table>
 