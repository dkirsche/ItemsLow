<?php

class ItemRunningLow{
	public $id,$productID,$createdBy,$createdByName,$createdDate,$active,$producer,$productname,$originalID;
	public $orderStatus; //1 - discontinued,2 - on order 
	public $orderNotes, $orderedByName, $orderedDate;
	private $casesRemain,$bottlesRemain;
	private $status; //1 - running low
					//2 - out of stock
	
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
	}

	public function Load($id){  
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status, itemslow.originalID,wines.producer,wines.productname, 
		itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate,os.login as osByName 
		FROM itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid   
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID 
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
			$this->originalID=$row["originalID"];
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
	public function LoadAll($sortby='',$order='desc', $view='active'){  
 		$itemslowArr=array();
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status,itemslow.originalID,wines.producer,wines.productname,
		itemslow_orderstatus.id orderStatusId, itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate, os.login as osByName  
		from itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid  
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID 
		left join users os on os.id=itemslow_orderstatus.UserID";

		if($view == 'active' || $view == '')
			$query.= ' where active=1 ';


		switch ($sortby){
			case 'wine':
				$query.= " order by wines.producer {$order}, wines.productname asc";
				break;
			case 'status':
				$query.= " order by itemslow.status ".$order;
				break;
			case 'added':
				$query.= " order by itemslow.createdDate  ".$order;
				break;
			case 'orderStatus':
				$query.= " order by  itemslow_orderstatus.status  ".$order;
				break;
			default:
				$query.= " order by  itemslow.createdDate  ASC";
			}	

		$result = mssql_query($query); 
	
		while($row=mssql_fetch_array($result)){   
			$SS = new ItemRunningLow;
			$SS->id=$row["id"];
			$SS->productid=$row["productid"];
			$SS->createdby=$row["createdby"];	
			$SS->createdname=$row["createdname"];	
			$SS->createdDate=$row["createdDate"];
			$SS->active=$row["active"];
			$SS->casesRemain=$row["casesremain"];
			$SS->bottlesRemain=$row["bottlesremain"];
			$SS->status=$row["status"]; 
			$SS->producer=$row["producer"];
			$SS->productname=$row["productname"];
			$SS->originalID=$row["originalID"];
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
		$sameOriginalIDArr=array();
		$query = "select itemslow.id,itemslow.productid,createdby,users.login as createdname,itemslow.createdDate,itemslow.active, casesremain,bottlesremain,itemslow.status,itemslow.originalID,wines.producer,wines.productname,
		itemslow_orderstatus.id orderStatusId, itemslow_orderstatus.status orderStatus, itemslow_orderstatus.notes orderNotes,itemslow_orderstatus.createdDate orderedDate, os.login as osByName  
		from itemslow 
		join users on itemslow.createdby=users.id 
		join wines on itemslow.productid=wines.productid  
		left join  itemslow_orderstatus on  itemslow_orderstatus.originalId=itemslow.originalID 
		left join users os on os.id=itemslow_orderstatus.UserID
		where itemslow.originalID=".$originalID." order by itemslow.id";  
		$result = mssql_query($query); 
	
		while($row=mssql_fetch_array($result)){ 
			$SS = new ItemRunningLow;
			$SS->id=$row["id"];
			$SS->productid=$row["productid"];
			$SS->createdby=$row["createdby"];	
			$SS->createdname=$row["createdname"];	
			$SS->createdDate=$row["createdDate"];
			$SS->active=$row["active"];
			$SS->casesRemain=$row["casesremain"];
			$SS->bottlesRemain=$row["bottlesremain"];
			$SS->status=$row["status"]; 
			$SS->producer=$row["producer"];
			$SS->productname=$row["productname"];
			$SS->originalID=$row["originalID"];
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
		/*
		$id= mssql_insert_id();
		$query = "update itemslow set originalID ={$id} where id={$id}";
			mssql_query($query); 
		*/
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