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
$accesskey=trim($data->accesskey);
$transid=trim($data->transid);

try {
     if(!empty($accesskey)&& !empty($transid)) {    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//check and cancel slot
$cancel_slot=$pdo4->prepare("UPDATE `patient_details` SET `slot_status`='cancelled',`modifiedon`=CURRENT_TIMESTAMP,`modifiedby`=:userid WHERE `transid`=:transid AND `slot_status`='booked' ");
$cancel_slot->bindParam(':transid', $transid, PDO::PARAM_STR);
$cancel_slot->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$cancel_slot->execute();
if($cancel_slot->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Slot Cancelled Success";
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Something Went Wrong";
}

}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {	
    http_response_code(503);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
	
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>