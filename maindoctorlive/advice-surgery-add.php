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
$reqno = strtoupper($data->reqno);
$umr_no = trim($data->umr_no);
$doctorcode = strtoupper($data->doctorcode);
$doctorname = strtoupper($data->doctorname);
$remarks = str_ireplace("'","",strtoupper($data->remarks));
$transfer_status = strtoupper($data->transfer_status);
$procedure_surgery = strtoupper($data->procedure_surgery);
$source=trim($data->source);
$adm_date = trim($data->adm_date);
$ward = trim($data->ward);
$adm_date = date_format(date_create($adm_date),"Y-m-d");
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {

if(!empty($accesskey) && !empty($reqno)&& !empty($umr_no)){

//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
		$checkq = $pdoread->prepare("SELECT `sno` FROM `surgery_advise` WHERE `estatus` = 'A' AND `req_no` = :reqno");
		$checkq->bindParam(':reqno', $reqno, PDO::PARAM_STR);
		$checkq -> execute();
		if($checkq -> rowCount() > 0){
			$checkres = $checkq->fetch(PDO::FETCH_ASSOC);
			$saleprice = $pdo4 -> prepare("UPDATE `surgery_advise` SET `source`=:source,`umrno`=:umr_no,`req_no`=:reqno,`transfer_status`=:transfer_status,`doctor_code`=:doctorcode,`doctor_name`=:doctorname,`adm_date`=:adm_date,`procedure_surgery`=:procedure_surgery,`remarks`=:remarks,`modifiedby`=:userid,
			`ward`=:ward,`modified_on`=CURRENT_TIMESTAMP WHERE `sno` = :sno");
			$saleprice->bindParam(':reqno', $reqno, PDO::PARAM_STR);
			$saleprice->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
			$saleprice->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
			$saleprice->bindParam(':doctorname', $doctorname, PDO::PARAM_STR);
			$saleprice->bindParam(':remarks', $remarks, PDO::PARAM_STR);
			$saleprice->bindParam(':transfer_status', $transfer_status, PDO::PARAM_STR);
			$saleprice->bindParam(':procedure_surgery', $procedure_surgery, PDO::PARAM_STR);
			$saleprice->bindParam(':source', $source, PDO::PARAM_STR);
			$saleprice->bindParam(':ward', $ward, PDO::PARAM_STR);
			$saleprice->bindParam(':adm_date', $adm_date, PDO::PARAM_STR);
			$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
			$saleprice->bindParam(':sno', $checkres['sno'] , PDO::PARAM_STR);
			$saleprice -> execute();
		}else{
			$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `surgery_advise`(`sno`, `source`, `umrno`, `req_no`, `transfer_status`, `doctor_code`, `doctor_name`, `adm_date`, `procedure_surgery`, `remarks`, `createdby`, `createdon`, `modifiedby`, `modified_on`, `estatus`,`ward`) VALUES (NULL,:source,:umr_no,:reqno,:transfer_status,:doctorcode,:doctorname,:adm_date,:procedure_surgery,:remarks,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'A',:ward)");
			$saleprice->bindParam(':reqno', $reqno, PDO::PARAM_STR);
			$saleprice->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
			$saleprice->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
			$saleprice->bindParam(':doctorname', $doctorname, PDO::PARAM_STR);
			$saleprice->bindParam(':remarks', $remarks, PDO::PARAM_STR);
			$saleprice->bindParam(':transfer_status', $transfer_status, PDO::PARAM_STR);
			$saleprice->bindParam(':procedure_surgery', $procedure_surgery, PDO::PARAM_STR);
			$saleprice->bindParam(':source', $source, PDO::PARAM_STR);
			$saleprice->bindParam(':adm_date', $adm_date, PDO::PARAM_STR);
			$saleprice->bindParam(':ward', $ward, PDO::PARAM_STR);
			$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
			$saleprice -> execute();
		}

		
	if($saleprice -> rowCount() > 0){
		http_response_code(200);
		$response['error']= false;
	    $response['message']= "Data saved";
	}else{
	http_response_code(503);	
	$response['error']= true;
	$response['message']="Sorry! please try again";
	}
//
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
	//
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