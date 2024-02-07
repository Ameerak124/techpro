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
$umrno = trim($data->umrno);
$accesskey = trim($data->accesskey);
$patient_category = strtoupper($data->patient_category);
$govt_id = strtoupper($data->govt_id);
$govtidnumber = strtoupper($data->govtidnumber);
$patientname = strtoupper($data->patientname);
$patientage = date_format(date_create($data->patientage),"Y-m-d H:i:s");
$patientgender = strtoupper($data->patientgender);
$s_w_d_b_o = strtoupper($data->s_w_d_b_o);
$contactno = ($data->contactno);
$alternativeno = ($data->alternativeno);
$address = strtoupper(str_ireplace("'","",$data->address));
$city = strtoupper(str_ireplace("'","",$data->city));
$state = strtoupper($data->state);
$admittedward = strtoupper($data->admittedward);
$map_ward = strtoupper($data->backend_ward);
$ward_code = strtoupper($data->ward_code);
$roomno = strtoupper($data->roomno);
$consultantname = strtoupper($data->consultantname);
$consultantcode = strtoupper($data->consultantcode);
$department = strtoupper($data->department);
$procedure_surgery = strtoupper($data->procedure_surgery);
$admission_category = strtoupper($data->admission_category);
$referrenceno = strtoupper($data->referrenceno);
$country = strtoupper($data->country);
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey)&& !empty($umrno)&& !empty($patientname)&& !empty($patientage)&& !empty($patientgender)&& !empty($s_w_d_b_o)&& !empty($contactno)&& !empty($address)&& !empty($city)&& !empty($state) && !empty($admittedward) && !empty($country)&& !empty($admission_category)&& !empty($roomno)&& !empty($patient_category)&&!empty($consultantname)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//Generate Admission number Start
$generateadmno = $pdoread -> prepare("SELECT IFNULL(MAX(`admissionno`),CONCAT('MCIP',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000')) AS admissionno  FROM `registration` WHERE DATE_FORMAT(`admittedon`,'%y%b') = DATE_FORMAT(CURRENT_TIMESTAMP,'%y%b')");
$generateadmno -> execute();
if($generateadmno -> rowCount() > 0){
$resultadmno = $generateadmno->fetch(PDO::FETCH_ASSOC);
$admission =  $resultadmno['admissionno'];
 $admissionnum = ++$admission;
}else{
$admissionnum = $admissionno;
}
//Generate Admission number End
//Generate Bill number Start
$generatebillno = $pdoread -> prepare("SELECT IFNULL(MAX(`billno`),CONCAT('MOFB',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000')) AS billno  FROM `registration` WHERE DATE_FORMAT(`admittedon`,'%y%b') = DATE_FORMAT(CURRENT_TIMESTAMP,'%y%b')");
$generatebillno -> execute();
if($generatebillno -> rowCount() > 0){
$resbillno = $generatebillno->fetch(PDO::FETCH_ASSOC);
$billno =  $resbillno['billno'];
 $billnum = ++$billno;
}else{
$billnum = $billno;
}
//Generate Bill number End
// Create Registration
$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `registration`(`sno`, `umrno`, `billno`, `admissionno`, `admissionstatus`, `admittedon`, `dischargedon`,`patient_category`, `govt_id`, `govtidnumber`,`patientname`, `patientage`, `patientgender`, `s_w_d_b_o`, `contactno`, `alternativeno`, `address`,`country`, `city`, `state`,`map_ward`,`ward_code`,`admittedward`, `roomno`, `consultantname`, `consultantcode`, `department`, `procedure_surgery`, `referral_code`, `referral_name`, `ref_pan`, `ref_acc`, `ref_ifsc`, `paymentmode`, `ref_amount`, `ref_paidon`, `ref_transactionno`, `total_bill`, `hospital_amount`, `pharmacy_amount`,`createdby`, `createdon`, `modifiedby`, `modifiedon`, `admission_category`, `referrenceno`, `status`) VALUES (NULL,:umrno,:billnum,:admissionnum,'Admitted',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,:patient_category,:govt_id,:govtidnumber,:patientname,:patientage,:patientgender,:s_w_d_b_o,:contactno,:alternativeno,:address,:country,:city,:state,:map_ward,:ward_code,:admittedward,:roomno,:consultantname,:consultantcode,:department,:procedure_surgery,'No Update','No Update', 'No Update', 'No Update', 'No Update', 'No Update', '0', CURRENT_TIMESTAMP, 'No Update', '0', '0', '0',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:admission_category,'No Update','Visible')");
$saleprice->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$saleprice->bindParam(':admissionnum', $admissionnum, PDO::PARAM_STR);
$saleprice->bindParam(':billnum', $billnum, PDO::PARAM_STR);
$saleprice->bindParam(':patient_category', $patient_category, PDO::PARAM_STR);
$saleprice->bindParam(':govt_id', $govt_id, PDO::PARAM_STR);
$saleprice->bindParam(':govtidnumber', $govtidnumber, PDO::PARAM_STR);
$saleprice->bindParam(':patientname', $patientname, PDO::PARAM_STR);
$saleprice->bindParam(':patientage', $patientage, PDO::PARAM_STR);
$saleprice->bindParam(':patientgender', $patientgender, PDO::PARAM_STR);
$saleprice->bindParam(':s_w_d_b_o', $s_w_d_b_o, PDO::PARAM_STR);
$saleprice->bindParam(':contactno', $contactno, PDO::PARAM_STR);
$saleprice->bindParam(':alternativeno', $alternativeno, PDO::PARAM_STR);
$saleprice->bindParam(':address', $address, PDO::PARAM_STR);
$saleprice->bindParam(':state', $state, PDO::PARAM_STR);
$saleprice->bindParam(':city', $city, PDO::PARAM_STR);
$saleprice->bindParam(':map_ward', $map_ward, PDO::PARAM_STR);
$saleprice->bindParam(':ward_code', $ward_code, PDO::PARAM_STR);
$saleprice->bindParam(':admittedward', $admittedward, PDO::PARAM_STR);
$saleprice->bindParam(':roomno', $roomno, PDO::PARAM_STR);
$saleprice->bindParam(':consultantname', $consultantname, PDO::PARAM_STR);
$saleprice->bindParam(':consultantcode', $consultantcode, PDO::PARAM_STR);
$saleprice->bindParam(':department', $department, PDO::PARAM_STR);
$saleprice->bindParam(':procedure_surgery', $procedure_surgery, PDO::PARAM_STR);
$saleprice->bindParam(':admission_category', $admission_category, PDO::PARAM_STR);
$saleprice->bindParam(':country', $country, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	$bed = $pdo4 -> prepare("INSERT IGNORE INTO `bed_transfer`(`sno`, `admissionno`, `service_code`, `service_name`, `remarks`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `reference`, `bed_status`, `status`) VALUES (NULL,:admissionnum,'SEM0001',:ad,'Admission',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'0','ON_BED','Visible')");
	$ad = $admittedward." / ".$roomno;
	$bed->bindParam(':admissionnum', $admissionnum, PDO::PARAM_STR);
	$bed->bindParam(':ad', $ad, PDO::PARAM_STR);
	$bed->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$bed -> execute();
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Thank you! IP number is ".$admissionnum;
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please contact IT team.";
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
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>