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
$ipno=$data->ipno;
$medicine_name=$data->medicine_name;
try {
	if(!empty($accesskey)&& !empty($ipno)&& !empty($medicine_name)) {
$check =$pdoread->prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active' ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0){

 $validate = $pdoread->prepare("SELECT @a:=@a+1 serial_number,`sno` AS id,`medicine_name`,`medicine_code`,DATE_FORMAT(`dosage_date`,'%d-%b-%Y') AS edate,TIME_FORMAT(`dosage_time`,'%h:%i %p') AS etime,`remarks`,drug_time_remarks FROM (SELECT @a:= 0) AS a,`drug_medication` WHERE `ip_no` = :ipno AND `medicine_name` = :medicine_name AND `status` = 'Active'");  
 $validate->bindParam(':ipno', $ipno, PDO::PARAM_STR);
 $validate->bindParam(':medicine_name', $medicine_name, PDO::PARAM_STR);
 $validate->execute();
 if($validate->rowCount() > 0){
 while($validres=$validate->fetch(PDO::FETCH_ASSOC)){
   http_response_code(200);
$response['error'] = false;
$response['message'] = "Data found";
$response['ipmedicationlist'][] = $validres;
}
 }else{
   http_response_code(503);
    $response['error'] = true;
    $response['message'] = "No Data found";
 }
}else{
   http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}

} else {
   http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! Some details are missing';
}
} catch(PDOException $e) {
   http_response_code(503);
   $response['error'] = true;
   $response['message']= "Connection failed";
}
echo json_encode($response);
$pdoread = null;
?>