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
$response = array();
try{

 if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	  
 
   $my_array = array("Lunch","Meeting","Others");
   $my_array1 = array("https://65.1.244.68/hims-demo/mobile-api/super-app/appicons/lunch.png","https://65.1.244.68/hims-demo/mobile-api/super-app/appicons/meeting.png","https://65.1.244.68/hims-demo/mobile-api/super-app/appicons/others.png");
   
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    for($x = 0; $x < sizeof($my_array); $x++){
		$response['staffmovementlist'][$x]['name']=$my_array[$x];	
		$response['staffmovementlist'][$x]['icon']=$my_array1[$x];	
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
$pdoread= null;
?>