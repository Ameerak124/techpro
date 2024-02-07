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
try{	
$stmt2=$pdoread->prepare("SELECT `sno`, `customer`, `mobile`, `emailid`, `form_id` AS formid, `form_name`, `form_description`, `department`, `frequency`, `usage_type`, Date_format(`createdon`,'%d-%b-%Y %H:%i %p') As date, `createdby`, `status`, `transid`,if(signature_image='','',Concat(:baseurl,signature_image)) AS signature_image FROM `dynamic_form_submit`");
$stmt2->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
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
	
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
echo json_encode($response);
unset($pdoread);
?>
