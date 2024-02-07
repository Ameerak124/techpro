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
try{

 /* if(!empty($accesskey)){ */
/* $validate = $pdo -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();

if($validate -> rowCount() > 0){
	$row = $validate->fetch(); */
	  
 
   $my_array = array("Doctor Assist","Employee Assist","Procurement","I Assist");
   $my_array1 = array("https://65.1.244.68/hims-demo/mobile-api/mobile/","https://65.1.244.68/hims-demo/mobile-api/mobile/employee/","http://13.235.101.8/po/api/","");
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    for($x = 0; $x < sizeof($my_array); $x++){
		$response['subdashboardlist'][$x]['name']=$my_array[$x];
		$response['subdashboardlist'][$x]['url']=$my_array1[$x];
     }	
    /* 	}else{
			http_response_code(400);
		    $response['error'] = true;
			$response['message']="Access denied!";
		}   */
/* }else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
} */
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);

?>