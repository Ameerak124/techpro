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
$accesskey = $data->accesskey;
$branchcode = $data->branchcode;
$fdate=$data->fdate;
$tdate=$data->tdate;
$response = array();
try
{
if(!empty($accesskey) && !empty($fdate) && !empty($tdate)&& !empty($branchcode)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	
	
    $org_list = $pdoread -> prepare("SELECT IFNULL((SELECT COUNT(*) FROM `mwc_bed_master` WHERE `mwc_bed_master`.`cost_center`=:branchcode  AND `mwc_bed_master`.`status`='Active'),0) AS totalbeds,IFNULL((SELECT COUNT(*) FROM `mwc_bed_master` INNER JOIN `registration` ON `mwc_bed_master`.`service_code` = `registration`.`ward_code` WHERE `mwc_bed_master`.`cost_center`=:branchcode  AND admissionstatus IN ('Admitted','Initiated Discharge')),0) AS occupiedbeds");
	$org_list->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
	/* $org_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$org_list->bindParam(':tdate', $tdate, PDO::PARAM_STR); */
	$org_list -> execute();
	if($org_list -> rowCount() > 0){
	$org_lists = $org_list->fetch(PDO::FETCH_ASSOC);
	
	
	$finalvccbeds=$org_lists['totalbeds']-$org_lists['occupiedbeds'];
	
	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	$response['totalname']="Total";
	$response['totlacount'] = strval($org_lists['occupiedbeds']+$finalvccbeds);
	
	
	$response['bedwiselist'][0]['name']="VACC.BEDS";
	$response['bedwiselist'][0]['count'] = strval($finalvccbeds);
	$response['bedwiselist'][1]['name']="OCC.BEDS";
	$response['bedwiselist'][1]['count'] = $org_lists['occupiedbeds'];
	}
	else{
		
			http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
	}
		
	
 }else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
} 

 }else{
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