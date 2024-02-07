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
$check =$pdoread->prepare("SELECT user_logins.`userid`,user_logins.`username`,user_logins.`desgination`,user_logins.`createdon`,user_logins.`cost_center`, branch_master.branch_name  FROM `user_logins` inner join `branch_master` on user_logins.cost_center=branch_master.cost_center WHERE user_logins.`mobile_accesskey`=:accesskey AND user_logins.`status`='Active'");

$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
if($check->rowCount()>0){
$result = $check->fetch(PDO::FETCH_ASSOC);

$query1=$pdo1->prepare("SELECT `Sno`, `id` AS staffmovementid,`emp_id`, `emp_name`, `desgination`, `branch`, `reason`, `reason_remarks`, `created_by`, DATE_FORMAT(`created_on`,'%d-%b-%Y %H:%i:%s') as created_on, `modified_by`, IFNULL(DATE_FORMAT(`modified_on`,'%d-%b-%Y %H:%i:%s'),'') as `modified_on`, `in_time`, `out_time`,status FROM `staff_movement` where date(created_on) BETWEEN :fdate and :tdate and branch=:branch order by Sno desc");
$query1->bindParam(':branch', $result['branch_name'], PDO::PARAM_STR);
$query1->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$query1->bindParam(':tdate', $tdate, PDO::PARAM_STR);
$query1->execute();
if($query1->rowCount()>0){
    $result1=$query1->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Found';
    $response['stafflist']=$result1;
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
