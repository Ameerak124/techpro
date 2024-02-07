<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$reqno=$data->reqno;
$bloodpressure=$data->bloodpressure;
$ptweight=$data->ptweight;
$ptheight=$data->ptheight;
$rbs=$data->rbs;
$spo2=$data->spo2;
$pr=$data->pr;
// $chiefcomplaint=$data->chiefcomplaint;
// $ptillness=$data->ptillness;
// $pthistory=$data->pthistory;
// $ptexamination=$data->ptexamination;
// $ptdiagnosis=$data->ptdiagnosis;
$temp=$data->temp;
$doctor_advice=$data->doctor_advice;
$ipno=$data->ipno;
$umrno=$data->umrno;
$doc_id=$data->doc_id;
$idnum=trim($data->idnum);
$chiefcomplaint=($data->chiefcomplaint);
$ptillness=($data->ptillness);
$pthistory=($data->pthistory);
$treatment_history=($data->treatment_history);
$allergies=($data->allergies);
$personal_history=($data->personal_history);
$family_history=($data->family_history);
$obstetric_history=($data->obstetric_history);
$smoking_status=($data->smoking_status);
$alcohol_drugs=($data->alcohol_drugs);
$ptexamination=($data->ptexamination);
$vitals=($data->vitals);
$systemic_examination=($data->systemic_examination);
$previous_investigations=($data->previous_investigations);
$ptdiagnosis=($data->ptdiagnosis);
$assessment_plan=($data->assessment_plan);
$medication_reconciliation=($data->medication_reconciliation);
$review=($data->review);
$pain_assessment=($data->pain_assessment);
$trans_shift_disc=($data->trans_shift_disc);
$pain_type=($data->pain_type);
$pain_rate=($data->pain_rate);
$doc_name=($data->doc_name);
$pain=($data->pain);
$pain_loc=($data->pain_loc);
$pain_char=($data->pain_char);
$acute_chornic=($data->acute_chornic);
$pain_duration=($data->pain_duration);
$dec_pain=($data->dec_pain);
$inc_pain=($data->inc_pain);
$action_plan=($data->action_plan);
$intervention=($data->intervention);
$interventiond=($data->interventiond);
$heparin_therapy=($data->heparin_therapy);
$facial_expression=($data->facial_expression);
$upper_limb_movements=($data->upper_limb_movements);
$compliance_mechanical_ventilation=($data->compliance_mechanical_ventilation);
$total_score_bp=($data->total_score_bp);
$compliance_bp=($data->compliance_bp);
$upper_limb_movements_bp=($data->upper_limb_movements_bp);
$facial_expression_bp=($data->facial_expression_bp);
$temp=$data->temp;
$resp=trim($data->resp);

$current_medication=trim($data->current_medication);
$feeding_history=trim($data->feeding_history);
$immunization_history=trim($data->immunization_history);
$development_history=trim($data->development_history);
$perinatal_history=trim($data->perinatal_history);
$correct_gestational_age=trim($data->correct_gestational_age);
$week_days=trim($data->week_days);
$air_vo2=trim($data->air_vo2);
$hfnc=trim($data->hfnc);
$cpap=trim($data->cpap);
$local_examination=trim($data->local_examination);
$gynecology_history=trim($data->gynecology_history);
$birth_history=trim($data->birth_history);
// $gestational_atb=trim($data->gestational_atb);

$response = array();
//$ipaddress = $_SERVER['REMOTE_ADDR'];
try{

if(!empty($accesskey)&& !empty($ipno)&& !empty($umrno)&& !empty($doc_id)) {
$check =$pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check->execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0) {
//Access verified//
$query=$pdo4->prepare("UPDATE op_biling_history SET `blood_pressure`=:bloodpressure,`weight`=:ptweight,`height`=:ptheight,`rbs`=:rbs,`spo2`=:spo2,`pr`=:pr,`temperature`=:temp,`cc_createdby`=:userid , `cc_createdon`=CURRENT_TIMESTAMP,`pd_createdby`=:userid,`pd_createdon`=CURRENT_TIMESTAMP,`ph_createdby`=:userid,`ph_createdon`=CURRENT_TIMESTAMP,`pe_createdby`=:userid,`pe_createdon`=CURRENT_TIMESTAMP, `doctor_advice`=:doctor_advice,`pdg_createdby`=:userid,`pdg_createdon`=CURRENT_TIMESTAMP ,`ipaddress`=:ip,`modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP,`prescription`=:respiration
 WHERE `status`='Visible' AND `requisition_no`=:reqno ");
$query->bindParam(':bloodpressure', $bloodpressure, PDO::PARAM_STR);
$query->bindParam(':ptweight', $ptweight, PDO::PARAM_STR);
$query->bindParam(':ptheight', $ptheight, PDO::PARAM_STR);
$query->bindParam(':rbs', $rbs, PDO::PARAM_STR);
$query->bindParam(':spo2', $spo2, PDO::PARAM_STR);
$query->bindParam(':pr', $pr, PDO::PARAM_STR);
$query->bindParam(':temp', $temp, PDO::PARAM_STR);
$query->bindParam(':doctor_advice', $doctor_advice, PDO::PARAM_STR);
$query->bindParam(':respiration', $resp, PDO::PARAM_STR);
$query->bindParam(':reqno', $ipno, PDO::PARAM_STR);
$query->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$query->bindParam(':ip', $ipaddress, PDO::PARAM_STR);
$query->execute();

//Access verified//
//check if details already exists or not

$check_patient=$pdoread->prepare("SELECT `doctor_assessment`.`umr_no` FROM `doctor_assessment`
LEFT JOIN pain_scores_table ON doctor_assessment.pain_id=pain_scores_table.page_id
WHERE `source`='OP' AND `doctor_assessment`.`ipno`=:reqno AND `doctor_assessment`.`umr_no`=:umr_no AND `doctor_assessment`.`doctor_uid`=:doctor_uid AND `doctor_assessment`.`status`='Active' AND `doctor_assessment`.`cost_center`=:branch ");
$check_patient->bindParam(':reqno', $ipno, PDO::PARAM_STR);
$check_patient->bindParam(':umr_no', $umrno, PDO::PARAM_STR);
$check_patient->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$check_patient->bindParam(':doctor_uid', $doc_id, PDO::PARAM_STR);
$check_patient->execute();

if(($check_patient->rowCount() > 0)){
    //update the data on details

   $update_data=$pdo4->prepare("UPDATE `doctor_assessment` SET `chief_complaint`=:chiefcomplaint,`present_illness`=:ptillness,`past_history`=:pthistory,`treatment_history`=:treatment_history,`allergies`=:allergies,`personal_history`=:personal_history,`family_history`=:family_history,`obstetric_history`=:obstetric_history,`smoking_status`=:smoking_status,`alcohol_drugs`=:alcohol_drugs,`general_examination`=:ptexamination,`vitals`=:vitals,`systemic_examination`=:systemic_examination,`adviced_investigations`=:previous_investigations,`emergency_diagnosis`=:ptdiagnosis,`assessment_plan`=:assessment_plan,`medication_reconciliation`=:medication_reconciliation,`review`=:review,`pain_assessment`=:pain_assessment,`trans_shift_disc`=:trans_shift_disc,`facial_expression`=:facial_expression,`upper_limb_movements`=:upper_limb_movements,`compliance_mechanical_ventilation`=:compliance_mechanical_ventilation,
    `modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP, `week_days`=:week_days, `correct_gestational_age`=:correct_gestational_age, `perinatal_history`=:perinatal_history, `development_history`=:development_history, `immunization_history`=:immunization_history, `feeding_history`=:feeding_history, `current_medication`=:current_medication,`local_examination`=:local_examination ,`gynecology_history`=:gynecology_history,`birth_history`=:birth_history  WHERE `status`='Active' AND `cost_center`=:branch AND`ipno`=:ipno AND `umr_no`=:umrno AND `doctor_uid`=:doctor_uid  ");

$update_data->bindParam(':current_medication', $current_medication , PDO::PARAM_STR);
$update_data->bindParam(':feeding_history', $feeding_history , PDO::PARAM_STR);
$update_data->bindParam(':immunization_history', $immunization_history , PDO::PARAM_STR);
$update_data->bindParam(':development_history', $development_history , PDO::PARAM_STR);
$update_data->bindParam(':perinatal_history', $perinatal_history , PDO::PARAM_STR);
$update_data->bindParam(':correct_gestational_age', $correct_gestational_age , PDO::PARAM_STR);
$update_data->bindParam(':week_days', $week_days , PDO::PARAM_STR);
// $update_data->bindParam(':gestational_atb', $gestational_atb , PDO::PARAM_STR);
    $update_data->bindParam(':ipno', $ipno , PDO::PARAM_STR);
    $update_data->bindParam(':umrno', $umrno , PDO::PARAM_STR);
    $update_data->bindParam(':doctor_uid', $doc_id , PDO::PARAM_STR);
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
    $update_data->bindParam(':local_examination', $local_examination , PDO::PARAM_STR);
    $update_data->bindParam(':gynecology_history', $gynecology_history , PDO::PARAM_STR);
    $update_data->bindParam(':birth_history', $birth_history , PDO::PARAM_STR);
    $update_data->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
    $update_data->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
    $update_data->execute();
    if($update_data->rowCount()>0){
        http_response_code(200);
        $response['error']=false;
        $response['message']='Updated Successfully';

$update_pain_details=$pdo4->prepare("UPDATE `pain_scores_table` SET  `pain_type`=:pain_type,`pain_rate`=:pain_rate,`doc_name`=:doc_name,`pain`=:pain,`pain_loc`=:pain_loc,`pain_char`=:pain_char,`acute_chornic`=:acute_chornic,`pain_duration`=:pain_duration,`dec_pain`=:dec_pain,`inc_pain`=:inc_pain,`action_plan`=:action_plan,`intervention`=:intervention,`interventiond`=:interventiond,`heparin_therapy`=:heparin_therapy,`modifiedby`=:userid,`modifiedon`=CURRENT_TIMESTAMP,
`facial_expression_bp`=:facial_expression_bp,`upper_limb_movements_bp`=:upper_limb_movements_bp,`compliance_bp`=:compliance_bp,`total_score_bp`=:total_score_bp ,`cpap`=:cpap ,`hfnc`=:hfnc,`air_vo2`=:air_vo2
 WHERE `status`='Active' AND `admission_num`=:ipno AND `doctor_uid`=:doctor_uid AND `page_id`=:idnum ");
$update_pain_details->bindParam(':idnum', $idnum , PDO::PARAM_STR);
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
$update_pain_details->bindParam(':facial_expression_bp', $facial_expression_bp , PDO::PARAM_STR);
$update_pain_details->bindParam(':upper_limb_movements_bp', $upper_limb_movements_bp , PDO::PARAM_STR);
$update_pain_details->bindParam(':compliance_bp', $compliance_bp , PDO::PARAM_STR);
$update_pain_details->bindParam(':total_score_bp', $total_score_bp , PDO::PARAM_STR);
$update_pain_details->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update_pain_details->bindParam(':doctor_uid', $doc_id , PDO::PARAM_STR);
$update_pain_details->bindParam(':ipno', $ipno , PDO::PARAM_STR);
$update_pain_details->bindParam(':air_vo2', $air_vo2 , PDO::PARAM_STR);
$update_pain_details->bindParam(':hfnc', $hfnc , PDO::PARAM_STR);
$update_pain_details->bindParam(':cpap', $cpap , PDO::PARAM_STR);
$update_pain_details->execute();
//vitals update

    }else{
		http_response_code(503);
         $response['error']=true;
        $response['message']='Sorry! Updation failed!';
    }
}else{

    //SELECT IFNULL(MAX(`pain_id`),CONCAT('OR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS id  FROM `doctor_assessment` WHERE `pain_id` LIKE '%OR%'
    $generate_id=$pdoread->prepare("SELECT IFNULL(MAX(`pain_id`),CONCAT('OR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS id  FROM `doctor_assessment` WHERE `pain_id` LIKE '%OR%' ");
$generate_id->execute();
$gid=$generate_id->fetch(PDO::FETCH_ASSOC);
$uid=$gid['id'];
$unique_id=++$uid;


$query_doctor=$pdo4->prepare("INSERT IGNORE INTO `doctor_assessment`(`sno`, `ipno`, `umr_no`, `doctor_uid`, `source`, `chief_complaint`, `present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`, `smoking_status`, `alcohol_drugs`, `general_examination`, `vitals`, `systemic_examination`, `adviced_investigations`, `emergency_diagnosis`, `assessment_plan`, `medication_reconciliation`, `review`, `pain_assessment`, `trans_shift_disc`, `pain_id`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`, `cost_center`
, `week_days`, `correct_gestational_age`, `perinatal_history`, `development_history`, `immunization_history`, `feeding_history`, `current_medication`,`local_examination`,`gynecology_history`,birth_history) VALUES (NULL,:ipno,:umrno,:doc_id,'OP',:chiefcomplaint,:ptillness,:pthistory, :treatment_history, :allergies, :personal_history, :family_history, :obstetric_history, :smoking_status, :alcohol_drugs, :ptexamination, :vitals, :systemic_examination, :previous_investigations, :ptdiagnosis, :assessment_plan, :medication_reconciliation, :review, :pain_assessment, :trans_shift_disc,:uid, :facial_expression, :upper_limb_movements, :compliance_mechanical_ventilation, :userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:branch ,:week_days,:correct_gestational_age,:perinatal_history,:development_history,:immunization_history,:feeding_history,:current_medication,:local_examination,:gynecology_history,:birth_history)");
//:pain_type, :pain_rate, :doc_name, :pain, :pain_loc, :pain_char, :acute_chornic, :pain_duration, :dec_pain, :inc_pain, :action_plan, :intervention, :interventiond, :heparin_therapy,
$query_doctor->bindParam(':current_medication', $current_medication , PDO::PARAM_STR);
$query_doctor->bindParam(':feeding_history', $feeding_history , PDO::PARAM_STR);
$query_doctor->bindParam(':immunization_history', $immunization_history , PDO::PARAM_STR);
$query_doctor->bindParam(':development_history', $development_history , PDO::PARAM_STR);
$query_doctor->bindParam(':perinatal_history', $perinatal_history , PDO::PARAM_STR);
$query_doctor->bindParam(':correct_gestational_age', $correct_gestational_age , PDO::PARAM_STR);
$query_doctor->bindParam(':week_days', $week_days , PDO::PARAM_STR);
$query_doctor->bindParam(':birth_history', $birth_history , PDO::PARAM_STR);
// $query_doctor->bindParam(':gestational_atb', $gestational_atb , PDO::PARAM_STR);
$query_doctor->bindParam(':ipno', $ipno , PDO::PARAM_STR);
$query_doctor->bindParam(':umrno', $umrno , PDO::PARAM_STR);
$query_doctor->bindParam(':doc_id', $doc_id , PDO::PARAM_STR);
$query_doctor->bindParam(':chiefcomplaint', $chiefcomplaint , PDO::PARAM_STR);
$query_doctor->bindParam(':ptillness', $ptillness , PDO::PARAM_STR);
$query_doctor->bindParam(':pthistory', $pthistory , PDO::PARAM_STR);
$query_doctor->bindParam(':treatment_history', $treatment_history , PDO::PARAM_STR);
$query_doctor->bindParam(':allergies', $allergies , PDO::PARAM_STR);
$query_doctor->bindParam(':personal_history', $personal_history , PDO::PARAM_STR);
$query_doctor->bindParam(':family_history', $family_history , PDO::PARAM_STR);
$query_doctor->bindParam(':obstetric_history', $obstetric_history , PDO::PARAM_STR);
$query_doctor->bindParam(':smoking_status', $smoking_status , PDO::PARAM_STR);
$query_doctor->bindParam(':alcohol_drugs', $alcohol_drugs , PDO::PARAM_STR);
$query_doctor->bindParam(':ptexamination', $ptexamination , PDO::PARAM_STR);
$query_doctor->bindParam(':vitals', $vitals , PDO::PARAM_STR);
$query_doctor->bindParam(':systemic_examination', $systemic_examination , PDO::PARAM_STR);
$query_doctor->bindParam(':previous_investigations', $previous_investigations , PDO::PARAM_STR);
$query_doctor->bindParam(':ptdiagnosis', $ptdiagnosis , PDO::PARAM_STR);
$query_doctor->bindParam(':assessment_plan', $assessment_plan , PDO::PARAM_STR);
$query_doctor->bindParam(':medication_reconciliation', $medication_reconciliation , PDO::PARAM_STR);
$query_doctor->bindParam(':review', $review , PDO::PARAM_STR);
$query_doctor->bindParam(':pain_assessment', $pain_assessment , PDO::PARAM_STR);
$query_doctor->bindParam(':trans_shift_disc', $trans_shift_disc , PDO::PARAM_STR);
$query_doctor->bindParam(':uid', $unique_id , PDO::PARAM_STR);
$query_doctor->bindParam(':facial_expression', $facial_expression , PDO::PARAM_STR);
$query_doctor->bindParam(':upper_limb_movements', $upper_limb_movements , PDO::PARAM_STR);
$query_doctor->bindParam(':compliance_mechanical_ventilation', $compliance_mechanical_ventilation , PDO::PARAM_STR);
$query_doctor->bindParam(':gynecology_history', $gynecology_history , PDO::PARAM_STR);
$query_doctor->bindParam(':local_examination', $local_examination , PDO::PARAM_STR);
$query_doctor->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$query_doctor->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$query_doctor->execute();
if($query_doctor->rowCount()>0){
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Inserted Successfully';
    //add to pain score
$add_to_pain_score=$pdo4->prepare("INSERT IGNORE INTO `pain_scores_table`(`sno`, `page_id`, `assessment_type`, `admission_num`, `umr_no`, `doctor_uid`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`, `facial_expression_bp`,`upper_limb_movements_bp`,`compliance_bp`,`total_score_bp`,`cpap`,`hfnc`,`air_vo2`) VALUES (NULL,:idnum,'OP-ASSESSMENT',:ipno,:umr,:doctor_uid,:pain_type, :pain_rate, :doc_name, :pain, :pain_loc, :pain_char, :acute_chornic, :pain_duration, :dec_pain, :inc_pain, :action_plan, :intervention, :interventiond, :heparin_therapy,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:facial_expression_bp,:upper_limb_movements_bp,:compliance_bp,:total_score_bp,:cpap,:hfnc,:air_vo2)");
$add_to_pain_score->bindParam(':idnum', $unique_id , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':umr', $umrno , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':doctor_uid', $doc_id , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':ipno', $ipno , PDO::PARAM_STR);
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
$add_to_pain_score->bindParam(':total_score_bp', $total_score_bp , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':compliance_bp', $compliance_bp , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':upper_limb_movements_bp', $upper_limb_movements_bp , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':facial_expression_bp', $facial_expression_bp , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':air_vo2', $air_vo2 , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':hfnc', $hfnc , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':cpap', $cpap , PDO::PARAM_STR);
$add_to_pain_score->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$add_to_pain_score->execute();

}else{
	http_response_code(503);
     $response['error']=true;
    $response['message']='Sorry! Updation failed!';
}
}
}else{
	http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
}
}else{
	http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! some details are missing';
}
}catch(PDOException $e) {
    http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>