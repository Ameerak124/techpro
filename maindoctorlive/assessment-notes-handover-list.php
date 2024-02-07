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
$doc_uid=trim($data->doc_uid);
$umrno=trim($data->umrno);
$ipno=trim($data->ipno);
$accesskey=trim($data->accesskey);

try {
	if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)){ 

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$notes_handover=$pdoread->prepare("SELECT `findings`, `clinical_examination`, `plan_care`, `diet`, `procedure_details`,`medication`, `others`, `allergies_medication_type`, `allergies_medication`, `food_type`, `food`, `food_others`, `progress_notes`, `vitals`, `assessment_diet`, `labs`, `critical_values`, `pending_orders`, `assessment_clinical_examination`, `assessment_others`, `recommendation_plan_care`, `handover_constructions`, `recommendations_others`, `handover_by`, `handover_to` FROM `doctor_assessment_notes_handover` WHERE  `status`='Active' AND `umrno`=:umrno AND `ipno`=:ipno AND `doc_uid`=:doc_uid AND `cost_center`=:branch ORDER BY `modified_on` DESC");
$notes_handover->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$notes_handover->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$notes_handover->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$notes_handover->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$notes_handover->execute();
if($notes_handover->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Found";
	$get_details=$notes_handover->fetch(PDO::FETCH_ASSOC);
    $response['assementnoteslist']=$get_details;

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
	$response['message']= "Connection failed";
	
}
echo json_encode($response);
$pdoread = null;
?>