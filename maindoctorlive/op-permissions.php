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
try {
if(!empty($accesskey) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);

	$select ="SELECT `sno`, `name`, `title` as maintitle, `packagename`, `controllername`, `classtype`, `created_by`, `created_on`, `modified_by`, `modified_on`, `type`, `status` FROM `doctor_permissions` WHERE `type`='Op' and  `status`='Active'";
	$select = $pdoread->prepare($select);
	$select->execute();
if($select -> rowCount() > 0){
              http_response_code(200);
			$response['error'] = false;
			$response['message'] = 'Data found';	
			while($invres = $select->fetch(PDO::FETCH_ASSOC)){
				$response['titlesdropdowmlist'][] = $invres;
			}
}else{
	     http_response_code(503);
		$response['error'] = true;
		$response['message'] = 'No data found';
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