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
$admissionno = trim($data->admissionno);
$servicecode = trim($data->servicecode);
$servicename = explode("|",($data->servicename));
$remarks = str_ireplace("'\'","/",($data->remarks));
//$ipaddress = $_SERVER['REMOTE_ADDR'];

try {
if(!empty($accesskey) && !empty($admissionno) && !empty($servicecode) && !empty($servicename) && !empty($remarks)){	
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
 //check patient discharged or not on ip
$check_ip=$pdoread->prepare("SELECT `admissionno` FROM `registration` WHERE `admissionstatus` NOT IN('Discharged') AND `status`='Visible' AND `admissionno`=:ipno");
$check_ip->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
$check_ip->execute();
if($check_ip->rowCount() > 0) {
$SNO = $pdoread-> prepare("SELECT `sno` FROM `bed_transfer` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `bed_status` = 'ON_BED'");
$SNO->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$SNO -> execute();
$ref = $SNO->fetch(PDO::FETCH_ASSOC);
$bed = $pdoread -> prepare("SELECT `backend_ward`, `ward_name`, `bed_no` FROM `mwc_bed_master` WHERE `service_code` = :servicecode");
$bed->bindParam(':servicecode', $servicename[1], PDO::PARAM_STR);
$bed -> execute();
$refbed = $bed->fetch(PDO::FETCH_ASSOC);
// Insert
$saleprice = $pdo4-> prepare("INSERT IGNORE INTO `bed_transfer`(`sno`, `admissionno`, `service_code`, `service_name`, `remarks`, `createdby`, `createdon`,`transferedby`,`transferedon`, `modifiedby`, `modifiedon`, `reference`, `bed_status`, `status`) VALUES (NULL,:admissionno,:servicecode,:servicename,:remarks,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:reference,'ON_BED','Visible')");
$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$saleprice->bindParam(':servicecode', $servicename[1], PDO::PARAM_STR);
$saleprice->bindParam(':servicename', $servicename[2], PDO::PARAM_STR);
$saleprice->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$saleprice->bindParam(':reference', $ref['sno'], PDO::PARAM_STR);
$saleprice -> execute();
$insertedid = $pdo->lastInsertId();
if($saleprice -> rowCount() > 0){
$update = $pdo4 -> prepare("UPDATE `bed_transfer` SET `transferedby` = :userid,`transferedon` = CURRENT_TIMESTAMP,`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP,`bed_status` = 'TRANSFERED',`reference` = :reference WHERE `sno` = :sno");
$update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update->bindParam(':sno', $ref['sno'], PDO::PARAM_STR);
$update->bindParam(':reference', $insertedid, PDO::PARAM_STR);
$update -> execute();
$admission = $pdo4 -> prepare("UPDATE `registration` SET `map_ward` = :backend_ward,`ward_code` =:ward_code,`admittedward`=:ward_name,`roomno`=:bed_no,`modifiedby` =:userid,`modifiedon`=CURRENT_TIMESTAMP WHERE `admissionno`=:admissionno");
$admission->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$admission->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$admission->bindParam(':backend_ward', $servicename[2], PDO::PARAM_STR);
$admission->bindParam(':ward_name',  $servicename[2], PDO::PARAM_STR);
$admission->bindParam(':ward_code', $servicename[1], PDO::PARAM_STR);
$admission->bindParam(':bed_no', $servicename[0], PDO::PARAM_STR);
$admission -> execute();	

http_response_code(200);
$response['error']= false;
$response['message']= "Bed transfered";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please try again";
}
			}else{
				http_response_code(503);
				$response["error"]= true;
				$response["message"]= "Patient Checked Out";
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