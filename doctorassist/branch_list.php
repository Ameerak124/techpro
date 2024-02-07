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
try
{
if(!empty($accesskey)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,`accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$location = explode(",",$result['storeaccess']);
$i = 0;
if($check -> rowCount() > 0){
	
	
	foreach($location as $branche){
		$branch = $pdoread -> prepare("SELECT `display_name` AS storename FROM `branch_master` WHERE `cost_center` = :branche LIMIT 1");
	$branch->bindParam(':branche', $branche, PDO::PARAM_STR);
	$branch -> execute();
	$branchres = $branch->fetch(PDO::FETCH_ASSOC);
	if($location != '')
	{
		
		
		http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
		$response['branchlist'][$i]['display'] = $branchres['storename'];
	$response['branchlist'][$i]['value'] = $branche;
	$i++;	
	}
	else{
		
			http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
	}
		
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
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   //unset($pdo4);
   unset($pdoread);
?>