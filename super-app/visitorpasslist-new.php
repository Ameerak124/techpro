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
$fdate = date('Y-m-d', strtotime($data->fdate));
$tdate = date('Y-m-d', strtotime($data->tdate));
$response = array();
try
{
if(!empty($accesskey)){
$accesscheck =$pdoread->prepare("SELECT `userid`,`username`,`desgination`,`createdon`,`branch` FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`='Active'");
$accesscheck->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$accesscheck->execute();
if($accesscheck->rowCount()>0){
$result = $accesscheck->fetch(PDO::FETCH_ASSOC);

$select=$pdo1->prepare("SELECT `sno`, `name`, `address`, `phone_number`, `whoom_to_meet`, `purpose`, `in_time`, `out_time`,if(signature='Please select an image file to upload.','',concat(:baseurl,`signature`)) as signature, if(photo='Please select an image file to upload.','',concat(:baseurl,`photo`)) as photo, `created_by` ,DATE_FORMAT(`created_on`,'%d-%b-%Y %H:%i:%s') as created_on, `modified_by`, IFNULL(DATE_FORMAT(`modified_on`,'%d-%b-%Y %H:%i:%s'),'') as `modified_on`, `status`,if (`status`='Entered', '#4CAF50', '#FF9800') as status_colour FROM `visitor_pass` WHERE date(created_on) BETWEEN :fdate and :tdate order by sno desc");
$select->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$select->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$select->bindParam(':tdate', $tdate, PDO::PARAM_STR);
$select->execute();
if($select->rowCount()>0){
    $result1=$select->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Found';
    $response['visitorlist']=$result1;
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
