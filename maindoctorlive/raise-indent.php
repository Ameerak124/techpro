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
include "laboratory-data-save.php";
include "radiology-data-save.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$admissionno = strtoupper($data->admissionno);
/* $billno = strtoupper($data->billno); */
try {
	/* $con = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully"; */
if(!empty($accesskey) && !empty($admissionno)){
//Check access 
$check = $pdoread  -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
$list = $pdoread->prepare("SELECT `requisition_no`,`patient_type`,`billinghead`,category FROM `billing_history` WHERE `ipno` = :admissionno AND `status` = 'Hold'");
$list->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$list -> execute();
if($list -> rowCount() > 0){
	$listres = $list->fetch(PDO::FETCH_ASSOC);

	if($listres['category']=='LABORATORY' || $listres['category']=='RADIOLOGY' || $listres['category']=='WELLNESS' || $listres['category']=='CARDIOLOGY'){
		$lab = iplaboratoryworklist($pdo4, $admissionno,$result['userid'],$listres['patient_type'],$result['cost_center']);
		$radiology = ipradiologyworklist($pdo4,$admissionno,$result['userid'],$listres['patient_type'],$result['cost_center']);
	if($lab == 'Data Saved' || $radiology == 'Data Saved'){
		$saleprice = $pdo4 -> prepare("UPDATE `billing_history` SET `status` = 'Transit',`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP,`ipaddress` = :ipaddress WHERE `ipno` = :admissionno AND `status` = 'Hold'");
		$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
		$saleprice->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
		$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
		$saleprice -> execute();
		if($saleprice -> rowCount() > 0){
			http_response_code(200);
				$response['error']= false;
			$response['message']= "Indent Raised";
			
		}else{
			http_response_code(503);
			$response['error']= true;
			$response['message']="Sorry! Please Add Items";
		}
	}else{
		http_response_code(503);
		$response['error']= true;
	$response['message']=$lab;
	}
	}else{
  
		$saleprice = $pdo4 -> prepare("UPDATE `billing_history` SET `status` = 'Visible',`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP,`ipaddress` = :ipaddress WHERE `ipno` = :admissionno AND `status` = 'Hold' ");
		$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
		$saleprice->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
		$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
		$saleprice -> execute();
		if($saleprice -> rowCount() > 0){
			http_response_code(200);
				$response['error']= false;
			$response['message']= "Indent Raised";
			
		}else{
			http_response_code(503);
			$response['error']= true;
			$response['message']="Sorry! Please Add Items1";
		}
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please Add Items2";
} 

//
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
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