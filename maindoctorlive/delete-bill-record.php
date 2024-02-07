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
include "laboratory-data-delete.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$sno = strtoupper($data->sno);
$remarks = trim($data->remarks);
/* $ipaddress = $_SERVER['REMOTE_ADDR']; */
try {
if(!empty($accesskey) && !empty($sno)&& !empty($remarks)){
	//
	
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Updated details
$saleprice = $pdo4 -> prepare("UPDATE `billing_history` SET `modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP,`status`='Hidden' , `remarks` = :remarks WHERE (`sno` = :sno AND `status` = 'Hold') OR (`sno` = :sno AND `status` = 'Transit' AND `service_status` = 'No Update') OR (`sno` = :sno AND `status` = 'Visible' AND `service_status` = 'No Update')");
$saleprice->bindParam(':sno', $sno, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
//$saleprice->bindParam(':ipaddress', $ipaddress , PDO::PARAM_STR);
$saleprice->bindParam(':remarks', $remarks , PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Service is deleted";
    $del_worklist=$pdo->prepare("SELECT `ipno`,`billno`,`requisition_no`,`service_status`,`category` FROM `billing_history` WHERE `sno` =:sno");
    $del_worklist->bindParam(':sno', $sno, PDO::PARAM_STR);
$del_worklist-> execute();
$work_list_details= $del_worklist->fetch(PDO::FETCH_ASSOC);
iplabremoveworklist($pdo,$result['userid'],$work_list_details['requisition_no']);
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Service is not available";
}
// 
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
	//
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
$pdo4 = null;
?>