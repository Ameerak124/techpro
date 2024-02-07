<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$response = array();
try{
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);

$my_array = array("Mobile No");
$my_array1 = array("mobile");
   
http_response_code(200);
$response['error'] = false; 
$response['message']= "Data Found";
for($x = 0; $x < sizeof($my_array); $x++){
$response['mobiletypelist'][$x]['displaytype']=$my_array[$x];	
$response['mobiletypelist'][$x]['type']=$my_array1[$x];	
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
catch(PDOException $e) {
http_response_code(503);
$response['error'] = true;
$response['message']= "Connection failed ".$e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>