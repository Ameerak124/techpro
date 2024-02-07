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
$response = array();
try
{
if(!empty($accesskey)){
$check =$pdoread->prepare("SELECT `userid`,`username`,`desgination`,`createdon`,`branch` FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`='Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
if($check->rowCount()>0){
$result = $check->fetch(PDO::FETCH_ASSOC);

$query1=$pdo1->prepare("SELECT `sno`, `id`,DATE_FORMAT(`date_time`,'%d-%b-%Y %H:%i:%s') as `date_time`, `from_address`, `dc_invoice_no`, `dc_invoice_date`, `description`, `qty`, `vehicle_no`, `brought_by`, if(signature='Please select an image file to upload.','',concat(:baseurl,'super-app/',`signature`))as signature, `remarks`, `status`, `created_by`,DATE_FORMAT(`created_on`,'%d-%b-%Y %H:%i:%s') as created_on, `modified_by`, IFNULL(DATE_FORMAT(`modified_on`,'%d-%b-%Y %H:%i:%s'),'') as `modified_on` FROM `inward` where date(created_on)=CURRENT_DATE and (vehicle_no like :searchterm) order by sno desc");
$query1->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
$query1->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$query1->execute();
if($query1->rowCount()>0){
    $result1=$query1->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Found';
    $response['inwardlist']=$result1;
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
