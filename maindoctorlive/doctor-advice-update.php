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
$doctor_advice = trim($data->advice_note);
$doctor_advice_created_date = date('Y-m-d', strtotime($data->reviewdate)); 
$doctor_advice_remarks = trim($data->remarks);
$requisition_no = trim($data->requisition_no);

try{
if(!empty($accesskey) && !empty($doctor_advice_created_date) && !empty($requisition_no)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result= $check->fetch(PDO::FETCH_ASSOC);
	

$stmt1 = $pdo4->prepare("UPDATE op_biling_history SET `doctor_advice`=:doctor_advice,`modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP ,`doctor_advice_remarks`=:doctor_advice_remarks ,`doctor_advice_created_date`=:doctor_advice_created_date  WHERE `status`='Visible' AND `requisition_no`=:reqno");
$stmt1->bindParam(':doctor_advice', $doctor_advice, PDO::PARAM_STR);
$stmt1->bindParam(':reqno', $requisition_no, PDO::PARAM_STR);
$stmt1->bindParam(':doctor_advice_created_date', $doctor_advice_created_date, PDO::PARAM_STR);
$stmt1->bindParam(':doctor_advice_remarks', $doctor_advice_remarks, PDO::PARAM_STR);
$stmt1->bindParam(':userid', $result['userid'], PDO::PARAM_STR);

$stmt1->execute();
if($stmt1->rowCount()>0){

http_response_code(200);
	$response['error']= false;
	$response['message']= "Updated Successfully";
	
	
}else{
		http_response_code(503);
	$response['error']=true;
	$response['message']="Sorry! Updation failed!";
	
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	http_response_code(400);
	$response['error']=true;
	$response['message']="Sorry! some details are missing";
}


echo json_encode($response);
}
catch(PDOException $err){
     echo $err -> getMessage();
}
$pdoread = null;
$pdo4 = null;
?>