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
$template_name=trim($data->template_name);
$resid=trim($data->resid);
try{
if(!empty($accesskey)&& !empty($template_name)&& !empty($resid)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
  //check if template exists already
  $check_template=$pdoread->prepare("SELECT `template_name` FROM `medication_templates` WHERE `template_name`=:template_name AND `category`='MEDICATION' AND `status`='Active' AND `created_by`=:userid");
  $check_template->bindParam(':template_name', $template_name, PDO::PARAM_STR);
  $check_template->bindValue(':userid', $result['userid'], PDO::PARAM_STR);
  $check_template -> execute();
if($check_template->rowCount() > 0){
	http_response_code(503);
    $response['error']=true;
    $response['message']='Item Alreay Added To Template';
    $response['template_name']= $template_name;
}else{
$insert_template=$pdo4->prepare("INSERT INTO `medication_templates`(`sno`, `template_name`, `category`, `medicine_code`, `medicine_name`, `dose_frequecy`, `duration`, `frequency`, `route`, `days`, `quantity`, `instructions`, `status`, `created_by`, `created_on`, `modified_by`, `modified_on`, `cost_center`,`concurrently`) (SELECT NULL, :template_name,'MEDICATION', `medicine_code`, `medicine_name`,`dosage`,`duration`,`frequency`,`route`,`days`,`quantity`,`instructions`,'Active',:userid,CURRENT_TIMESTAMP,:userid, CURRENT_TIMESTAMP,:branch,`concurrently` FROM `doctor_mediciation` WHERE `billno` = :resid AND `vstatus` = 'Active' AND `source` = 'OPD')");
$insert_template->bindParam(':template_name', $template_name, PDO::PARAM_STR);
$insert_template->bindParam(':resid', $resid, PDO::PARAM_STR);
$insert_template->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$insert_template->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$insert_template->execute();
if($insert_template->rowCount() > 0){
	http_response_code(200);
	$response['error'] = false;
	$response['message']= "Template Saved";
	$response['template_name']= $template_name;
	
}else{
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Sorry! Please select medication details";
	$response['template_name']= $template_name;
}
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

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>