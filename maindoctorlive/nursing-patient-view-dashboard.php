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
$accesskey=trim($data->accesskey);
$ipno=strtoupper($data->ipno);
try {
if(!empty($accesskey)&&!empty($ipno)){
    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount()>0){
//Doctor progress note
$fetchlist=$pdoread->prepare("SELECT DATE_FORMAT(`createdon`,'%d-%b-%Y %h:%i %p') AS edate,CONCAT('C/S/B Dr. ',`doctor_name`,'\r\n',`notes`) AS remarks FROM `doctor_progress_notes` WHERE `admissionno` = :ipno AND`estatus` = 'Active'");
$fetchlist->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$fetchlist->execute();
if($fetchlist->rowCount()> 0){
	 http_response_code(200);
	$response['doctorprognote']['error'] = false;
	$response['doctorprognote']['message'] = "Records found";
	while($fetchres=$fetchlist->fetch(PDO::FETCH_ASSOC)){
		$response['doctorprognote']['progressnotelist'][] = $fetchres;
	}
}else{
	http_response_code(200);
	$response['doctorprognote']['error'] = true;
	$response['doctorprognote']['message'] = "No records found";
}
//Medications
$medication=$pdoread->prepare("SELECT date_format(`issuedon`,'%d-%b-%Y') AS edate, `patient_stockissue_items`.`itemname` AS remarks,'Oral' AS routes, SUM(`patient_stockissue_items`.`quantity`) as qty, 'HIMS' AS sources FROM `patient_stock_issue` INNER JOIN `patient_stockissue_items` ON `patient_stock_issue`.`issueno` = `patient_stockissue_items`.`issue_no` WHERE `patient_stock_issue`.`ipnumber` = :ipno GROUP BY `patient_stockissue_items`.`itemcode`");
$medication->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$medication->execute();
if($medication->rowCount()> 0){
	http_response_code(200);
	$response['medications']['error'] = false;
	$response['medications']['message'] = "Records found";
	while($medicationres=$medication->fetch(PDO::FETCH_ASSOC)){
		$response['medications']['medicationslist'][] = $medicationres;
	}
}else{
	http_response_code(200);
	$response['medications']['error'] = true;
	$response['medications']['message'] = "No records found";
}
//Transaction 
$investigation=$pdoread->prepare("SELECT `services`,'HIMS' AS sources,DATE_FORMAT(`createdon`,'%d-%b-%Y %h:%i %p') AS billedon FROM `billing_history` WHERE `ipno` = :ipno AND `status` = 'Visible'  ORDER BY `createdon` DESC");
$investigation->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$investigation->execute();
if($investigation->rowCount()> 0){
	http_response_code(200);
	$response['investigation']['error'] = false;
	$response['investigation']['message'] = "Records found";
	while($investigationres=$investigation->fetch(PDO::FETCH_ASSOC)){
		$response['investigation']['investigationlist'][] = $investigationres;
	}
}else{
	http_response_code(200);
	$response['investigation']['error'] = true;
	$response['investigation']['message'] = "No records found";
}

$vital=$pdoread->prepare("SELECT (CASE WHEN `bp_systolic_sit` != 0 THEN CONCAT(`bp_systolic_sit`,' / ',`bp_diastolic_sit`) ELSE '---' END)  AS bprestpostion,(CASE WHEN `bp_systolic_sleep` != 0 THEN CONCAT(`bp_systolic_sleep`,' / ',`bp_diastolic_sleep`) ELSE '---' END) AS bpsleeppostion,(CASE WHEN `bp_systolic_stand` != 0 THEN CONCAT(`bp_systolic_stand`,' / ',`bp_diastolic_stand`) ELSE '---' END) AS bpstandpostion,(CASE WHEN `pulse_rate` != 0 THEN `pulse_rate` ELSE '---' END) AS pulserate, (CASE WHEN `respiratory_rate`!= 0 THEN CONCAT(`heart_rate`,'/',`respiratory_rate`) ELSE '---' END) AS heartrate,`temp_rate` AS temperature,DATE_FORMAT(`created_on`,'%d-%b-%Y %H:%i') AS createdon FROM `vital_signs_table` WHERE `admission_num` = :ipno AND `estatus` = 'Active' ORDER BY `created_on` DESC");
$vital->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$vital->execute();
if($vital->rowCount()> 0){
	 http_response_code(200);
	$response['vital']['error'] = false;
	$response['vital']['message'] = "Records found";
	while($vitalres=$vital->fetch(PDO::FETCH_ASSOC)){
		$response['vital']['vitallist'][] = $vitalres;
	}
}else{
	 http_response_code(200);
	$response['vital']['error'] = true;
	$response['vital']['message'] = "No records found";
}
}else {
	 http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}    
}else {	
 http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}
}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>


































