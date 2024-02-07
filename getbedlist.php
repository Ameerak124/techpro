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
$accesskey =$data->accesskey;
$wardname =$data->wardname; 
try {
if(!empty($accesskey)&& !empty($wardname)){ 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount()> 0){
      
     $getbed_query = "SELECT E.backend_ward AS mapward,E.bed_no,E.service_code,(CASE WHEN statuss = 'open' THEN 'green_ward' ELSE 'red_ward' END) AS classcode,(CASE WHEN statuss = 'open' THEN 'enabled' ELSE 'disabled' END) AS ostatus FROM (SELECT `backend_ward`,`bed_no`, `service_code`,IFNULL(`registration`.`ward_code`,'open') AS statuss FROM `mwc_bed_master` LEFT JOIN `registration` ON `mwc_bed_master`.`service_code` = `registration`.`ward_code` WHERE `ward_name` LIKE :wardname) AS E" ;  
     $getbed_sbmt = $pdoread -> prepare($getbed_query);
     $getbed_sbmt -> bindParam(":wardname",$wardname, PDO::PARAM_STR);    
     $getbed_sbmt -> execute();
     if($getbed_sbmt -> rowCount() > 0){
         $beddata = $getbed_sbmt -> fetchAll(PDO::FETCH_ASSOC);
		 http_response_code(200);
         $response['error']= false;
	     $response['message']="Data Found!";
         $response['bedlist'] = $beddata;
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
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>