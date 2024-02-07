<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ip_no=trim($data->ip_no);
$id=trim($data->id);
$remarks=trim($data->remarks);
try {
if(!empty($accesskey) && !empty($ip_no)&& !empty($id) ){    
$check = $pdoread-> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$update_remarks=$pdo4->prepare("UPDATE `drug_medication` SET `drug_time_remarks`=:remarks,`modified_by`=:userid, `modified_on`=CURRENT_TIMESTAMP WHERE `sno`=:id AND `ip_no`=:ip_no ");
$update_remarks->bindParam(':ip_no', $ip_no, PDO::PARAM_STR);
$update_remarks->bindParam(':id', $id, PDO::PARAM_STR);
$update_remarks->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$update_remarks->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update_remarks->execute();
if($update_remarks->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Remarks Updated Successfully";
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Please Try Again";
}

}else {	
	http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied";
}
}else {
	http_response_code(400);	
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

}   catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>