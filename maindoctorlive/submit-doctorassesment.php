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
$accesskey = trim($data->accesskey);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$doc_uid=trim($data->doc_uid);
$chiefcomplaint=trim($data->chiefcomplaint);
$ptillness=trim($data->ptillness);
$pthistory=trim($data->pthistory);
$treatment_history=trim($data-> treatment_history);
$allergies=trim($data-> allergies);
$personal_history=trim($data-> personal_history);
$family_history=trim($data-> family_history);
$obstetric_history=trim($data-> obstetric_history);
$smoking_status=trim($data-> smoking_status);
$alcohol_drugs=trim($data-> alcohol_drugs);
$ptexamination=trim($data-> ptexamination);
$vitals=trim($data-> vitals);
$systemic_examination=trim($data-> systemic_examination);
$previous_investigations=trim($data-> previous_investigations);
$ptdiagnosis=trim($data-> ptdiagnosis);
$assessment_plan=trim($data-> assessment_plan);
$medication_reconciliation=trim($data-> medication_reconciliation);
$review=trim($data-> review);
$pain_assessment=trim($data-> pain_assessment);
$trans_shift_disc=trim($data-> trans_shift_disc);
$pain_type=trim($data-> pain_type);
$pain_rate=trim($data-> pain_rate);
$doc_name=trim($data-> doc_name);
$pain=trim($data-> pain);
$pain_loc=trim($data-> pain_loc);
$pain_char=trim($data-> pain_char);
$acute_chornic=trim($data-> acute_chornic);
$pain_duration=trim($data-> pain_duration);
$dec_pain=trim($data-> dec_pain);
$inc_pain=trim($data-> inc_pain);
$action_plan=trim($data-> action_plan);
$intervention=trim($data-> intervention);
$interventiond=trim($data-> interventiond);
$heparin_therapy=trim($data-> heparin_therapy);
$facial_expression=trim($data-> facial_expression);
$upper_limb_movements=trim($data-> upper_limb_movements);
$compliance_mechanical_ventilation=trim($data-> compliance_mechanical_ventilation);
$birth_history=trim($data->birth_history);
$immunization_history=trim($data->immunization_history);
$development_history=trim($data->development_history);
$temp=$data->temp;
$response = array();
$ipaddress = $_SERVER['REMOTE_ADDR'];
try{
if(!empty($accesskey)&& !empty($ipno)&& !empty($umrno)&& !empty($doc_uid)) {
$check =$pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check->execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0) {

 //check if patient discharged or not
 $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
 $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
 $validate -> execute();
 $validates = $validate->fetch(PDO::FETCH_ASSOC);
 if($validate -> rowCount() > 0){



//Access verified//
$check_details=$pdoread->prepare("SELECT `umr_no` , `pain_id` FROM `doctor_assessment` WHERE `status`='Active' AND `cost_center`=:branch AND`ipno`=:ipno AND `umr_no`=:umr_no AND `doctor_uid`=:doctor_uid");
$check_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$check_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$check_details->bindParam(':umr_no', $umrno, PDO::PARAM_STR);
$check_details->bindParam(':doctor_uid', $doc_uid, PDO::PARAM_STR);
$check_details->execute();
$check_detail = $check_details->fetch(PDO::FETCH_ASSOC);
if($check_details->rowCount () > 0){
    //update if data exists
    $update_data=$pdo4->prepare("UPDATE `doctor_assessment` SET `chief_complaint`=:chiefcomplaint,`present_illness`=:ptillness,`past_history`=:pthistory,`treatment_history`=:treatment_history,`allergies`=:allergies,`personal_history`=:personal_history,`family_history`=:family_history,`obstetric_history`=:obstetric_history,`smoking_status`=:smoking_status,`alcohol_drugs`=:alcohol_drugs,`general_examination`=:ptexamination,`vitals`=:vitals,`systemic_examination`=:systemic_examination,`adviced_investigations`=:previous_investigations,`emergency_diagnosis`=:ptdiagnosis,`assessment_plan`=:assessment_plan,`medication_reconciliation`=:medication_reconciliation,`review`=:review,`pain_assessment`=:pain_assessment,`trans_shift_disc`=:trans_shift_disc,`facial_expression`=:facial_expression,`upper_limb_movements`=:upper_limb_movements,`compliance_mechanical_ventilation`=:compliance_mechanical_ventilation,
    `modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP ,birth_history=:birth_history,`immunization_history`=:immunization_history,`development_history`=:development_history WHERE `status`='Active' AND `cost_center`=:branch AND`ipno`=:ipno AND `umr_no`=:umrno AND `doctor_uid`=:doctor_uid  ");
    $update_data->bindParam(':ipno', $ipno , PDO::PARAM_STR);
    $update_data->bindParam(':development_history', $development_history , PDO::PARAM_STR);
    $update_data->bindParam(':immunization_history', $immunization_history , PDO::PARAM_STR);
    $update_data->bindParam(':umrno', $umrno , PDO::PARAM_STR);
    $update_data->bindParam(':doctor_uid', $doc_uid , PDO::PARAM_STR);
    $update_data->bindParam(':chiefcomplaint', $chiefcomplaint , PDO::PARAM_STR);
    $update_data->bindParam(':ptillness', $ptillness , PDO::PARAM_STR);
    $update_data->bindParam(':pthistory', $pthistory , PDO::PARAM_STR);
    $update_data->bindParam(':treatment_history', $treatment_history , PDO::PARAM_STR);
    $update_data->bindParam(':allergies', $allergies , PDO::PARAM_STR);
    $update_data->bindParam(':personal_history', $personal_history , PDO::PARAM_STR);
    $update_data->bindParam(':family_history', $family_history , PDO::PARAM_STR);
    $update_data->bindParam(':obstetric_history', $obstetric_history , PDO::PARAM_STR);
    $update_data->bindParam(':smoking_status', $smoking_status , PDO::PARAM_STR);
    $update_data->bindParam(':alcohol_drugs', $alcohol_drugs , PDO::PARAM_STR);
    $update_data->bindParam(':ptexamination', $ptexamination , PDO::PARAM_STR);
    $update_data->bindParam(':vitals', $vitals , PDO::PARAM_STR);
    $update_data->bindParam(':systemic_examination', $systemic_examination , PDO::PARAM_STR);
    $update_data->bindParam(':previous_investigations', $previous_investigations , PDO::PARAM_STR);
    $update_data->bindParam(':ptdiagnosis', $ptdiagnosis , PDO::PARAM_STR);
    $update_data->bindParam(':assessment_plan', $assessment_plan , PDO::PARAM_STR);
    $update_data->bindParam(':medication_reconciliation', $medication_reconciliation , PDO::PARAM_STR);
    $update_data->bindParam(':review', $review , PDO::PARAM_STR);
    $update_data->bindParam(':pain_assessment', $pain_assessment , PDO::PARAM_STR);
    $update_data->bindParam(':trans_shift_disc', $trans_shift_disc , PDO::PARAM_STR);
    $update_data->bindParam(':facial_expression', $facial_expression , PDO::PARAM_STR);
    $update_data->bindParam(':upper_limb_movements', $upper_limb_movements , PDO::PARAM_STR);
    $update_data->bindParam(':compliance_mechanical_ventilation', $compliance_mechanical_ventilation , PDO::PARAM_STR);
    $update_data->bindParam(':birth_history', $birth_history , PDO::PARAM_STR);
    $update_data->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
    $update_data->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
    $update_data->execute();
    if($update_data->rowCount()>0){
		http_response_code(200);
        $response['error']=false;
        $response['message']="Data Updated Successfully";
//pain score update

$update_pain_details=$pdo4->prepare("UPDATE `pain_scores_table` SET  `pain_type`=:pain_type,`pain_rate`=:pain_rate,`doc_name`=:doc_name,`pain`=:pain,`pain_loc`=:pain_loc,`pain_char`=:pain_char,`acute_chornic`=:acute_chornic,`pain_duration`=:pain_duration,`dec_pain`=:dec_pain,`inc_pain`=:inc_pain,`action_plan`=:action_plan,`intervention`=:intervention,`interventiond`=:interventiond,`heparin_therapy`=:heparin_therapy,`modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP WHERE `status`='Active' AND `admission_num`=:ipno AND `doctor_uid`=:doctor_uid AND `page_id`=:pain_id ");
$update_pain_details->bindParam(':pain_id', $check_detail['pain_id'] , PDO::PARAM_STR);
$update_pain_details->bindParam(':ipno', $ipno , PDO::PARAM_STR);
$update_pain_details->bindParam(':pain_type', $pain_type , PDO::PARAM_STR);
$update_pain_details->bindParam(':pain_rate', $pain_rate , PDO::PARAM_STR);
$update_pain_details->bindParam(':doc_name', $doc_name , PDO::PARAM_STR);
$update_pain_details->bindParam(':pain', $pain , PDO::PARAM_STR);
$update_pain_details->bindParam(':pain_loc', $pain_loc , PDO::PARAM_STR);
$update_pain_details->bindParam(':pain_char', $pain_char , PDO::PARAM_STR);
$update_pain_details->bindParam(':acute_chornic', $acute_chornic , PDO::PARAM_STR);
$update_pain_details->bindParam(':pain_duration', $pain_duration , PDO::PARAM_STR);
$update_pain_details->bindParam(':dec_pain', $dec_pain , PDO::PARAM_STR);
$update_pain_details->bindParam(':inc_pain', $inc_pain , PDO::PARAM_STR);
$update_pain_details->bindParam(':action_plan', $action_plan , PDO::PARAM_STR);
$update_pain_details->bindParam(':intervention', $intervention , PDO::PARAM_STR);
$update_pain_details->bindParam(':interventiond', $interventiond , PDO::PARAM_STR);
$update_pain_details->bindParam(':heparin_therapy', $heparin_therapy , PDO::PARAM_STR);
$update_pain_details->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update_pain_details->bindParam(':doctor_uid', $doc_uid , PDO::PARAM_STR);
$update_pain_details->bindParam(':ipno', $ipno , PDO::PARAM_STR);
$update_pain_details->execute();

}else{
	http_response_code(503);
        $response['error']=true;
        $response['message']="Please Try Again";
}
}else{
    	//generate uid
$generate_id=$pdoread->prepare("SELECT IFNULL(MAX(`page_id`),CONCAT('DA',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS id  FROM `pain_scores_table` WHERE `page_id` LIKE '%DA%' ");
$generate_id->execute();
$gid=$generate_id->fetch(PDO::FETCH_ASSOC);
$uid=$gid['id'];
$unique_id=++$uid;

//insert data into db if data not exists
$query=$pdo4->prepare("INSERT IGNORE  INTO `doctor_assessment`(`sno`, `ipno`, `umr_no`, `doctor_uid`, `chief_complaint`, `present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`, `smoking_status`, `alcohol_drugs`, `general_examination`, `vitals`, `systemic_examination`, `adviced_investigations`, `emergency_diagnosis`, `assessment_plan`, `medication_reconciliation`, `review`, `pain_assessment`, `trans_shift_disc`, `pain_id`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`,`cost_center`,`source`,`birth_history`,`development_history`,`immunization_history`)VALUES  (NULL,:ipno,:umrno,:doctor_uid,:chiefcomplaint,:ptillness,:pthistory, :treatment_history, :allergies, :personal_history, :family_history, :obstetric_history, :smoking_status, :alcohol_drugs, :ptexamination, :vitals, :systemic_examination, :previous_investigations, :ptdiagnosis, :assessment_plan, :medication_reconciliation, :review, :pain_assessment, :trans_shift_disc, :idnum,  :facial_expression, :upper_limb_movements, :compliance_mechanical_ventilation, :userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:branch,'IP',:birth_history,:development_history,:immunization_history)");
$query->bindParam(':ipno', $ipno , PDO::PARAM_STR);
$query->bindParam(':umrno', $umrno ,PDO::PARAM_STR);
$query->bindParam(':doctor_uid', $doc_uid , PDO::PARAM_STR);
$query->bindParam(':chiefcomplaint', $chiefcomplaint , PDO::PARAM_STR);
$query->bindParam(':ptillness', $ptillness , PDO::PARAM_STR);
$query->bindParam(':pthistory', $pthistory , PDO::PARAM_STR);
$query->bindParam(':treatment_history', $treatment_history , PDO::PARAM_STR);
$query->bindParam(':allergies', $allergies , PDO::PARAM_STR);
$query->bindParam(':personal_history', $personal_history , PDO::PARAM_STR);
$query->bindParam(':family_history', $family_history , PDO::PARAM_STR);
$query->bindParam(':obstetric_history', $obstetric_history , PDO::PARAM_STR);
$query->bindParam(':smoking_status', $smoking_status , PDO::PARAM_STR);
$query->bindParam(':alcohol_drugs', $alcohol_drugs , PDO::PARAM_STR);
$query->bindParam(':ptexamination', $ptexamination , PDO::PARAM_STR);
$query->bindParam(':vitals', $vitals , PDO::PARAM_STR);
$query->bindParam(':systemic_examination', $systemic_examination , PDO::PARAM_STR);
$query->bindParam(':previous_investigations', $previous_investigations , PDO::PARAM_STR);
$query->bindParam(':ptdiagnosis', $ptdiagnosis , PDO::PARAM_STR);
$query->bindParam(':assessment_plan', $assessment_plan , PDO::PARAM_STR);
$query->bindParam(':medication_reconciliation', $medication_reconciliation , PDO::PARAM_STR);
$query->bindParam(':review', $review , PDO::PARAM_STR);
$query->bindParam(':pain_assessment', $pain_assessment , PDO::PARAM_STR);
$query->bindParam(':trans_shift_disc', $trans_shift_disc , PDO::PARAM_STR);
$query->bindParam(':idnum', $unique_id, PDO::PARAM_STR);
$query->bindParam(':facial_expression', $facial_expression , PDO::PARAM_STR);
$query->bindParam(':upper_limb_movements', $upper_limb_movements , PDO::PARAM_STR);
$query->bindParam(':compliance_mechanical_ventilation', $compliance_mechanical_ventilation , PDO::PARAM_STR);
$query->bindParam(':birth_history', $birth_history , PDO::PARAM_STR);
$query->bindParam(':immunization_history', $immunization_history , PDO::PARAM_STR);
$query->bindParam(':development_history', $development_history , PDO::PARAM_STR);
$query->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$query->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount()>0){
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Inserted Successfully';

    //add to pain score
$add_to_pain_score=$pdo4->prepare("INSERT IGNORE INTO `pain_scores_table`(`sno`, `page_id`, `assessment_type`, `admission_num`, `doctor_uid`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`,`umr_no`) VALUES (NULL,:idnum,'DOCTOR ASSESSMENT',:ipno,:doctor_uid,:pain_type, :pain_rate, :doc_name, :pain, :pain_loc, :pain_char, :acute_chornic, :pain_duration, :dec_pain, :inc_pain, :action_plan, :intervention, :interventiond, :heparin_therapy,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:umr)");
$add_to_pain_score->bindParam(':idnum', $unique_id, PDO::PARAM_STR);
$add_to_pain_score->bindParam(':umr', $umrno, PDO::PARAM_STR);
$add_to_pain_score->bindParam(':doctor_uid', $doc_uid , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$add_to_pain_score->bindParam(':pain_type', $pain_type , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':pain_rate', $pain_rate , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':doc_name', $doc_name , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':pain', $pain , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':pain_loc', $pain_loc , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':pain_char', $pain_char , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':acute_chornic', $acute_chornic , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':pain_duration', $pain_duration , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':dec_pain', $dec_pain , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':inc_pain', $inc_pain , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':action_plan', $action_plan , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':intervention', $intervention , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':interventiond', $interventiond , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':heparin_therapy', $heparin_therapy , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$add_to_pain_score->execute();
}else{
	http_response_code(503);
     $response['error']=true;
    $response['message']='Sorry! Updation failed!';
}
}
}else{
	http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
}
}else{
	http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied! please try to re-login again';
}
}else{
	http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! some details are missing';
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>