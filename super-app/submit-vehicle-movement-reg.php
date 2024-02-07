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
$vehicle_no = $data->vehicle_no;
$driver_name = $data->driver_name;
$reading_out = $data->reading_out;
$aurthrized_by = $data->aurthrized_by;
$place = $data->place;
$purpose = $data->purpose;
$remarks = $data->remarks;
$response = array();
try
{
if(!empty($accesskey)){
$accesscheck =$pdoread->prepare("SELECT `userid`,`username`,`desgination`,`createdon`,`branch` FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`='Active'");
$accesscheck->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$accesscheck->execute();
if($accesscheck->rowCount()>0){
$row = $accesscheck->fetch(PDO::FETCH_ASSOC);



	$fetch = $pdo1 -> prepare("SELECT `vehicle_no`, `driver_name`,`status` FROM `vehicle_movement_reg` WHERE `vehicle_no`=:vehicle_no and status='Exit' and date(created_on)=CURRENT_DATE");
	$fetch->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount() == 0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);

   $result2 = $pdo1 -> prepare("SELECT Concat('VMR',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`id`),'VMR23090000'),Concat('VMR',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS id FROM `vehicle_movement_reg` where id like concat('%VMR',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
					$result2->execute();
                        $data=$result2->fetch(PDO::FETCH_ASSOC);
					$vehicle_id=$data['id'];



$result=$pdo1->prepare("INSERT INTO `vehicle_movement_reg`(`id`, `vehicle_no`, `driver_name`, `out_time`, `reading_out`, `aurthrized_by`, `place`, `purpose`, `remarks`, `created_by`, `created_on`,`status`) VALUES (:id,:vehicle_no,:driver_name,CURRENT_TIME,:reading_out,:aurthrized_by,:place,:purpose,:remarks,:userid,CURRENT_TIMESTAMP,'Exit')");
	$result->bindParam(':id', $vehicle_id, PDO::PARAM_STR);
	$result->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$result->bindParam(':driver_name', $driver_name, PDO::PARAM_STR);
	$result->bindParam(':reading_out', $reading_out, PDO::PARAM_STR);
	$result->bindParam(':aurthrized_by', $aurthrized_by, PDO::PARAM_STR);
	$result->bindParam(':place', $place, PDO::PARAM_STR);
	$result->bindParam(':purpose', $purpose, PDO::PARAM_STR);
	$result->bindParam(':remarks', $remarks, PDO::PARAM_STR);
    $result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();

  $result1 = $pdo1 -> prepare("INSERT INTO `vehicle_movement_reg_log`(`id`, `vehicle_no`, `driver_name`, `time`, `created_by`, `created_on`,`status`) VALUES (:id,:vehicle_no,:driver_name,CURRENT_TIME,:userid,CURRENT_TIMESTAMP,'Exit')");
	$result1->bindParam(':id', $vehicle_id, PDO::PARAM_STR);
	$result1->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$result1->bindParam(':driver_name', $driver_name, PDO::PARAM_STR);
	//$result1->bindParam(':time', $time, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1-> execute();

   if($result->rowCount()>0){
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data inserted';
}   
else{
    http_response_code(503);
    $response['error']=true;
    $response['message']='Data not inserted'; 
}
 }else{
	 http_response_code(503);
    $response['error'] = true; 
    $response['message']= "Data already inserted";
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
