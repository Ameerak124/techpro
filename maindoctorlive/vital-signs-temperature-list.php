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
$accesskey = $data->accesskey;
$response = array();

try {

if(!empty($accesskey)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   $list = $pdoread->prepare("SELECT @a:=@a+1 serial_number, `age_details`, MAX(case when `category` = 'Temperature Oral' then concat(`start_point`,'-',`end_point`) else 0 end) as Oral, MAX(case when `category` = 'Temperature Rectal' then concat(`start_point`,'-',`end_point`) else 0 end) as Rectal, MAX(case when `category` = 'Temperature Axillary(Armpit)' then concat(`start_point`,'-',`end_point`) else 0 end) as Axillary, MAX(case when `category` = 'Temperature Ear' then concat(`start_point`,'-',`end_point`) else 0 end) as Ear FROM (SELECT @a:= 0) a, `normal_vital_signs_range` WHERE `category` LIKE 'Temperature%' GROUP BY `age_details` ORDER BY `sno` ");
       
        $list->execute();
        if($list-> rowCount() > 0){
            $response['error'] = false;
          $response['message']= "Data found";
          $response['Heading'] = "Normal Temperatures by Age and Method";
          while(  $results = $list->fetch(PDO::FETCH_ASSOC)){
            $response['templist'][] = $results;
          }
          }else{
              $response['error'] = true;
              $response['message']= "No data found";
          }
//Check User Access End
}else{
    $response['error'] = true; 
      $response['message']= "Access Denied";
  }
}else{
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();;
}
echo json_encode($response);
$pdoread = null;
?>
