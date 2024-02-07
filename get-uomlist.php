<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
//data credentials
include 'pdo-db.php';
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $query = "SELECT `sno`, `uom`, `uom_name` FROM `uom_master` WHERE `is_active` = '1'";   
     $sbmt = $pdoread -> prepare($query);   
     $sbmt -> execute();
     if($sbmt -> rowCount() > 0){
          $data = $sbmt -> fetchAll(PDO::FETCH_ASSOC);
		  http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['uomlist'] = $data;
     }
     else
     {    
          http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
}
else
{   
    http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>