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
$accesskey = trim($data->accesskey);
$billno=$data->billno;
$response = array();
try {
if(!empty($accesskey)&& !empty($billno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
//Access key verified
if($check -> rowCount() > 0){
$query=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`sno` AS id,dose_volume,`billno`,`medicine_name`,`frequency`,`route`,`days`,`quantity`,`instructions`,(SELECT `username` FROM `user_logins` WHERE `userid`=`doctor_mediciation`.`createdby`) AS cname,`createdby`,dosage,duration,concurrently ,phar_on AS savedon,medicine_code,`stop_medication`
 FROM (SELECT @a:=0) AS a, `doctor_mediciation` WHERE `billno`=:billno AND `vstatus`='Active' order by sno desc");
$query->bindParam(':billno', $billno, PDO::PARAM_STR);
$query->execute();
if($query->rowCount()>0){
    $sn=0;
while($queryresult=$query->fetch(PDO::FETCH_ASSOC)){
    http_response_code(200);
    $response['error']=false;
    $response['message']="Data found";
    $response['medicinename']="Medicine Name";
    $response['dose']="Dose";
    $response['frequency']="Frequency";
    $response['duration']="Duration";
    $response['instruction']="Instruction";
    $response['active']="Action";
	$response['route']="Route";
	$response['dose_date_time']="Date & Time";
	$response['title_when']="When";
    $response['doctormedicationlist'][]=$queryresult;   
     $sn++;
}
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="No Data found";
	$response['medicinename']="Medicine Name";
    $response['dose']="Dose";
    $response['when']="When";
    $response['frequency']="Frequency";
    $response['duration']="Duration";
    $response['instruction']="Instruction";
    $response['active']="Action";
	$response['route']="Route";
}
}else{
	http_response_code(400);
    $response['error']= true;
	$response['message']="Access denied!";
}

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
?>