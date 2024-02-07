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
$check = $pdoread-> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Generate Registration List
$reglist =$pdoread-> prepare("SELECT COUNT(E.total) AS total,SUM(E.occupied) AS occupied,SUM(E.vaccant) AS vaccant,SUM(E.hold) AS hold FROM (SELECT `mwc_bed_master`.`ward_name` AS total,(CASE WHEN `registration`.`ward_code` != 'Discharged' THEN 1 ELSE 0 END) AS occupied,(CASE WHEN IFNULL(`registration`.`ward_code`,'VACCANT') = 'VACCANT' THEN 1 ELSE 0 END) AS vaccant,(CASE WHEN `registration`.`ward_code` = 'Hold' THEN 1 ELSE 0 END) AS hold FROM `mwc_bed_master` LEFT JOIN `registration` ON `mwc_bed_master`.`service_code` = `registration`.`ward_code`) AS E");
$reglist -> execute();
if($reglist -> rowCount() > 0){
	$orderres = $reglist->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
	$response['total']= $orderres['total'];
	$response['totallcolor']= "#d1d1d1";
	$response['totaldcolor']= "#3a3a3a";
	$response['occupied']= $orderres['occupied'];
	$response['occupiedlcolor']= "#ffd4d4";
	$response['occupieddcolor']= "#c32d2d";
	$response['vaccant']= $orderres['vaccant'];
	$response['vaccantlcolor']= "#b8e7ba";
	$response['vaccantdcolor']= "#19951e";
	$response['hold']= $orderres['hold'];
	$response['holdlcolor']= "#fff0b3";
	$response['holddcolor']= "#917d2f";
$ward = $pdoread -> prepare("SELECT DISTINCT `ward_name` FROM `mwc_bed_master` ORDER BY `ward_name` ASC");
$ward -> execute();
$i = 0;
	while($wardres = $ward->fetch(PDO::FETCH_ASSOC)){
		$response['wardname'][$i]['value'] = $wardres['ward_name'];
	$bed = $pdoread -> prepare("SELECT `mwc_bed_master`.`bed_no` AS bedno,(CASE WHEN `registration`.`admissionstatus` != 'Discharged' THEN '#ffd4d4' ELSE '#b8e7ba' END) AS lightcolorcode,(CASE WHEN `registration`.`admissionstatus` != 'Discharged' THEN '#c32d2d' ELSE '#19951e' END) AS darkcolorcode FROM `mwc_bed_master` LEFT JOIN `registration` ON `mwc_bed_master`.`service_code` = `registration`.`ward_code` WHERE `ward_name` LIKE :ward_name");
	$bed->bindParam(':ward_name', $wardres['ward_name'], PDO::PARAM_STR);
	$bed -> execute();
	while($bedres = $bed->fetch(PDO::FETCH_ASSOC)){
		$response['wardname'][$i]['bedlist'][] = $bedres;
	}
	$i++;	
	}	
}else{
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
} catch(PDOException $e) {
	http_response_code(400);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>