<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("Access-Control-Request-Method: GET");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$mchncode = trim($data->mchncode);
$response = array();
$method = $_SERVER['REQUEST_METHOD'];
try {
if($method=="GET"){
if(!empty($mchncode)){
$check = $pdoread-> prepare("SELECT `mchn_code`, `mchn_name`, `comport`, `baudrate`, `databit`, `stopbit`, `parity`, `machstatus`, `remarks`, `createdby`, `createdon`, `updatedby`, `updatedon` FROM `machine_master` WHERE `mchn_code`=:mchncode");
$check->bindParam(':mchncode', $mchncode, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$result = $check->fetchALL(PDO::FETCH_ASSOC);
$response=$result;
}
else
{
	$response['error']= true;
	$response['message']="Machine Code Not Available";
}
}
else{
	
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
}
	}else{
		$response['error']= true;
	$response['message']="Only GET Method Allowed";	
	}
} 
catch(PDOException $e) {
	
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>