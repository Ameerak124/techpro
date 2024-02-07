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
$ip = trim($data->ip);
$category = trim($data->category);
$date = date('Y-m-d', strtotime($data->date));
$time = trim($data->time);
$desp = trim($data->desp);
$amount = trim($data->amount);
$remarks = trim($data->remarks);

try {

if(!empty($accesskey) && !empty($ip) && !empty($category) && !empty($date) && !empty($time) && !empty($desp) && !empty($amount) && !empty($remarks)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
    $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
    $validate->bindParam(':ip', $ip, PDO::PARAM_STR);
    $validate -> execute();
    $validates = $validate->fetch(PDO::FETCH_ASSOC);
    if($validate -> rowCount() > 0){
        $insert = $pdo4->prepare("INSERT IGNORE INTO `nursing_intake_output`(`sno`, `ip`, `category`,`date`,`time`, `desp`, `amount`, `remarks`, `createdby`, `createdon`, `modifiedon`, `modifiedby`, `estatus`) VALUES(NULL, :ip, :category, :dates, :tm, :desp, :amount, :remarks, concat(:userid,'_',:username),  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :accesskey, 'Active')");
        $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
        $insert->bindParam(':category', $category, PDO::PARAM_STR);
        $insert->bindParam(':dates', $date, PDO::PARAM_STR);
        $insert->bindParam(':tm', $time,PDO::PARAM_STR);                                                              
        $insert->bindParam(':desp', $desp, PDO::PARAM_STR);
        $insert->bindParam(':amount', $amount, PDO::PARAM_STR);
        $insert->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $insert->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
        $insert->bindParam(':username', $result['username'], PDO::PARAM_STR);
        $insert->execute();
        if($insert-> rowCount() > 0){
			http_response_code(200);
            $response['error'] = false;
          $response['message']= "Data Inserted Successfully";
         
          }else{
			  http_response_code(503);
              $response['error'] = true;
              $response['message']= "Data Not Inserted";
          }

   
}else{
	http_response_code(503);
    $response['error'] = true;
      $response['message']= "Please Check IP / Patient Checked Out";
  }
//Check User Access End
}else{
	http_response_code(400);
    $response['error'] = true;
      $response['message']= "Access Denied";
  }
}else{
	http_response_code(400);
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
$pdo4 = null;
$pdoread = null;
?>
