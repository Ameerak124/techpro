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
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
if(!empty($data)){  
}else{
$dataa = json_encode($_POST);
$data = json_decode($dataa);
}
$response = array();
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
     $getbed_query = "SELECT `ward_name` FROM `mwc_bed_master` GROUP BY `ward_name`";   
     $getbed_sbmt = $pdoread -> prepare($getbed_query);
     //$getbed_sbmt -> bindParam(":wardname", $wardname, PDO::PARAM_STR);    
     $getbed_sbmt -> execute();
     if($getbed_sbmt -> rowCount() > 0){
        http_response_code(200);
         $response['error']= false;
	     $response['message']="Data Found!";
		   $beddata = $getbed_sbmt -> fetchAll(PDO::FETCH_ASSOC);
		//$response['data'][0]['ward_name'] = "-Select ward-";
         $response['data']= $beddata;
     }
     else
     {    
 http_response_code(503);
          $response['error']= true;
	     $response['message']="Something Went Wrong!";
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