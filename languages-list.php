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
	$accesscheck ="SELECT `userid`,`cost_center`,`role`  from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'";
	$sql = $pdoread->prepare($accesscheck);
	$sql->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$sql->execute();
     if($sql->rowCount() > 0){
	$row = $sql->fetch(PDO::FETCH_ASSOC);
		$my_array = array("English"/* ,"Telugu","Hindi" */);
		$my_array1 = array("Eng");
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
	for($x = 0; $x < sizeof($my_array); $x++){
		$response['languagelist'][$x]['passvalue']=$my_array[$x];
		$response['languagelist'][$x]['displayvalue']=$my_array1[$x];
     }		
	}else{
		http_response_code(400);
		$response['error'] = true;
		$response['message']="Access denied!";
	}
}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message']="Sorry some details missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response); 
unset($pdoread);

?>