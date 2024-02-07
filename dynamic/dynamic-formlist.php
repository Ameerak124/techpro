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
$stmt2=$pdoread->prepare("SELECT `sno`, `form_id`, `formname`, `formdescription`, `department`, `usage_type`, `frequency`, `freqcount`, `url_name`, `createdon`, `createdby`, `estatus`, `costcenter` FROM `main_dynamic_form` WHERE `estatus`='Active' order by sno desc");
$stmt2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$stmt2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
$stmt2-> execute();
if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
           $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
	      $response['dynamicformlist']= $data;
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
