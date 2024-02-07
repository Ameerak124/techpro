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
$accesskey= $data->accesskey;
$status= $data->status;
$department= $data->department;
$fdate=$data->fdate;
$tdate=$data->tdate;
$response = array();
try{
if(!empty($accesskey)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
if(empty($department) && empty($fdate) && empty($tdate) && empty($status)){
$stmt2=$pdoread->prepare("SELECT `sno` AS slno, `return_id`,(SELECT COALESCE(Count(`sno`),'0') FROM `return_item` WHERE  `status`!='Delete' AND return_id=A1.return_id Group by `return_id`) AS lineitems, `department`,`created_by`, Date_format(`created_on`,'%d-%b-%Y %H:%S %p') As raisedon, `status` FROM `generate_return` As A1");
}
else if(empty($status)){
$stmt2=$pdoread->prepare("SELECT `sno` AS slno,`return_id`,(SELECT COALESCE(Count(`sno`),'0') FROM `return_item` WHERE  `status`!='Delete' AND return_id=A1.return_id Group by `return_id`) AS lineitems, `department`,`created_by`, Date_format(`created_on`,'%d-%b-%Y %H:%S %p') As raisedon, `status` FROM `generate_return` As A1 WHERE `department`=:dept AND Date(created_on) between :fdate AND :tdate");
$stmt2->bindParam(':dept', $department, PDO::PARAM_STR);
$stmt2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$stmt2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
}
else if(empty($department) && empty($fdate) && empty($tdate) && !empty($status)){
$stmt2=$pdoread->prepare("SELECT `sno` AS slno,`return_id`,(SELECT COALESCE(Count(`sno`),'0') FROM `return_item` WHERE  `status`!='Delete' AND return_id=A1.return_id Group by `return_id`) AS lineitems, `department`,`created_by`, Date_format(`created_on`,'%d-%b-%Y %H:%S %p') As raisedon, `status` FROM `generate_return` As A1 WHERE status=:status");
$stmt2->bindParam(':status', $status, PDO::PARAM_STR);

}
else{
$stmt2=$pdoread->prepare("SELECT `sno` AS slno,`return_id`,(SELECT COALESCE(Count(`sno`),'0') FROM `return_item` WHERE  `status`!='Delete' AND return_id=A1.return_id Group by `return_id`) AS lineitems, `department`,`created_by`, Date_format(`created_on`,'%d-%b-%Y %H:%S %p') As raisedon, `status` FROM `generate_return` As A1 WHERE status=:status AND  `department`=:dept AND Date(created_on) between :fdate AND :tdate");
$stmt2->bindParam(':status', $status, PDO::PARAM_STR);
$stmt2->bindParam(':dept', $department, PDO::PARAM_STR);
$stmt2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$stmt2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
}
$stmt2-> execute();
	if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
          $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
	    $response['returnlist']= $data;
     }
	
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
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
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>
