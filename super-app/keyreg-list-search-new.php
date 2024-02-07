<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db-new.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$searchterm = $data->searchterm;
$fdate = date('Y-m-d', strtotime($data->fdate));
$tdate = date('Y-m-d', strtotime($data->tdate));
$response = array();
try
{
if(!empty($accesskey)){
$check =$pdoread->prepare("SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
if($check->rowCount()>0){
$result = $check->fetch(PDO::FETCH_ASSOC);

$query1=$pdo1->prepare("SELECT `sno`, `department`, `key_no`, `no_of_key`,((no_of_key)-keys_return) as pendingkeys,if (`out_time`='00:00:00', '', out_time) AS out_time, if (`in_time`='00:00:00', '', in_time) as in_time, `taken_by_userid`, if(`taken_security_signature`='Please select an image file to upload.','',concat(:baseurl,`taken_security_signature`)) as taken_security_signature, if(`taken_digital_signature`='Please select an image file to upload.','',concat(:baseurl,`taken_digital_signature`)) as taken_digital_signature,if(`deposit_signature`='','',if(`deposit_signature`='Please select an image file to upload.','',concat(:baseurl,`deposit_signature`))) as deposit_signature, if(`deposit_security_signature`='','',if(`deposit_security_signature`='Please select an image file to upload.','',concat(:baseurl,`deposit_security_signature`))) as deposit_security_signature, `deposited_by`, `created_by`,  DATE_FORMAT(`created_on`,'%d-%b-%Y %H:%i:%s') as created_on, `modified_by`,  IFNULL(DATE_FORMAT(`modified_on`,'%d-%b-%Y %H:%i:%s'),'') as `modified_on`, `status`,if (`status`='Entered', '#4CAF50', '#FF9800') as status_colour FROM `key_reg` where taken_by_userid like :searchterm  and date(created_on) BETWEEN :fdate and :tdate order by sno desc");
$query1->bindValue(':searchterm',"%{$searchterm}%",PDO::PARAM_STR);
$query1->bindParam(':baseurl',$baseurl,PDO::PARAM_STR);
$query1->bindParam(':fdate',$fdate,PDO::PARAM_STR);
$query1->bindParam(':tdate',$tdate,PDO::PARAM_STR);
$query1->execute();
if($query1->rowCount()>0){
    $result1=$query1->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Found';
    $response['Keyreglist']=$result1;
}   
else{
    http_response_code(503);
    $response['error']=true;
    $response['message']='No data Found'; 
}
}else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
}
}else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! some details are missing';
}
}catch(PDOEXCEPTION $e){
    http_response_code(503);
    $response['error']=true;
    $response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response,true);
unset($pdoread);
unset($pdo1);
?>
