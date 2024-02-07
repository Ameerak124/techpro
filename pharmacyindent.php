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
$accesskey=$data->accesskey;
try{
if(!empty($accesskey)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check-> rowCount() > 0){
 $stmt = $pdoread->prepare ("SELECT `umrno`,  `admissionno`, DATE(`admittedon`) AS admtno,  `patientname`, `patientage`, `consultantname`, `department`, DATE(`dischargedon`) AS DDATE, 'GENERAL' AS Admittedward FROM `registration` WHERE `status` = 'Visible' AND `admissionstatus` != 'Discharged'");
 $stmt->execute();
 if($stmt->rowCount() > 0) {
 $result1 = $stmt-> fetchAll(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
	$response['patientlist'] = $result1;
	}
    else{
		http_response_code(503);
		$response['error'] = true;
        $response['message'] = "No data found";
        
    }
}else{
		http_response_code(400);
		$response['error'] = true;
        $response['message'] = "Access denied!";
        
    }

}
else{
	http_response_code(400);
	$response['error'] = true;
$response['message'] = "some details are miising";

}

}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " ;
	$e;
	}
echo json_encode($response);
$pdoread = null;
?>
