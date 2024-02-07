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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ip = trim($data->ip);
$consultant = trim($data->consultant);
$cross_consultants = trim($data->cross_consultants);
$surgery_date = trim($data->surgery_date);
$diagnosis = trim($data->diagnosis);
$procedure_done = trim($data->procedure_done);
$chief_complaint = trim($data->chief_complaint);
$history_present_illness = trim($data->history_present_illness);
$past_history = trim($data->past_history);
$treatment_history = trim($data->treatment_history);
$allergies = trim($data->allergies);
$personal_history = trim($data->personal_history);
$family_history = trim($data->family_history);
$obstetric_history = trim($data->obstetric_history);
$general_examination = trim($data->general_examination);
$vitals = trim($data->vitals);
$systemic_examination = trim($data->systemic_examination);
$course_hospital = trim($data->course_hospital);
$investigation_reports = trim($data->investigation_reports);
$preventive_measures = trim($data->preventive_measures);
$urgent_care_instructions = trim($data->urgent_care_instructions);
$dietary_advised = trim($data->dietary_advised);
$follow_up_instructions = trim($data->follow_up_instructions);
$transfer_shift_discharge_details = trim($data->transfer_shift_discharge_details);
$medications_advised = trim($data->medications_advised);
$is_dead = trim($data->is_dead);
$developmental_history = trim($data->developmental_history);
$immunization_history = trim($data->immunization_history);
$birth_history = trim($data->birth_history);

try {
    
    if(!empty($accesskey) && !empty($ip) ){
    //Check User Access Start
    $check = $pdoread -> prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check -> execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
    if($check -> rowCount() > 0){
            $validate = $pdoread -> prepare("SELECT `admissionstatus` AS admissionstatus,`umrno`,`patient_category`,`dis_edit_status` FROM `registration` WHERE `admissionno` = :ip AND `admissionstatus` IN ('Admitted','Initiated Discharge','Discharged')");
            $validate->bindParam(':ip', $ip, PDO::PARAM_STR);
            //$validate->bindParam(':umrno', $umrno, PDO::PARAM_STR);
            $validate -> execute();
            $validates = $validate->fetch(PDO::FETCH_ASSOC);
            if(($validate -> rowCount() > 0 && $validates['admissionstatus'] != 'Discharged') || ($validates['admissionstatus'] == 'Discharged' && $validates['dis_edit_status'] == 1)){
                
                // if($validates['admissionstatus']== $admitted){



                    //
                    $get_details=$pdoread->prepare("SELECT  `ip` FROM `ip_discharge_summary` WHERE `estatus`='Active' AND `ip`=:ip  ");
                    $get_details->bindParam(':ip', $ip, PDO::PARAM_STR);
                    $get_details->execute();
                     if($get_details-> rowCount() == 0){
                   
                    $insert = $pdo4->prepare("INSERT IGNORE INTO `ip_discharge_summary`(`sno`, `ip`, `umrno`, `consultant`, `cross_consultants`, `surgery_date`, `diagnosis`, `procedure_done`, `chief_complaint`, `history_present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`, `general_examination`, `vitals`, `systemic_examination`, `course_hospital`, `investigation_reports`, `preventive_measures`, `urgent_care_instructions`, `dietary_advised`, `follow_up_instructions`, `transfer_shift_discharge_details`, `medications_advised`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `estatus`,`is_dead`,`birth_history`, `immunization_history`, `developmental_history`) (Select NULL, :ip, `umrno`, :consultant, :cross_consultants, :surgery_date, :diagnosis, :procedure_done, :chief_complaint, :history_present_illness, :past_history, :treatment_history, :allergies, :personal_history, :family_history, :obstetric_history, :general_examination, :vitals, :systemic_examination, :course_hospital, :investigation_reports, :preventive_measures, :urgent_care_instructions, :dietary_advised, :follow_up_instructions, :transfer_shift_discharge_details, :medications_advised, :userid,  CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP,  'Active',:is_dead 
                    ,:birth_history ,:immunization_history ,:developmental_history FROM `registration` WHERE `admissionno` = :ip) ");
                    $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
                   // $insert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
                    $insert->bindParam(':consultant', $consultant, PDO::PARAM_STR);
                    $insert->bindParam(':cross_consultants', $cross_consultants, PDO::PARAM_STR);
                    $insert->bindParam(':surgery_date', $surgery_date, PDO::PARAM_STR);
                    $insert->bindParam(':diagnosis', $diagnosis, PDO::PARAM_STR);
                    $insert->bindParam(':procedure_done', $procedure_done, PDO::PARAM_STR);
                    $insert->bindParam(':chief_complaint', $chief_complaint, PDO::PARAM_STR);
                    $insert->bindParam(':history_present_illness', $history_present_illness, PDO::PARAM_STR);
                    $insert->bindParam(':past_history', $past_history, PDO::PARAM_STR);
                    $insert->bindParam(':treatment_history', $treatment_history, PDO::PARAM_STR);
                    $insert->bindParam(':allergies', $allergies, PDO::PARAM_STR);
                    $insert->bindParam(':personal_history', $personal_history, PDO::PARAM_STR);
                    $insert->bindParam(':family_history', $family_history, PDO::PARAM_STR);
                    $insert->bindParam(':obstetric_history', $obstetric_history, PDO::PARAM_STR);
                    $insert->bindParam(':general_examination', $general_examination, PDO::PARAM_STR);
                    $insert->bindParam(':vitals', $vitals, PDO::PARAM_STR);
                    $insert->bindParam(':systemic_examination', $systemic_examination, PDO::PARAM_STR);
                    $insert->bindParam(':course_hospital', $course_hospital, PDO::PARAM_STR);
                    $insert->bindParam(':investigation_reports', $investigation_reports, PDO::PARAM_STR);
                    $insert->bindParam(':preventive_measures', $preventive_measures, PDO::PARAM_STR);
                    $insert->bindParam(':urgent_care_instructions', $urgent_care_instructions, PDO::PARAM_STR);
                    $insert->bindParam(':dietary_advised', $dietary_advised, PDO::PARAM_STR);
                    $insert->bindParam(':follow_up_instructions', $follow_up_instructions, PDO::PARAM_STR);
                    $insert->bindParam(':transfer_shift_discharge_details', $transfer_shift_discharge_details, PDO::PARAM_STR);
                    $insert->bindParam(':immunization_history', $immunization_history, PDO::PARAM_STR);
                    $insert->bindParam(':birth_history', $birth_history, PDO::PARAM_STR);
                    $insert->bindParam(':developmental_history', $developmental_history, PDO::PARAM_STR);
                    $insert->bindParam(':medications_advised', $medications_advised, PDO::PARAM_STR);
                    $insert->bindParam(':is_dead', $is_dead, PDO::PARAM_STR);
                    $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
                    $insert->execute();
                //     $update = $con->prepare("UPDATE `registration` SET `admissionstatus` = 'Initiated Discharge',`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP WHERE `admissionno` = :ip AND `admissionstatus` =:astatus ");
                //     $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
                //     $update->bindParam(':ip', $ip, PDO::PARAM_STR);
                //     $update->bindParam(':astatus', $admitted, PDO::PARAM_STR);
                // //  $update->execute();
                    if($insert-> rowCount() > 0){
						http_response_code(200);
                        $response['error'] = false;
                      $response['message']= "Data Inserted";


                      }else{
						  http_response_code(503);
                          $response['error'] = true;
                          $response['message']= "Patient Already Discharged";
                      }
                    }else{
                    // } elseif($validates['admissionstatus'] == $initiated){
                        $update = $pdo4->prepare(" UPDATE  `ip_discharge_summary` SET `consultant` = :consultant,  `cross_consultants` = :cross_consultants, `surgery_date` = :surgery_date,  `diagnosis` =  :diagnosis, `procedure_done` = :procedure_done, `chief_complaint` = :chief_complaint, `history_present_illness` = :history_present_illness, `past_history` = :past_history, `treatment_history` = :treatment_history, `allergies` = :allergies,  `personal_history` = :personal_history, `family_history` =  :family_history, `obstetric_history` = :obstetric_history, `general_examination` = :general_examination,  `vitals` = :vitals, `systemic_examination` = :systemic_examination, `course_hospital` = :course_hospital, `investigation_reports` = :investigation_reports, `preventive_measures` = :preventive_measures,  `urgent_care_instructions` = :urgent_care_instructions, `dietary_advised` = :dietary_advised, `follow_up_instructions` = :follow_up_instructions,  `transfer_shift_discharge_details` = :transfer_shift_discharge_details, `medications_advised` =  :medications_advised,  `modifiedby` = :userid, `modifiedon` =  CURRENT_TIMESTAMP ,`is_dead`=:is_dead ,`birth_history`=:birth_history, `immunization_history`=:immunization_history, `developmental_history`=:developmental_history
                        
                        WHERE `estatus` = 'Active' AND `ip` = :ip");
                        $update->bindParam(':ip', $ip, PDO::PARAM_STR);
                        //$update->bindParam(':umrno', $umrno, PDO::PARAM_STR);
                        $update->bindParam(':consultant', $consultant, PDO::PARAM_STR);
                        $update->bindParam(':cross_consultants', $cross_consultants, PDO::PARAM_STR);
                        $update->bindParam(':surgery_date', $surgery_date, PDO::PARAM_STR);
                        $update->bindParam(':diagnosis', $diagnosis, PDO::PARAM_STR);
                        $update->bindParam(':procedure_done', $procedure_done, PDO::PARAM_STR);
                        $update->bindParam(':chief_complaint', $chief_complaint, PDO::PARAM_STR);
                        $update->bindParam(':history_present_illness', $history_present_illness, PDO::PARAM_STR);
                        $update->bindParam(':past_history', $past_history, PDO::PARAM_STR);
                        $update->bindParam(':treatment_history', $treatment_history, PDO::PARAM_STR);
                        $update->bindParam(':allergies', $allergies, PDO::PARAM_STR);
                        $update->bindParam(':personal_history', $personal_history, PDO::PARAM_STR);
                        $update->bindParam(':family_history', $family_history, PDO::PARAM_STR);
                        $update->bindParam(':obstetric_history', $obstetric_history, PDO::PARAM_STR);
                        $update->bindParam(':general_examination', $general_examination, PDO::PARAM_STR);
                        $update->bindParam(':vitals', $vitals, PDO::PARAM_STR);
                        $update->bindParam(':systemic_examination', $systemic_examination, PDO::PARAM_STR);
                        $update->bindParam(':course_hospital', $course_hospital, PDO::PARAM_STR);
                        $update->bindParam(':investigation_reports', $investigation_reports, PDO::PARAM_STR);
                        $update->bindParam(':preventive_measures', $preventive_measures, PDO::PARAM_STR);
                        $update->bindParam(':urgent_care_instructions', $urgent_care_instructions, PDO::PARAM_STR);
                        $update->bindParam(':dietary_advised', $dietary_advised, PDO::PARAM_STR);
                        $update->bindParam(':follow_up_instructions', $follow_up_instructions, PDO::PARAM_STR);
                        $update->bindParam(':transfer_shift_discharge_details', $transfer_shift_discharge_details, PDO::PARAM_STR);
                        $update->bindParam(':medications_advised', $medications_advised, PDO::PARAM_STR);
                        $update->bindParam(':is_dead', $is_dead, PDO::PARAM_STR);
                        $update->bindParam(':birth_history', $birth_history, PDO::PARAM_STR);
                        $update->bindParam(':immunization_history', $immunization_history, PDO::PARAM_STR);
                        $update->bindParam(':developmental_history', $developmental_history, PDO::PARAM_STR);
                        $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
                        $update->execute();
                        if($update-> rowCount() > 0){
							http_response_code(200);
                            $response['error'] = false;
                          $response['message']= "Updated Successfully";
                         
                          }else{
							  http_response_code(503);
                              $response['error'] = true;
                              $response['message']= "Data Not Updated";
                          }
                    }
                    }else{
						http_response_code(503);
                        $response['error'] = true;
                        $response['message']= "Patient already Discharged";
                    }
                // }else{
                //     $response['error'] = true;
                //     $response['message']= "Sorry! you are not allowed to do the changes";
                // }
        //Check User Access End
        }else{
			http_response_code(400);
            $response['error'] = true;
              $response['message']= "Access Denied";
          }
        }else{
			http_response_code(400);
            $response['error'] = true;
            $response['message']= "Sorry! some details are missing";
        }
        //Check empty Parameters End
}catch(PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>