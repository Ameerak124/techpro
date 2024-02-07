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
$reqno=($data->reqno);
try{

if(!empty($accesskey)&& !empty($reqno)){
//Check access 
$check =$pdoread->prepare("SELECT `userid`AS empid FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check -> execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0) {
//Access verified//
$query=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`umr_registration`.`patient_name`AS ptname,`umr_registration`.`mobile_no`AS mob,DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),`umr_registration`.`patient_age`)), '%Y')+0 AS Age,
`op_biling_history`.`blood_pressure` AS bloodpressure,`op_biling_history`.`weight`,`op_biling_history`.`height`,`op_biling_history`.`rbs`,`op_biling_history`.`spo2`,`op_biling_history`.`pr`,`op_biling_history`.`temperature`,`doctor_advice`,`op_biling_history`.`billno` AS billno,`op_biling_history`.`requisition_no`,`op_biling_history`.`umr_no`,`op_biling_history`.`servicecode` AS doc_id
FROM (SELECT @a:=0) AS a,`umr_registration` 
INNER JOIN `op_biling_history`  ON `op_biling_history`.`umr_no`=`umr_registration`.`umrno` WHERE `op_biling_history`.`status`='Visible' AND `op_biling_history`.`requisition_no`=:reqno ");
$query->bindParam(':reqno', $reqno, PDO::PARAM_STR);
$query->execute();
$queryres=$query->fetch(PDO::FETCH_ASSOC);
if($query->rowCount()>0) {
    //  $sn=0;
 http_response_code(200);
    $response['error']=false;
    $response['message']='Data found on status';
        $response['sno']=$queryres['sno'];
        $response['ptname']=$queryres['ptname'];
        $response['mob']=$queryres['mob'];
        $response['Age']=$queryres['Age'];
        $response['bloodpressure']=$queryres['bloodpressure'];
        $response['weight']=$queryres['weight'];
        $response['height']=$queryres['height'];
        $response['rbs']=$queryres['rbs'];
        $response['spo2']=$queryres['spo2'];
        $response['pr']=$queryres['pr'];
        $response['temperature']=$queryres['temperature'];
        // $response['chief_complaint']=$queryres['chief_complaint'];
        // $response['patient_illness']=$queryres['patient_illness'];
        // $response['patient_history']=$queryres['patient_history'];
        // $response['general_examination']=$queryres['general_examination'];
        // $response['patient_diagnosis']=$queryres['patient_diagnosis'];
        $response['doctor_advice']=$queryres['doctor_advice'];
        $response['billno']=$queryres['billno'];
        $response['requisition_no']=$queryres['requisition_no'];
        $response['umr_no']=$queryres['umr_no'];
        $response['doc_id']=$queryres['doc_id'];
        //    
        //get the doctor assessment details
        $query_doctor=$pdoread->prepare("SELECT `doctor_assessment`.`doctor_uid`, `source`, `chief_complaint`, `present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`, `smoking_status`, `alcohol_drugs`, `general_examination`, `vitals`, `systemic_examination`, `adviced_investigations`, `emergency_diagnosis`, `assessment_plan`, `medication_reconciliation`, `review`, `pain_assessment`, `trans_shift_disc`, `pain_type`, `pain_rate`, `doc_name`, `pain_scores_table`.`pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`,`pain_id` AS page_id FROM `doctor_assessment` left join `pain_scores_table` ON `pain_scores_table`.`page_id`=`doctor_assessment`.`pain_id` AND `pain_scores_table`.`admission_num`=`doctor_assessment`.`ipno` AND `pain_scores_table`.`doctor_uid`=`doctor_assessment`.`doctor_uid` WHERE `ipno`=:reqno AND `doctor_assessment`.`status` = 'Active' ORDER BY `doctor_assessment`.`modifiedon` DESC  LIMIT 1");
        $query_doctor->bindParam(':reqno', $reqno, PDO::PARAM_STR);
        $query_doctor->execute();
        // if($query_doctor->rowCount() > 0){
        $query_result=$query_doctor->fetch(PDO::FETCH_ASSOC);
                $response['page_id']=$query_result['page_id'];
                $response['chief_complaint']=$query_result['chief_complaint'];
                $response['present_illness']=$query_result['present_illness'];
                $response['past_history']=$query_result['past_history'];
                $response['treatment_history']=$query_result['treatment_history'];
                $response['allergies']=$query_result['allergies'];
                $response['personal_history']=$query_result['personal_history'];
                $response['family_history']=$query_result['family_history'];
                $response['obstetric_history']=$query_result['obstetric_history'];
                $response['smoking_status']=$query_result['smoking_status'];
                $response['alcohol_drugs']=$query_result['alcohol_drugs'];            
                $response['general_examination']=$query_result['general_examination'];
                $response['vitals']=$query_result['vitals'];
                $response['systemic_examination']=$query_result['systemic_examination'];
                $response['previous_investigations']=$query_result['previous_investigations'];
                $response['patient_diagnosis']=$query_result['patient_diagnosis'];
                $response['assessment_plan']=$query_result['assessment_plan'];
                $response['medication_reconciliation']=$query_result['medication_reconciliation'];
                $response['review']=$query_result['review'];
                $response['pain_assessment']=$query_result['pain_assessment'];
                $response['trans_shift_disc']=$query_result['trans_shift_disc'];
                $response['pain_type']=$query_result['pain_type'];
                $response['pain_rate']=$query_result['pain_rate'];
                $response['doc_name']=$query_result['doc_name'];
                $response['pain']=$query_result['pain'];
                $response['pain_loc']=$query_result['pain_loc'];
                $response['pain_char']=$query_result['pain_char'];
                $response['acute_chornic']=$query_result['acute_chornic'];
                $response['pain_duration']=$query_result['pain_duration'];
                $response['dec_pain']=$query_result['dec_pain'];
                $response['inc_pain']=$query_result['inc_pain'];
                $response['action_plan']=$query_result['action_plan'];
                $response['intervention']=$query_result['intervention'];
                $response['interventiond']=$query_result['interventiond'];
                $response['heparin_therapy']=$query_result['heparin_therapy'];
                $response['facial_expression']=$query_result['facial_expression'];
                $response['upper_limb_movements']=$query_result['upper_limb_movements'];
                $response['compliance_mechanical_ventilation']=$query_result['compliance_mechanical_ventilation'];
        // }else{
        //     $response['error']=true;
        //     $response['message']="No Data Found";
        // }
        

        



        }else{
    http_response_code(503);			
    $response['error']=true;
    $response['message']='No data found on status';
        }
      

   }else {
    http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
   }
   }else{
    http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
   }
} catch(PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message']= "Connection failed";
 }
echo json_encode($response);
$pdoread = null;
?>