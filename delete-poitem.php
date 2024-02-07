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
//get payload
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
	$empdata = $check -> fetch(PDO::FETCH_ASSOC);
	$empname = $empdata['userid'];
/* delete poitem id */
$delete_query = "UPDATE `po_item` SET `modified_on`= CURRENT_TIMESTAMP,`modifiedby`=:empname,`status`=:del  WHERE `po_item`.`id` = :poid";
$delete_sbmt  = $pdo4 -> prepare($delete_query);
$delete_sbmt -> bindParam(":poid", $poid, PDO::PARAM_STR);
$delete_sbmt -> bindParam(":empname", $empname, PDO::PARAM_STR);
$delete_sbmt -> bindParam(":del", $delete, PDO::PARAM_STR);
$delete_sbmt -> execute();
if($delete_sbmt -> rowCount() > 0){
	http_response_code(200);
     $response['error']  = false;
     $response['message'] = "PO Item Deleted!";
}
else{
	http_response_code(503);
     $response['error'] = true;
     $response['message'] = "Sorry Soemtthing Went Wrong";
}
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