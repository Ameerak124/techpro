<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$response = array();
$data = json_decode(file_get_contents("php://input"));
if(!empty($data)){    
}else{
$dataa = json_encode($_POST);
$data = json_decode($dataa);
}
$itemcode =  trim($data->editemcode);
$quantity =  trim($data->edquantity);
$purchaseprice =  trim($data->edunitprice);
$accesskey =  trim($data->accesskey);
$uom =  trim($data->eduom);
$poid =  trim($data->edpoid);
$pono =  trim($data->edpono);
$gsttype =  trim($data->edgsttype);
$gstper =  trim($data->edgstper);
$itemqtystatus = 'Pending';
/* data object end */
try {
if(!empty($accesskey) && !empty($itemcode) && !empty($pono) && $quantity > 0 && $purchaseprice > 0 && !empty($poid)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$empdata = $check -> fetch(PDO::FETCH_ASSOC);
	$empname = $empdata['userid'];
/* get vendorname based on id */
$vendor_query = "SELECT `po_number`, SUBSTRING(`vendormaster`.`gstno`,1,2) AS gst_state FROM `po_generate` INNER JOIN `vendormaster` ON `po_generate`.`vendor_sno` = `vendormaster`.`sno` WHERE `po_generate`.`po_number` = :ponumber  LIMIT 1";
$vendor_sbmt = $pdoread ->prepare($vendor_query);
$vendor_sbmt -> bindParam(":ponumber", $pono, PDO::PARAM_STR);
$vendor_sbmt -> execute();
if($vendor_sbmt -> rowCount() > 0){
	$vendordata = $vendor_sbmt -> fetch(PDO::FETCH_ASSOC);
	$ponumber = $vendordata['po_number'];
	$vendorstate = $vendordata['gst_state'];
	if(!empty($vendorstate) ){ 
		/* gst gst code */
		$getgst_query = "SELECT  `gst` FROM `item_master` WHERE `item_master`.`itemcode` = :itemcode";
		$getgst_sbmt  = $pdoread -> prepare($getgst_query);
		$getgst_sbmt -> bindParam(":itemcode", $itemcode, PDO::PARAM_STR);
		$getgst_sbmt -> execute();
		if($getgst_sbmt -> rowCount() > 0) {
			$gstdata = $getgst_sbmt -> fetch(PDO::FETCH_ASSOC);
			$gstcode = $gstdata['gst'];
			$gst_per = substr($gstcode,3);
		/* gst calculations */
		 $purchasevalue = $purchaseprice * $quantity ;
		if($vendorstate != "27"){
			$gsttype = "SGST";
			$igst = ($purchasevalue/100)*($gst_per);
			$sgst = 0;
			$cgst = 0;
		}
		else{
			$gsttype = "IGST";
			$sgst = ($purchasevalue/100)*($gst_per/2);
			$cgst = $sgst;
			$igst = 0;
		
		}
		$total = ($purchasevalue + $sgst + $cgst +$igst);
		/* update po item */
		$udpate_query = "UPDATE `po_item` SET `item_qty`=:itemqty,`unit_price`= :purchaseprice,`gst_type`=:gsttype,`gst_per`= :gstper,`igst`=:igst,`sgst`= :sgst,`cgst`= :cgst,`uom`=:uom,`total`=:total,`base_total`= :basetotal,`balance_qty`= :balanceqty,`item_qty_status`= :itemqtystatus,`modified_on`= CURRENT_TIMESTAMP,`modifiedby`= :empname WHERE `po_item`.`id` = :poitemid";
		$update_sbmt = $pdo4 -> prepare($udpate_query);
		$update_sbmt -> bindParam(":igst", $igst, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":sgst", $sgst, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":cgst", $cgst, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":uom", $uom, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":total", $total, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":basetotal", $purchasevalue, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":balanceqty", $quantity, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":itemqtystatus", $itemqtystatus, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":gstper", $gst_per, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":gsttype", $gsttype, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":purchaseprice", $purchaseprice, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":itemqty", $quantity, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":poitemid", $poid, PDO::PARAM_STR);
		$update_sbmt -> bindParam(":empname", $empname, PDO::PARAM_STR);
		$update_sbmt ->execute();
		if($update_sbmt -> rowCount() > 0){
	    http_response_code(200);		
		$response['error']= false;
	     $response['message']="Item Update!";
		}
		else
		{
			http_response_code(503);
		$response['error']= true;
	     $response['message']="Sorry Something Went Wrong";
		}
		/* update poitem end */

	}
	else{
		http_response_code(503);
		$response['error']= true;
	     $response['message']="Warning:Invalid GST Details";

	}
	}
	else{
		http_response_code(503);
		$response['error']= true;
	     $response['message']="Warning:Invalid Vendor GST Number"; 
	}
	}
else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Warning: Invalid vendor details";
}
/* get vendorname based on id */
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>