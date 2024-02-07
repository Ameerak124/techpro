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
$accesskey = $data->accesskey;
try {
if(!empty($accesskey)){
		$check = $pdoread -> prepare("SELECT `userid`,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check -> execute();
          if($check -> rowCount() > 0){
		$result = $check->fetch(PDO::FETCH_ASSOC);
          $my_array = array("All","Urgent and Important","Not Urgent but Important","Urgent but Not Important","Not Urgent and Not Important");
          http_response_code(200);
           $response['error'] = false; 
           $response['message']= "Data Found";
           for($x = 0; $x < sizeof($my_array); $x++){	
            $response['approvealldropdownlist'][$x]['type']=$my_array[$x];	
     	
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
       }
       echo json_encode($response);
       $pdoread = null;
       ?>