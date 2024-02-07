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
$accesskey = $data->accesskey;
$fdate = $data->fdate;
$tdate = $data->tdate;
try {
if(!empty($accesskey)&&!empty($fdate)&&!empty($tdate)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$stmt = $pdoread -> prepare("SELECT (SELECT vendormaster.`legalname` FROM inward_security INNER JOIN vendormaster ON inward_security.`vendor_id`=vendormaster.sno  )AS vendorname ,`inward_id`,`invoice_date`, `invoice_amount`, `dc_no`, `dc_date`,  DATE_FORMAT(`inward_date`,'%d-%b-%Y') AS inward_date, `ponumber`  FROM `inward_security`  WHERE DATE(`inward_date`) BETWEEN  :fdate AND :tdate");
$stmt->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$stmt->bindParam(':tdate', $tdate , PDO::PARAM_STR);
$stmt -> execute();
if($stmt -> rowCount()>0){
		$result1 = $stmt->fetch(PDO::FETCH_ASSOC);
		 http_response_code(200);
         $response['error']= false;
		 $response['message']="Data found";
		 $response['inwardlist'][]=$result1; 
     }else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No data found";
     }
}else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: ".$e;
	}
echo json_encode($response);
$pdoread = null;
?>