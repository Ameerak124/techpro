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
$accesskey = trim($data->accesskey);
try {

if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $getbed_query = "SELECT `ward_name` FROM `mwc_bed_master` WHERE `cost_center` = :cost_center  and ward_name not like'%02%' GROUP BY `ward_name`";   
     $getbed_sbmt = $pdoread -> prepare($getbed_query);
     $getbed_sbmt -> bindParam(":cost_center", $result['cost_center'], PDO::PARAM_STR);    
     $getbed_sbmt -> execute();
     if($getbed_sbmt -> rowCount() > 0){
         http_response_code(200);
         $response['error']= false;
	     $response['message']="Data Found!";
		 $beddata = $getbed_sbmt->fetchAll(PDO::FETCH_ASSOC);
	     $response['wardlist']=$beddata;
		 /* $response['data'][0]['ward_name'] = "-Select ward-";
		 $sn = 1;
		   while($beddata = $getbed_sbmt->fetch(PDO::FETCH_ASSOC)){
			$response['data'][$sn]['ward_name'] = $beddata['ward_name'];
			$sn++;
		   } */
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
	$response['message']="Access denied!";
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