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
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$doctorid=trim($data->doctorid);
$pain_scale_type=trim($data->pain_scale_type);
$painscale_score=trim($data->painscale_score);
$doctor_name=trim($data->doctor_name);
$pain=trim($data->pain);
$pain_location=trim($data->pain_location);
$pain_character=trim($data->pain_character);
$pain_duration_type=trim($data->pain_duration_type);
$pain_duration=trim($data->pain_duration);
$factor_decreasing_pain=trim($data->factor_decreasing_pain);
$factor_increasing_pain=trim($data->factor_increasing_pain);
$action_plan=trim($data->action_plan);
$intervention=trim($data->intervention);
$intervention_therapy=trim($data->intervention_therapy);
try {
     if(!empty($accesskey)&& !empty($ipno)&& !empty($umrno) && !empty($pain_scale_type) && ($painscale_score) >= 0 && ($painscale_score) !=''){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//check if data exists already
   //check if patient discharged or not
   $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
   $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
   $validate -> execute();
   $validates = $validate->fetch(PDO::FETCH_ASSOC);
   if($validate -> rowCount() > 0){

$page_id = $pdoread -> prepare("SELECT IFNULL(MAX(`page_id`),CONCAT('SA',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS page_id  FROM `pain_scores_table` WHERE `page_id` LIKE '%SA%'");
    $page_id -> execute();
    if($page_id -> rowCount() > 0){
    $pgid = $page_id->fetch(PDO::FETCH_ASSOC);
    $pageid =  $pgid['page_id'];
     $pgids = ++$pageid;
    }else{
    $pgids = $pgidno;
    }
$psinsert=$pdo4->prepare("INSERT IGNORE INTO `pain_scores_table`(`sno`, `page_id`, `assessment_type`, `admission_num`,  `umr_no`, `doctor_uid`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`,`del_remarks`) VALUES (NULL,:painid, 'Score Assessment', :admission_num, :umr, :doctorid, :pain_type, :pain_rate, :doc_name, :pain, :pain_loc, :pain_char, :acute_chornic, :pain_duration, :dec_pain, :inc_pain, :action_plan, :intervention, :interventiond, '',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active','')");
$psinsert->bindParam(':admission_num', $ipno, PDO::PARAM_STR);
$psinsert->bindParam(':painid', $pgids, PDO::PARAM_STR);
$psinsert->bindParam(':umr', $umrno, PDO::PARAM_STR);
$psinsert->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
$psinsert->bindParam(':pain_type', $pain_scale_type, PDO::PARAM_STR);
$psinsert->bindParam(':pain_rate', $painscale_score, PDO::PARAM_STR);
$psinsert->bindParam(':doc_name', $doctor_name, PDO::PARAM_STR);
$psinsert->bindParam(':pain', $pain, PDO::PARAM_STR);
$psinsert->bindParam(':pain_loc', $pain_location, PDO::PARAM_STR);
$psinsert->bindParam(':pain_char', $pain_character, PDO::PARAM_STR);
$psinsert->bindParam(':acute_chornic', $pain_duration_type, PDO::PARAM_STR);
$psinsert->bindParam(':pain_duration', $pain_duration, PDO::PARAM_STR);
$psinsert->bindParam(':dec_pain', $factor_decreasing_pain, PDO::PARAM_STR);
$psinsert->bindParam(':inc_pain', $factor_increasing_pain, PDO::PARAM_STR);
$psinsert->bindParam(':action_plan', $action_plan, PDO::PARAM_STR);
$psinsert->bindParam(':intervention', $intervention, PDO::PARAM_STR);
$psinsert->bindParam(':interventiond', $intervention_therapy, PDO::PARAM_STR);
//$psinsert->bindParam(':heparin_therapy', $heparin_therapy, PDO::PARAM_STR);
$psinsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$psinsert->execute();
if($psinsert -> rowCount() > 0){
    http_response_code(200);
$response['error']= false;
$response['message']= "Data inserted";
}else{
    http_response_code(503);
$response['error']= true;
$response['message']= "Sorry! Data not inserted in PS";
}
}else{
    http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
  }
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access Denied";
}
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
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