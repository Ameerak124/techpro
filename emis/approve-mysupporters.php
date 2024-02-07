<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$sno = $data->sno;
$response = array();

try {

   if(!empty($accesskey)){  
   
    $check = $pdoread -> prepare("SELECT `userid` ,`cost_center`,`role` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
    $check -> execute();
    if($check -> rowCount() > 0){
    $result = $check->fetch(PDO::FETCH_ASSOC);
			
if($result['role']=='Center Head'){
	
$stmt1=$pdoread->prepare("SELECT `sno`, `unique_id`, `billno`, `umrno`, `invoice_no`, `ucid`, `trans_id`, `branch`, `created_on`, `created_by`, `ch_on`, `ch_by`, `ch_status`, `audit_on`, `audit_by`, `audit_status`, `status` FROM `mysupporters_mapping` WHERE `sno`=:sno AND `ch_status` = 'Pending' AND `status` = 'Pending'");

$stmt1->bindParam(':sno', $sno, PDO::PARAM_STR);
$stmt1 -> execute();		
if($stmt1 -> rowCount() > 0){

	
$insert=$pdo4->prepare("UPDATE `mysupporters_mapping` SET `ch_on`= CURRENT_TIMESTAMP,`ch_by`= :userid,`ch_status`='Approved' WHERE `ch_status` = 'Pending' AND `status` = 'Pending' AND `sno`=:sno");

$insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$insert->bindParam(':sno', $sno, PDO::PARAM_STR);
$insert->execute();
if($insert->rowCount() > 0){
	
	http_response_code(200);
    $response['error']=false;
    $response['message']="Data Approved Successfully";
	

}else{
	http_response_code(400);
  $response['error']= true;
$response['message']= "Data Not Approved";
}

}else{
	http_response_code(400);
  $response['error']= true;
$response['message']= "Data Already Approved";
}

}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Unauthorized Access";
}

}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}

}catch(PDOException $e) {
http_response_code(503);
$response['error'] = true;
$response['message']= "Connection failed: ".$e;
}
echo json_encode($response);
unset($pdoread);
unset($pdo4);
?>