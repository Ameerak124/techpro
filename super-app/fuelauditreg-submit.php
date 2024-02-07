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
/* $id = $data->id; */
$vehicle_reading = $data->vehicle_reading;
$billno = $data->billno;
$vehicle_no = $data->vehicle_no;
$rate = $data->rate;
$volume = $data->volume;
$sale = $data->sale;
$driver = $data->driver;
$response = array();
try{

 if(!empty($accesskey) && !empty($billno)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);



     $result2 = $pdo1-> prepare("SELECT Concat('FAR',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`id`),'FAR23090000'),Concat('FAR',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS id FROM `fuel_audit_reg` where id like concat('%FAR',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
     $result2->execute();
     $data=$result2->fetch(PDO::FETCH_ASSOC);
     $fuel_id=$data['id'];


     $result = $pdo1 -> prepare("INSERT INTO `fuel_audit_reg`(`id`,`vehicle_reading`,`billno`, `vehicle_no`,`rate`, `volume`,`sale`,`driver`,`date_time`,`status`,`created_by`,`created_on`) VALUES 
	(:id,:vehicle_reading,:billno,:vehicle_no,:rate,:volume,:sale,:driver,CURRENT_TIMESTAMP,'Registered',:userid,CURRENT_TIMESTAMP)");
	$result->bindParam(':id', $fuel_id, PDO::PARAM_STR);
	$result->bindParam(':vehicle_reading', $vehicle_reading, PDO::PARAM_STR);
	$result->bindParam(':billno', $billno, PDO::PARAM_STR);
	$result->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$result->bindParam(':rate', $rate, PDO::PARAM_STR);
	$result->bindParam(':volume', $volume, PDO::PARAM_STR);
	$result->bindParam(':sale', $sale, PDO::PARAM_STR);
	$result->bindParam(':driver', $driver, PDO::PARAM_STR);
	$result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();

     $result1 = $pdo1 -> prepare("INSERT INTO `fuel_audit_reg_log`(`id`, `billno`, `vehicle_no`, `created_on`,`created_by`,`status`) VALUES (:id,:billno,:vehicle_no,:userid,CURRENT_TIMESTAMP,'Registered')");
	$result1->bindParam(':id', $fuel_id, PDO::PARAM_STR);
	$result1->bindParam(':billno', $billno, PDO::PARAM_STR);
	$result1->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1-> execute();

     if($result->rowCount()>0){
	   
          http_response_code(200);
           $response['error'] = false; 
           $response['message']= "Data inserted";
           }else{
              http_response_code(503);
              $response['error'] = true;
              $response['message']="Data not inserted";
            }
                }else{
                      http_response_code(400);
                     $response['error'] = true;
                      $response['message']="Access denied!";
                 }  
       }else{
       http_response_code(400);
       $response['error'] = true;
       $response['message'] ="Sorry! Some details are missing";
       }
       } 
       catch(PDOException $e)
       {
           die("ERROR: Could not connect. " . $e->getMessage());
       }
       echo json_encode($response);
       unset($pdoread);
       unset($pdo1);
       ?>