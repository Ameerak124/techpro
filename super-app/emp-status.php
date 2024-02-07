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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
try{
if(!empty($accesskey)){

	$rolecheck ="SELECT `userid`,`username`,`status` FROM `super_logins`  WHERE `mobile_accesskey`=:accesskey and status = 'Active'";
	 $stmt2 = $pdoread->prepare($rolecheck);
	$stmt2->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt2->execute();
	if($stmt2->rowCount() == 1){
    $row = $stmt2->fetch();
 
 
 
	http_response_code(200);
    $response['error'] = false;
	$response['status']=$row['status'];
 
	
 	}else{
		http_response_code(400);
		$response['error'] = true;
       $response['message']="Access denied!";
	}
 	
}else{

    http_response_code(400);
		$response['error'] = true;
       $response['message']="Sorry! some details are missing";
}
}catch(PDOEXCEPTION $e){
	echo $e;
}
//displaying the data in json format 
echo json_encode($response);
unset($pdoread);
	?>