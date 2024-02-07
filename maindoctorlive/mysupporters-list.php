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

try {
if(!empty($accesskey)){

$check = $pdoread -> prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	
    $list = $pdoread->prepare("SELECT `sno`, `uniqueid`, `name`, `mobileno`, `location`, `type`, `designation`, date_format(`created_on`,'%d-%b-%y') as created_on, `created_by`, `status` FROM `my_supporters` WHERE created_by=:userid and `status`='Active' order by `sno` desc");
	$list->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
    $list->execute();
        if($list-> rowCount() > 0){
			
            http_response_code(200);
            $response['error'] = false;
            $response['message']= "Data found";
            while($results = $list->fetch(PDO::FETCH_ASSOC)){
            $response['mysupporters_list'][] = $results;
            }
			
            }else{
                http_response_code(503);
                $response['error'] = true;
                $response['message']= "No data found";
            }                         
          
		  
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
         
           }catch(PDOException $e){
               http_response_code(503);
               $response['error'] = true;
               $response['message']= "Connection failed".$e->getMessage();;
           }
           echo json_encode($response);
           $pdoread = null;
           ?>
          