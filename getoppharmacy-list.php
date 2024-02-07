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
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$fromdate =date('Y-m-d', strtotime($data->fromdate));
$todate = date('Y-m-d', strtotime($data->todate));
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
//$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `op_pharmacy_inv_item`.`so_number` AS sonumber,date_format(`op_pharmacy_inv_generate`.`modified_on`,'%d-%b-%y %h:%i %p') AS invoicedate ,  `op_pharmacy_inv_item`.`item_code` , `op_pharmacy_inv_item`.`item_name` , `op_pharmacy_inv_item`.`batch_no` , `op_pharmacy_inv_item`.`expiry_date` , `op_pharmacy_inv_item`.`purchase_price` , `op_pharmacy_inv_item`.`mrp` AS sale , `op_pharmacy_inv_item`.`quantity` , `op_pharmacy_inv_item`.`total` , `op_pharmacy_inv_generate`.`created_by` AS userid FROM `op_pharmacy_inv_generate` INNER JOIN `op_pharmacy_inv_item` ON `op_pharmacy_inv_item`.`so_number` = `op_pharmacy_inv_generate`.`inv_no` WHERE `op_pharmacy_inv_generate`.`status` = 'Completed' AND `op_pharmacy_inv_item`.`item_status` != 'Delete' AND `op_pharmacy_inv_generate`.`cost_center` = :costcenter AND Date(`op_pharmacy_inv_generate`.`modified_on`) BETWEEN :fromdate AND :todate;");
	$reglist -> bindParam(":costcenter" , $result['cost_center'] , PDO::PARAM_STR);
	$reglist -> bindParam(":todate" , $todate , PDO::PARAM_STR);
	$reglist -> bindParam(":fromdate" , $fromdate , PDO::PARAM_STR);
$reglist -> execute();
if($reglist -> rowCount() > 0){
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	$response['qty']= "Qty";
	$response['batch']= "Batch";
	$response['sale']= "Sale";
	$response['purchase']= "Purchase";
	$response['expon']= "expiry on";
	$response['fromdate']= $fromdate;
	$response['todate']=  $todate;
	while($regres = $reglist->fetch(PDO::FETCH_ASSOC)){
		$response['oppharmacybillinglist'][] = $regres;
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>