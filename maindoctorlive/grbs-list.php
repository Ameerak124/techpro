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
$umrno=trim($data->umrno);
$accesskey=trim($data->accesskey);
try {
	if(!empty($accesskey)&& !empty($umrno)){ 

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$grbs_fetch=$pdoread->prepare("SELECT `umrno`,IFNULL(CONCAT(`doctor_master`.`title`,' ',`doctor_master`.`doctor_name`),'')AS docname,IFNULL(`doctor_master`.`department`,'')AS department ,`grbs_rate`,`insulin_type`,`route`,`other_insulin_type`,`insulin_dose`,ifnull(Date_format(grbs_date_time,'%d-%b-%Y %H:%i:%s'),'') as grbs_date_time ,comments as remarks FROM `doctor_assessment_grbs` LEFT JOIN `doctor_master` ON `doctor_master`.`doctor_uid`=`doctor_assessment_grbs`.`doc_uid` 
WHERE `doctor_assessment_grbs`.`status`='Active' AND `doctor_assessment_grbs`.`cost_center`=:branch AND `doctor_assessment_grbs`.`umrno`=:umrno order by doctor_assessment_grbs.grbs_date_time desc");

$grbs_fetch->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$grbs_fetch->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$grbs_fetch->execute();
if($grbs_fetch->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Found";
	$get_details=$grbs_fetch->fetchAll(PDO::FETCH_ASSOC);
	$response['grbslist']=$get_details;
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="No Data Found";
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
	$response['message']= "Connection failed" .$e->getmessage();
}
echo json_encode($response);
$pdoread = null;
?>