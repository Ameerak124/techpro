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
$accesskey = trim($data->accesskey);
$response = array();
try{
if(!empty($accesskey)){	
$check = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
$stmt2=$pdoread->prepare("SELECT `sno`, `customer`, `mobile`, `emailid`, `form_id` AS formid, `form_name`, `form_description`, `department`, `frequency`, `usage_type`, Date_format(`createdon`,'%d-%b-%Y %H:%i %p') As date, `createdby`, `status`, `transid`,if(signature_image='','',Concat(:baseurl,signature_image)) AS signature_image FROM `dynamic_form_submit` where cost_center=:cost_center order by sno desc");
$stmt2->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$stmt2->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$stmt2-> execute();
if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
           $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
	      $response['dynamicformsubmitlist']= $data;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }	
}else{
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
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
echo json_encode($response);
unset($pdoread);
?>
