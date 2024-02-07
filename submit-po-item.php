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
$uom =  trim($data->uom);
$gst =  trim($data->gst);
$slno =  trim($data->slno);
$accesskey =  trim($data->accesskey);
$purchaseprice =  trim($data->purchaseprice);
$quantity =  trim($data->quantity);
$itemcode =  trim($data->itemcode);
$itemname =  trim($data->itemname);
$ponumber =  trim($data->ponumber);
$mfr =  trim($data->mfr);
$gst_per = substr($gst,3);
/* data object end */
try {

if(!empty($accesskey) && !empty($itemname) && !empty($itemcode) && !empty($ponumber) && !empty($gst) && !empty($ponumber) ){
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
$vendor_sbmt -> bindParam(":ponumber", $ponumber, PDO::PARAM_STR);
$vendor_sbmt -> execute();
if($vendor_sbmt -> rowCount() > 0){
	$vendordata = $vendor_sbmt -> fetch(PDO::FETCH_ASSOC);
	$ponumber = $vendordata['po_number'];
	$vendorstate = $vendordata['gst_state'];
	if(!empty($vendorstate) ){ 
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
		/* insert po item */

		$poitem_query = "INSERT INTO `po_item`(`po_no`, `item_name`, `item_code`, `item_qty`, `unit_price`, `gst_type`, `gst_per`,`igst`, `sgst`, `cgst`, `uom`,`total`, `base_total`, `created_on`, `createdby`,`status`, `balance_qty`, `item_qty_status`, `mfr`) VALUES (:po_no,:item_name,:item_code,:item_qty, :unit_price, :gst_type, :gst_per, :igst, :sgst, :cgst, :uom, :total, :base_total, CURRENT_TIMESTAMP, :createdby, :postatus, :balance_qty, :item_qty_status, :mfr )";
		$poitem_sbmt = $pdo4 -> prepare($poitem_query);
		$poitem_sbmt -> bindParam(":po_no",$ponumber, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":item_name",$itemname, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":item_code",$itemcode, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":item_qty",$quantity, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":unit_price",$purchaseprice, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":gst_type",$gsttype, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":gst_per",$gst_per, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":igst",$igst, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":sgst",$sgst, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":cgst",$cgst, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":uom",$uom, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":total",$total, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":base_total",$purchasevalue, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":createdby",$empname, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":postatus",$openstatus, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":balance_qty",$quantity, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":item_qty_status",$pending, PDO::PARAM_STR);
		$poitem_sbmt -> bindParam(":mfr",$mfr, PDO::PARAM_STR);
		$pending = 'Pending';
		$openstatus = "Open";
		$poitem_sbmt -> execute();
		if($poitem_sbmt -> rowCount() > 0){
			http_response_code(200);
			$response['error']= false;
	          $response['message']= "Item Added!"; 
		}
		else{
			http_response_code(503);
			$response['error']= true;
	          $response['message']="Something went wrong"; 
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