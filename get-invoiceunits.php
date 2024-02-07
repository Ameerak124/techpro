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
try {
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$active = 'Active';
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $query = "SELECT `sno`,`unit`, `viewbranch`, `entity`, `address`, `city` FROM `unit_master` WHERE  `status`  = :active";   
     $sbmt = $pdoread -> prepare($query);   
     $sbmt -> bindParam(":active", $active, PDO::PARAM_STR);
     $sbmt -> execute();
     if($sbmt -> rowCount() > 0){
          $data = $sbmt -> fetchAll(PDO::FETCH_ASSOC);
		  http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['list'] = $data;
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
     $response['accesskey'] = $accesskey;
}
} 
catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']="Connection failed: " . $e->getMessage();
     
}
echo json_encode($response);
$pdoread = null;
?>