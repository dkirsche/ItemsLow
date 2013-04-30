$.ajaxSetup ({
	cache: false
});
var ajax_load = "loading...";

function getProducers(){ 
	var loadUrl = "getproducts.php";
	document.getElementById("producer").value = decode(document.getElementById("producer").value);
	
	var check = $(".ui-autocomplete").attr("style");
	if(check.search("none") > 0){
		val = document.getElementById("producer").value;  
		$("#cproduct_name").html(ajax_load).load(loadUrl, "q="+val);
	}
}

function decode_producer(){
	document.getElementById("producer").value = decode(document.getElementById("producer").value);

}
function submitItems(){ 
		var producer = document.getElementById("producer").value;
		var product_name = document.getElementById("product_name").value;
		var status =  $("input[name='status']:checked").val();
		var cs =  document.getElementById("cs").value ;
		var btls =  document.getElementById("btls").value ; 
		var id =  document.getElementById("id").value;

		if(producer == ""){
			alert("Please fill Producer textfield");
			return false;
		} 
		if(product_name == "Select Product"){
			if(producer != ""){
				alert("Please insert Valid Producer");
				return false;
			}
			alert("Please "+product_name);
			return false;
		}

		if(!status){
			alert("Please select status");
			return false;
		}
		if(status == '1'){
			if(cs ==''){
				cs=0;
			}
			if(btls ==''){
				btls=0;
			}
			if(cs =='0' && btls =='0'){
				alert("Data cannot be saved, at least one of the fields of cs and btl should have a value>0");
				return false;
			}
		}
/* For testing purposes
		if(document.getElementById(id))
			alert(document.getElementById(id).title)
		else
			alert("could not find element");
	return false;
*/
		
		alert("producer:"+ producer+", productid:"+product_name+", status:"+status+ ",cs:"+cs+", btls:"+btls+", id:"+id);
		var loadUrl = "savelowitems.php"; 
		$("#lowitemlist")
			.html(ajax_load).load(loadUrl, {producer: producer, productid:product_name, status:status,cs:cs, btls:btls, id:id}); 

		document.getElementById("product_name")[0].selected=true;
		document.getElementById("cs").value ='' ;
		document.getElementById("btls").value ='' ; 
		document.getElementById("id").value = '';
 }
	  
function decode(str) {
		return unescape(str.replace(/\+/g, " "));
}

function remove_confirm(producer, product_name, id){ 
		var producer = decode(producer);
		var product_name = decode(product_name)
		var r=confirm("Are you sure you would like to remove "+producer+" "+product_name);
		if (r==true){ 
				remove_data(id);
				return false;
		  }
		else{
				return false;
		  }
}

function remove_data(id){
		loadUrl = "removeItem.php"; 
		$("#lowitemlist").html(ajax_load).load(loadUrl, "id="+id+"&op=remove"); 
}

function itemUpdate(id){  
			loadUrl = "lowItemsForm.php"; 
			$("#formDiv").html(ajax_load).load(loadUrl, "id="+id+"&op=update"); 

			
}  

function displayStatus(id){  
	$.fn.extend({
	dropIn: function(speed, callback){
		var $t = $(this);

		if($t.css("display") == "none"){
			eltop = $t.css('top');
			elouterHeight = $t.outerHeight(true);

			$t.css({ top: -elouterHeight, display: 'block' })
				.animate({ top: eltop },speed,'swing', callback);
		}
	}
});


	$.prompt($("#"+id+"").html(),{ show:'dropIn', buttons: {Close: false } });
		//$("#"+id+"").html(); 
}
 
function orderStatus(who, id){   
		 var ajax_load = "loading..."; 
		 var loadUrl = "saveOrderStatus.php";  
		 if(document.getElementById("notes"+id+""))
			 var notes = document.getElementById("notes"+id+"").value;   
		
		$("#orderStatusDiv"+id+"").html(ajax_load).load(loadUrl, {notes: notes, who:who,id:id}); 
		
		if(who == 'discontinued'){
			$("#tr"+id+"").removeClass('orderClass');
			$("#tr"+id+"").addClass('discontinuedClass'); 
		}
		else{
			$("#tr"+id+"").removeClass('discontinuedClass');
			$("#tr"+id+"").addClass('orderClass'); 
		}  
}

function remove_confirm_order(producer, product_name, id){
		var producer = decode(producer);
		var product_name = decode(product_name)
		var r=confirm("Are you sure you would like to remove "+producer+" "+product_name);
		if(r==true){
				remove_data_order(id);
				return false;
		}
		else{
				return false;
		}
} 
function remove_data_order(id){ 
	loadUrl = "removeOrderItem.php"; 
 	$("#tr"+id+"").html(ajax_load).load(loadUrl, "id="+id+"&op=remove");
}

function removeStatus(producer, product_name, id){ 
		var r=confirm("Are you sure you would like to remove "+decode(producer)+" "+decode(product_name)+" order status");
		if(r==true){
				remove_order_Status(id);
				return false;
		}
		else{
				return false;
		}
}
function remove_order_Status(id){
		$("#tr"+id+"").removeClass('orderClass');
		$("#tr"+id+"").removeClass('discontinuedClass');
		
		loadUrl = "remove_order_Status.php"; 
 		$("#orderStatusDiv"+id+"").html(ajax_load).load(loadUrl, "id="+id+"&op=remove");
}

function editNotes(id){  

	var notes = $("#notesValue"+id+"").html(); 

	$("#divNotes"+id+"").html("<textarea rows='2' cols='25' id='notes"+id+"'  name='notes"+id+"' >"+notes+"</textarea><br><input type='button' value='Save' onClick='saveNotes("+id+")'>"); 
}

function saveNotes(id){   
	 var notes ;
	if(document.getElementById("notes"+id+""))
			 var notes = document.getElementById("notes"+id+"").value;   
  	var loadUrl = "saveOrderNotes.php";  
	$("#divNotes"+id+"").html(ajax_load).load(loadUrl, {notes: notes,id:id}); 
}

function POgetProducers(){ 
	var loadUrl = "ajaxPOgetproducts.php";
	document.getElementById("POproducer").value = decode(document.getElementById("POproducer").value);

 	val = document.getElementById("POproducer").value;  
	$("#POproduct").html(ajax_load).load(loadUrl, "q="+val);
}


function submitPO(){ 
		var producer = document.getElementById("POproducer").value;
		var product_name = document.getElementById("POproduct").value;
		 
		if(producer == ""){
			alert("Please fill Producer textfield");
			return false;
		} 
		if(product_name == "Select Product"){
			if(producer != ""){
				alert("Please insert Valid Producer");
				return false;
			}
			alert("Please "+product_name);
			return false;
		}
 
		var loadUrl = "ajaxPOSave.php"; 
		$("#POlist").html(ajax_load).load(loadUrl, {producer: producer, productid:product_name}); 
 }
 function hideVendors(){ 
	$('td:nth-child(7),th:nth-child(7)').hide();
	$('td:nth-child(8),th:nth-child(8)').hide();
}
 function showVendors(){ 
	$('td:nth-child(7),th:nth-child(7)').show();
	$('td:nth-child(8),th:nth-child(8)').show();
}