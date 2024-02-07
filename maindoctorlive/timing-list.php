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
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$selectdate = $data->selectdate;
$response = array();
try{
if(!empty($accesskey) && !empty($selectdate)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);

$check_getslot = $pdoread -> prepare("SELECT Concat_ws(' ',:selectdate,`from_time`) AS fdate,Concat_ws(' ',:selectdate,`to_time`) AS tdate, `slotgap` FROM `doctor_timings` WHERE `doctor_code`=:doctorcode AND `status`='Active'  AND location_cd=:cost_center AND day_name=DAYNAME(:selectdate) order by sno desc limit 1");
$check_getslot->bindParam(':doctorcode', $result['userid'], PDO::PARAM_STR);
$check_getslot->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$check_getslot->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
$check_getslot -> execute();
if($check_getslot -> rowCount() > 0){
$result_slot = $check_getslot->fetch(PDO::FETCH_ASSOC);	
 $response1 = array ();
 $start_time    = strtotime ($result_slot['fdate']);
 $end_time      = strtotime ($result_slot['tdate']);
if($result_slot['slotgap']!='0'){
$fifteen_mins  = $result_slot['slotgap'] * 60;

while ($start_time <= $end_time)
{
   $array_of_time= date ("h:i A", $start_time);
   $temp=[
   "time"=>$array_of_time,
   ];
   array_push($response1,$temp);
   $start_time += $fifteen_mins;
}
http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    
	$response['timinglist']=$response1;	
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Please update slotgap";
}
   
     		
	
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="No slots";
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

}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
     
     //"Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null; 

?>	
     
