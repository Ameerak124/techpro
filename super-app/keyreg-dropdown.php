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
	  
	$stmt1 = $pdo_hrms->prepare("SELECT `department` as departmentname FROM `employee_details` where department not in ('0','') group by department");
	$stmt1->execute();
	if($stmt1->rowCount() > 0){
	$result = $stmt1->fetchAll(PDO::FETCH_ASSOC);

   
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    $response['keydroplist']= $result;
   
	}else{
		http_response_code(503);
    $response['error'] = false; 
    $response['message']= "No Data Found";
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
    die("ERROR: Could not connect.".$e->getMessage());
}
echo json_encode($response);
$pdoread= null;
$pdo_hrms= null;
?>