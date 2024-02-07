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
$accesskey =  trim($data->accesskey);
$sno =  trim($data->sno);
$response = array();
try {
if(!empty($accesskey) && !empty($sno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$stmt = $check -> fetch(PDO::FETCH_ASSOC);
	$getsno = $pdoread -> prepare("SELECT `cen_sno` ,`issued_qty` FROM `indent_queue` WHERE `sno` =:sno AND `issued_status`!='delete'");
$getsno->bindParam(':sno',$sno, PDO::PARAM_STR);
$getsno-> execute();
if($getsno -> rowCount() > 0){
$getcno = $getsno -> fetch(PDO::FETCH_ASSOC);	
	$updt = $pdo4->prepare("UPDATE `indent_queue` SET `issued_qty`='0',`issued_status`='delete',`modified_by` = :userid,`modified_on`=CURRENT_TIMESTAMP WHERE `sno` =:sno");
	$updt->bindParam(':userid',$stmt['userid'], PDO::PARAM_STR);
	$updt->bindParam(':sno',$sno, PDO::PARAM_STR);
	
    $updt-> execute();
if($updt -> rowCount() > 0){
	
	$stmt1 = $pdo4->prepare("INSERT INTO `department_issued_logs`(`queue_sno`, `indent_no`, `branch`, `itemcode`, `itemname`, `indent_qty`, `issued_qty`, `batch_no`, `expiry_date`, `priority`, `status`, `created_on`, `createdby`, `ref_itemno`,`cen_sno`)SELECT `sno`, `indent_no`,  `branch`,  `itemcode`, `itemname`, `qty`,:val, `batch_no`,`exp_date`, `priority`,'delete', `modified_on`, `modified_by`, `ref_itemno`,`cen_sno` FROM `indent_queue` WHERE `sno` =:sno");
	 $stmt1->bindParam(':sno',$sno, PDO::PARAM_STR);
	 $stmt1->bindParam(':val',$getcno['issued_qty'], PDO::PARAM_STR);
  $stmt1-> execute();
	if($stmt1 -> rowCount() > 0){
	 $updt1 = $pdo4->prepare("UPDATE `central_store` SET `onhand`=(`onhand`+:val) WHERE `sno`=:cen_sno");
	$updt1->bindParam(':cen_sno',$getcno['cen_sno'], PDO::PARAM_STR);
	$updt1->bindParam(':val',$getcno['issued_qty'], PDO::PARAM_STR);
	$updt1-> execute();
      http_response_code(200);
	  $response['error']= false;
	  $response['message']= "Item Deleted Successfully";
	}
	}
}else{
	http_response_code(503);
			$response['error']= true;
	          $response['message']="Item Not Deleted";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}

}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(Exception $e) {

	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
	 