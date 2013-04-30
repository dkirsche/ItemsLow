<?php

class ItemRunningLow{
	public $id,$productID,$createdBy,$createdByName,$createdDate,$active,$producer,$productname,$vintage, $originalID;
	public $orderStatus; //1 - discontinued,2 - on order 
	public $orderNotes, $orderedDate;
	public $orderedByName,$producerName,$distributorName; //these values can not be set or saved they are loaded from other tables.
	private $casesRemain,$bottlesRemain;
	private $status; //1 - running low
					//2 - out of stock
					//0 - clear, when a wine is no longer running low status is set to 0.
	private $sortBy;//this is used to set the order by property when sending a query to the db 
	//PROPERTY METHODS
	//the only way to set the bottles and cases remaining is in this method when status=1.
	public function set_status($status,$cases=0,$bottles=0){
		//only allow either 0, 1 or 2 for status	
		if($status<0 || $status>2)
			return 0;  

 		$this->status=$status;
		//only allow cases and bottles to be set if status=1 (running low).
		if($this->status==1){
			$this->casesRemain=$cases;
			$this->bottlesRemain=$bottles;
		}
		return 1;
	}
	
	public function get_status(){
		return $this->status;
	}
	public function get_casesRemain(){
		return $this->casesRemain;
	}
	public function get_bottlesRemain(){
		return $this->bottlesRemain;
	}
	public function SortByName($sortDirection='asc'){
		$this->sortBy="order by wines.producer,wines.productname {$sortDirection}";
	}
	public function SortByStatus($sortDirection='desc'){
		$this->sortBy="order by itemslow.status {$sortDirection}";
	}
	public function SortByDate($sortDirection='desc'){
		$this->sortBy="order by itemslow.createdDate {$sortDirection}";
	}
	public function SortByOrderStatus($sortDirection='desc'){
		$this->sortBy="order by itemslow_orderstatus.status {$sortDirection}";
	}
	public function SortByProducer($sortDirection='asc'){
		$this->sortBy="order by producername {$sortDirection}";
	}
	public function SortByDistributor($sortDirection='asc'){
		$this->sortBy="order by distributorname {$sortDirection}";
	}

	//END PROPERTIES
	
	//Consturctor used to initialize all variables.
	public function ItemRunningLow(){
		$this->id=0;
		$this->productID=0;
		$this->createdBy=0;
		$this->createdByName='';
		$this->createdDate='1/1/2000';
		$this->active=0;
		$this->casesRemain=0;
		$this->bottlesRemain=0;
		$this->status=0;
		$this->originalID=0;
		$this->orderStatus=0;
		$this->orderNotes="";
		$this->orderedDate='1/1/2001';
		$this->orderedByName="";
		$this->sortBy='order by wines.producer,wines.productname';
		$this->producerName='';
		$this->distributorName='';
	}

	public function Load($id){  
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status, itemslow.originalID,wines.producer,wines.productname,wines.vintage, 
		itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate,os.login as osByName,distributors.name as distributorname,produceraccounts.companyname as producername
		FROM itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid
		left join distributors on wines.distributorid=distributors.id
		left join produceraccounts on wines.producerid=produceraccounts.id
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID and  itemslow_orderstatus.createddate=(select max(createddate) from itemslow_orderstatus where originalid=itemslow.originalid)
		left join users os on os.id=itemslow_orderstatus.UserID
		WHERE itemslow.id={$id}";  
		
		$result = mssql_query($query); 
		if($row=mssql_fetch_array($result)){  
			$this->id=$row["id"];
			$this->productID=$row["productid"];
			$this->createdBy=$row["createdby"];	
			$this->createdByName=$row["createdname"];	
			$this->createdDate=$row["createdDate"];
			$this->active=$row["active"];
			$this->casesRemain=$row["casesremain"];
			$this->bottlesRemain=$row["bottlesremain"];
			$this->status=$row["status"]; 
			$this->producer=$row["producer"];
			$this->productname=$row["productname"];
			$this->vintage=$row["vintage"];
			$this->originalID=$row["originalID"];
			$this->producerName=$row["producername"];
			$this->distributorName=$row["distributorname"];
			//------orderstatus---- 
			$this->orderStatus = empty($row["orderStatus"])?0:$row["orderStatus"];
			$this->orderNotes = $row["orderNotes"];
			$this->orderedByName = $row["osByName"];	
			$this->orderedDate = $row["orderedDate"];
 			//------orderstatus----
			return 1;
		}
		return 0;  
	}
	public function LoadAll($view='active'){  
 		$itemslowArr=array();
 		$active='';
 		if($view == 'active' || $view == '')
			$active.= ' where active=1 ';
			
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status,itemslow.originalID,wines.producer,wines.productname,wines.vintage,
		itemslow_orderstatus.id orderStatusId, itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate, os.login as osByName,distributors.name as distributorname,produceraccounts.companyname as producername  
		from itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid
		left join distributors on wines.distributorid=distributors.id
		left join produceraccounts on wines.producerid=produceraccounts.id
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID and  itemslow_orderstatus.createddate=(select max(createddate) from itemslow_orderstatus where originalid=itemslow.originalid)
		left join users os on os.id=itemslow_orderstatus.UserID {$active} {$this->sortBy}";
		

		$result = mssql_query($query); 
	
		while($row=mssql_fetch_array($result)){   
			$SS = new ItemRunningLow;
			$SS->id=$row["id"];
			$SS->productID=$row["productid"];
			$SS->createdby=$row["createdby"];	
			$SS->createdname=$row["createdname"];	
			$SS->createdDate=$row["createdDate"];
			$SS->active=$row["active"];
			$SS->casesRemain=$row["casesremain"];
			$SS->bottlesRemain=$row["bottlesremain"];
			$SS->status=$row["status"]; 
			$SS->producer=$row["producer"];
			$SS->productname=$row["productname"];
			$SS->vintage=$row["vintage"];
			$SS->originalID=$row["originalID"];
			$SS->producerName=$row["producername"];
			$SS->distributorName=$row["distributorname"];
			//------orderstatus---- 
			$SS->orderStatus = empty($row["orderStatus"])?0:$row["orderStatus"];
			$SS->orderNotes = $row["orderNotes"];
			$SS->orderedByName = $row["osByName"];	
			$SS->orderedDate = $row["orderedDate"];
 			//------orderstatus----
			$itemslowArr[]=$SS;
		}
		return $itemslowArr;  
	}
	public function LoadByOriginalID($originalID){
		// function to get all items of same orignalId  
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status,itemslow.originalID,wines.producer,wines.productname,wines.vintage,
		itemslow_orderstatus.id orderStatusId, itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate, os.login as osByName,distributors.name as distributorname,produceraccounts.companyname as producername
		from itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid
		left join distributors on wines.distributorid=distributors.id
		left join produceraccounts on wines.producerid=produceraccounts.id
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID and  itemslow_orderstatus.createddate=(select max(createddate) from itemslow_orderstatus where originalid=itemslow.originalid)
		left join users os on os.id=itemslow_orderstatus.UserID
		where itemslow.originalID=".$originalID." order by itemslow.id";  
		$result = mssql_query($query); 
	
		while($row=mssql_fetch_array($result)){ 
			$SS = new ItemRunningLow;
			$SS->id=$row["id"];
			$SS->productID=$row["productid"];
			$SS->createdby=$row["createdby"];	
			$SS->createdname=$row["createdname"];	
			$SS->createdDate=$row["createdDate"];
			$SS->active=$row["active"];
			$SS->casesRemain=$row["casesremain"];
			$SS->bottlesRemain=$row["bottlesremain"];
			$SS->status=$row["status"]; 
			$SS->producer=$row["producer"];
			$SS->productname=$row["productname"];
			$SS->vintage=$row["vintage"];
			$SS->originalID=$row["originalID"];
			$SS->producerName=$row["producername"];
			$SS->distributorName=$row["distributorname"];
			//------orderstatus---- 
			$SS->orderStatus = empty($row["orderStatus"])?0:$row["orderStatus"];
			$SS->orderNotes = $row["orderNotes"];
			$SS->orderedByName = $row["osByName"];	
			$SS->orderedDate = $row["orderedDate"];
 			//------orderstatus----
			$itemslowArr[]=$SS;
		}
		return $itemslowArr; 
	}
	//loads items from a specific distributor or producer.
	//type: 1 - Supplier(default)
	//		2 - Distributor
	//openOrders:1 - show only items that do not have an orderstatus i.e. items that have not been ordered or discontinued.
	//			  0 - show all items
	public function LoadByVendor($type,$vendorID,$openOrders=false){
		$field="";
		$field=$type==2?"distributorID":"ProducerID";
		$itemslowArr=array();	
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status,itemslow.originalID,wines.producer,wines.productname,wines.vintage,
		itemslow_orderstatus.id orderStatusId, itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate, os.login as osByName,distributors.name as distributorname,produceraccounts.companyname as producername
		from itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid
		left join distributors on wines.distributorid=distributors.id
		left join produceraccounts on wines.producerid=produceraccounts.id
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID and  itemslow_orderstatus.createddate=(select max(createddate) from itemslow_orderstatus where originalid=itemslow.originalid)
		left join users os on os.id=itemslow_orderstatus.UserID
		where wines.{$field}={$vendorID} and itemslow.active=1 {$this->sortBy}";  
		//echo $query;
		$result = mssql_query($query); 
	
		while($row=mssql_fetch_array($result)){ 
			//this checks to see if we care about orderstatus
			if($openOrders && !empty($row["orderStatus"]))
				continue;
			$SS = new ItemRunningLow;
			$SS->id=$row["id"];
			$SS->productID=$row["productid"];
			$SS->createdby=$row["createdby"];	
			$SS->createdname=$row["createdname"];	
			$SS->createdDate=$row["createdDate"];
			$SS->active=$row["active"];
			$SS->casesRemain=$row["casesremain"];
			$SS->bottlesRemain=$row["bottlesremain"];
			$SS->status=$row["status"]; 
			$SS->producer=$row["producer"];
			$SS->productname=$row["productname"];
			$SS->vintage=$row["vintage"];
			$SS->originalID=$row["originalID"];
			$SS->producerName=$row["producername"];
			$SS->distributorName=$row["distributorname"];
			//------orderstatus---- 
			$SS->orderStatus = empty($row["orderStatus"])?0:$row["orderStatus"];
			$SS->orderNotes = $row["orderNotes"];
			$SS->orderedByName = $row["osByName"];	
			$SS->orderedDate = $row["orderedDate"];
 			//------orderstatus----
			$itemslowArr[]=$SS;
		}
		return $itemslowArr; 
	}
	//check if there is an active record for the product. if so return the originalid
	public function CheckActive(){
		$query="select originalID from itemslow where productid={$this->productID} and active=1";
		mssql_query($query); 
		$result = mssql_query($query); 
		if(!($row=mssql_fetch_array($result)))
			return 0;
		else
			return $row["originalID"];
	}
//always run CheckActive before you call save to find out if there is a record 
//that is active already. You should then set the originalid to this record before saving.
	public function Save(){
		if(!$this->Validate()) return 0;
 		//----condition for remove and insert new items---
		$query = "update itemslow set active='0' where productid='".$this->productID."'";
		mssql_query($query); 

		$query = "insert into itemslow (productid,createdby, active, casesRemain, bottlesRemain, status, originalID)  		
					VALUES({$this->productID},{$this->createdBy},{$this->active},{$this->casesRemain},{$this->bottlesRemain},{$this->status},{$this->originalID});
					select @@identity"; 
					//select @@identity
		$result = mssql_query($query); 		

		//if no records returned the the insert failed 
		if(!($row=mssql_fetch_array($result)))
			return 0;
		$id=$row[0];//saves the new id to a variable;
		
		//if originalID=0 then it hasn't been set which means it isn't part of a larger group of itemslow. We must set the originalID=id
		if($this->originalID==0){
			$query = "update itemslow set originalID ={$id} where id={$id}";
			mssql_query($query); 
		}
		$this->Load($id); //load the record because there are certain fields that the db filled in when inserting. 
		return 1;
	}


private function Validate(){
	if($this->status==1){
		if($this->bottlesRemain<=0 && $this->casesRemain<=0)
			return 0;
	}
	if($this->productID==0)
		return 0;
	if($this->createdBy==0)
		return 0;
	return 1;
}	

public function DateDifference(){ 
	$today=mktime(0, 0, 0, date('m'), date('d'), date('Y')); //today's unix timestamp
	$createdTime=strtotime($this->createdDate);//convert datecreated into unix timestamp
	$dateCreated=mktime(0, 0, 0, date('m',$createdTime), date('d',$createdTime), date('Y',$createdTime)); //we need to cleans the date so that time is omitted and only the date part is used to create the time stamp
	$days=($today-$dateCreated)/(60*60*24);
	$days=round($days,0); //we have to round because daylight savings time messes up mktime and this will add a fraction to the day.
	$returnVal=$days==0?"Today":"{$days} days ago";
	return $returnVal;
		
	}

	

	public function set_orderStatus($userID){
		//- This method should insert the values into the table ItemsLow_OrderStatus
		$this->clear_orderStatus();
		$cleanNote=ms_escape_string($this->orderNotes);
		$query = "insert into  itemslow_orderstatus (originalId, status, notes, userId)  		
		VALUES({$this->originalID},{$this->orderStatus},'{$cleanNote}',{$userID});
					"; 
		$result = mssql_query($query); 		
		$this->Load($this->id); 
	}
	//this function is called by PurchaseOrder when a PO has been submitted.
	//will set the order status of all active ItemLow records that have been order in the PO
	public static function set_orderStatus_PO($poid){
		//- This method should insert the values into the table ItemsLow_OrderStatus
		$query = "insert into itemslow_orderstatus (originalid,status,notes,userid) 
				select originalid,2, 'PO {$poid}',9 
				from itemslow 
				join purchaseorderitem on itemslow.productid=purchaseorderitem.productid 
				where purchaseorderitem.poid={$poid} and itemslow.active=1;
					"; 
		$result = mssql_query($query); 		
		return 1;
	}
	public static function clear_orderStatus_PO($poid){
		$query="delete from itemslow_orderstatus where notes='PO {$poid}'";
		$result = mssql_query($query); 	
	}
    public function clear_orderStatus(){
		//- deletes from ItemsLow_OrderStatus where OriginalID=$origID
		$query = "delete from itemslow_orderstatus where originalId=".$this->originalID."";
					 
		$result = mssql_query($query); 		
	}
	
	public function get_producers(){   
		$producerArr=array();
		$query = "select distinct producer from wines where status>=0";
		$result = mssql_query($query); 
		while($row=mssql_fetch_array($result)){   
			$producerArr[]=$row["producer"]; 
		}
		return $producerArr;  
	}

	public function get_products($id){   
		$productsrArr=array();
		$producer=ms_escape_string($id);
		$query = "select productid, productname,vintage from wines where producer='{$producer}' and status>-1";
		$result = mssql_query($query);  
 		while($row = mssql_fetch_object($result)){  
			$productname=$row->vintage>0?$row->productname.' '.$row->vintage:$row->productname;
			$productsrArr[]= array("product_name" => $productname, "productid" => $row->productid); 
		} 

		return $productsrArr;  
	}

}


?>