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
$poid = trim($data->poid);
$delete = 'delete';
try {
if(!empty($accesskey) && !empty($poid)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
//get item list start
$list_query = "SELECT `po_no`, `item_name`, `item_code`, `item_qty`, `unit_price`,`uom`, `po_item`.`gst_type`, `po_item`.`gst_per`,`po_item`.`po_no`  FROM `po_item` WHERE `po_item`.`id` = :poid  AND `po_item`.`status` != :del LIMIT 1";
$list_sbmt = $pdoread -> prepare($list_query);
$list_sbmt -> bindParam(":poid",$poid,PDO::PARAM_STR);
$list_sbmt -> bindParam(":del",$delete,PDO::PARAM_STR);
$list_sbmt -> execute();
if($list_sbmt -> rowCount() > 0){
	$response['list'] = $list_sbmt -> fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error'] = false;
	$response['messsage'] = "Data Found!";
}
else
{
	http_response_code(503);
	$response['error'] = true;
	$response['message'] = "No Data Found!";
}
//get item list end

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
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>