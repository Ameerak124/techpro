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
$medicine_code = strtoupper($data->medicine_code);
$medicine_name = strtoupper($data->medicine_name);
$instructions = str_ireplace("'","",strtoupper($data->instructions));
$dose_volume = str_ireplace("'","",strtoupper($data->dose_volume));
$frequency = strtoupper($data->frequency);
$route = strtoupper($data->route);
$days = strtoupper($data->days);
$days =($data->days);
$quantity = (int) ($data->quantity);
$dosage=trim($data->dosage);
$concurrently=trim($data->concurrently);
$duration=trim($data->duration);
$template_name=trim($data->template_name);
$source=trim($data->source);
$dosagedatetime = trim($data->dosagedatetime);
$datetimes = date_format(date_create($dosagedatetime),"Y-m-d H:i:00");
try {
if(!empty($accesskey) && !empty($reqno)&& !empty($umr_no)){
if($medicine_code==''){
	$medicine_code='OTHERS';
}	
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
if(empty($template_name)){
	if($dosagedatetime != ''){
		$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `doctor_mediciation`(`sno`, `umrno`, `billno`,`dose_volume`, `medicine_code`, `medicine_name`, `frequency`, `route`, `days`, `quantity`, `instructions`, `concurrently`, `duration`, `dosage`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `phar_status`, `phar_by`, `phar_on`, `phar_qty`, `vstatus`,`source`) VALUES (NULL,:umr_no,:billno,:dose_volume,:medicine_code,:medicine_name,:frequency,:routes,:dayss,:quantity,:instructions,:concurrently,:duration,:dosage,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:userid,:datetimes,0,'Active',:source)");
		$saleprice->bindParam(':billno', $reqno, PDO::PARAM_STR);
		$saleprice->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
		$saleprice->bindParam(':medicine_code', $medicine_code, PDO::PARAM_STR);
		$saleprice->bindParam(':medicine_name', $medicine_name, PDO::PARAM_STR);
		$saleprice->bindParam(':frequency', $frequency, PDO::PARAM_STR);
		$saleprice->bindParam(':routes', $route, PDO::PARAM_STR);
		$saleprice->bindParam(':dayss', $days, PDO::PARAM_STR);
		$saleprice->bindParam(':quantity', $quantity, PDO::PARAM_STR);
		$saleprice->bindParam(':dose_volume', $dose_volume, PDO::PARAM_STR);
		$saleprice->bindParam(':instructions', $instructions, PDO::PARAM_STR);
		$saleprice->bindParam(':concurrently', $concurrently, PDO::PARAM_STR);
		$saleprice->bindParam(':duration', $duration, PDO::PARAM_STR);
		$saleprice->bindParam(':dosage', $dosage, PDO::PARAM_STR);
		$saleprice->bindParam(':source', $source, PDO::PARAM_STR);
		$saleprice->bindParam(':datetimes', $datetimes, PDO::PARAM_STR);
		$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
		$saleprice -> execute();
	}else{
		$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `doctor_mediciation`(`sno`, `umrno`, `billno`, `medicine_code`, `medicine_name`,`dose_volume`, `frequency`, `route`, `days`, `quantity`, `instructions`, `concurrently`, `duration`, `dosage`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `phar_status`, `phar_by`, `phar_on`, `phar_qty`, `vstatus`,`source`) VALUES (NULL,:umr_no,:billno,:medicine_code,:medicine_name,:dose_volume,:frequency,:routes,:dayss,:quantity,:instructions,:concurrently,:duration,:dosage,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:userid,CURRENT_TIMESTAMP,0,'Active',:source)");
		$saleprice->bindParam(':billno', $reqno, PDO::PARAM_STR);
		$saleprice->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
		$saleprice->bindParam(':medicine_code', $medicine_code, PDO::PARAM_STR);
		$saleprice->bindParam(':medicine_name', $medicine_name, PDO::PARAM_STR);
		$saleprice->bindParam(':dose_volume', $dose_volume, PDO::PARAM_STR);
		$saleprice->bindParam(':frequency', $frequency, PDO::PARAM_STR);
		$saleprice->bindParam(':routes', $route, PDO::PARAM_STR);
		$saleprice->bindParam(':dayss', $days, PDO::PARAM_STR);
		$saleprice->bindParam(':quantity', $quantity, PDO::PARAM_STR);
		$saleprice->bindParam(':instructions', $instructions, PDO::PARAM_STR);
		$saleprice->bindParam(':concurrently', $concurrently, PDO::PARAM_STR);
		$saleprice->bindParam(':duration', $duration, PDO::PARAM_STR);
		$saleprice->bindParam(':dosage', $dosage, PDO::PARAM_STR);
		$saleprice->bindParam(':source', $source, PDO::PARAM_STR);
		$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
		$saleprice -> execute();	
	}
	if($saleprice -> rowCount() > 0){
		http_response_code(200);
		$response['error']= false;
	$response['message']= "Medicine Added Successfully";
	}else{
		http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please check the requestion number";
	}
}else if(!empty($template_name)){
	//get template list
	$get_template=$pdoread->prepare("SELECT   `medicine_code`, `medicine_name`,`dose_volume`, `frequency`, `route`, `days`, `quantity`, `instructions`,`concurrently`,`duration`,`dose_frequecy` FROM `medication_templates` WHERE `status`='Active' AND `template_name`=:template_name AND `category` IN('MEDICATION') ");
	$get_template->bindParam(':template_name', $template_name, PDO::PARAM_STR);
	$get_template->execute();
	if($get_template->rowCount() > 0){
    while($tres=$get_template->fetch(PDO::FETCH_ASSOC)){
	$saleprice = $pdo4 -> prepare("INSERT INTO `doctor_mediciation`(`sno`, `umrno`, `billno`, `medicine_code`, `medicine_name`,`dose_volume`, `frequency`, `route`, `days`, `quantity`, `instructions`, `concurrently`, `duration`, `dosage`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `phar_status`, `phar_by`, `phar_on`, `phar_qty`, `vstatus`, `source`) VALUE (NULL,:umr_no,:billno,:medicine_code, :medicine_name,:dose_volume,:frequency,:routes,:dayss,:quantity,:instructions,:concurrently,:duration,:dose_frequecy,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP ,'Active',:userid,CURRENT_TIMESTAMP,0,'Active','OPD')");
$saleprice->bindParam(':billno', $reqno, PDO::PARAM_STR);
$saleprice->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
$saleprice->bindParam(':dose_volume', $tres['dose_volume'], PDO::PARAM_STR);
$saleprice->bindParam(':medicine_code', $tres['medicine_code'], PDO::PARAM_STR);
$saleprice->bindParam(':medicine_name', $tres['medicine_name'], PDO::PARAM_STR);
$saleprice->bindParam(':frequency', $tres['frequency'], PDO::PARAM_STR);
$saleprice->bindParam(':routes', $tres['route'], PDO::PARAM_STR);
$saleprice->bindParam(':dayss', $tres['days'], PDO::PARAM_STR);
$saleprice->bindParam(':quantity', $tres['quantity'], PDO::PARAM_STR);
$saleprice->bindParam(':instructions', $tres['instructions'], PDO::PARAM_STR);
$saleprice->bindParam(':concurrently', $tres['concurrently'], PDO::PARAM_STR);
$saleprice->bindParam(':duration', $tres['duration'], PDO::PARAM_STR);
$saleprice->bindParam(':dose_frequecy', $tres['dose_frequecy'], PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$saleprice -> execute();
	}
if($saleprice -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
$response['message']= "Medicine Added Successfully";
}else{
	http_response_code(503);
$response['error']= true;
$response['message']="Sorry! Please check the requestion number";
}
	}else{
		http_response_code(503);
		$response['error']=true;
		$response['message']='No Active Data Found On Template';
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Please Contact IT Team";
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