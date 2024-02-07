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
$ipno = ($data->ipno);
$atype = ($data->atype);
$id = ($data->id);
try {
    if (!empty($accesskey) && !empty($ipno) && !empty($atype) && !empty($id)) {
        $check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {
            $listt = $pdoread->prepare("SELECT `admission_num`, `umr_no`, `nursing_id`, `patient_category`, `assessment_type`, `assessment_id`, `assessment_category`, `admitted_status`, `relation_name`, `relation_type`, `phone_number`, `other_relation_name`, `other_relation_type`, `other_phone_number`, `primary_language`, `education_type`, `education_others`, `reason_for_enquiry`, DATE(`captured_date_and_time`)AS cdate,TIME(`captured_date_and_time`)AS ctime, `bp_check_position_1`, `bp_dd_1`, `bp_systolic_1`, `bp_diastolic_1`, `bp_check_position_2`, `bp_dd_2`, `bp_systolic_2`, `bp_diastolic_2`, `bp_check_position_3`, `bp_dd_3`, `bp_systolic_3`, `bp_diastolic_3`, `bp_units`, `pulse_dd`, `pulse_rate`, `pulse_units`, `heart_rate_dd`, `heart_rate`, `heart_rate_units`, `respiratory_rate`, `respiratory_units`, `weight`, `weight_units`, `height_rate`, `height_units`, `bmi_rate`, `bmi_units`, `bsa_rate`, `bsa_units`, `temp_dd`, `temp_rate`, `temp_units`, `spo2_dd1`, `spo2_dd2`, `spo2_rate`, `pain_score`, `grbs`, `grbs_units`, `blood_group`, `quest_1`, `quest_1_response`, `quest_2`, `quest_2_response`, `quest_3`, `quest_3_response`, `dentures`, `dentures_data`, `hearing_aid`, `hearing_aid_data`, `eye_glasses`, `eye_glasses_data`, `contact_lens`, `contact_lens_data`, `valubles_type`, `valubles`, `valubles_data`, `relation_for_patient`, `handover_to`, `contact_number`, `activity`, `bathing`, `eating`, `dressing`, `toilet_use`, `others`, `orientation`,`orientation_others`, `allergies_medication`,`allergies_medication_data`, `food`, `food_data`, `other_allergies`, `quest_4`, `quest_4_response`, `quest_5`, `quest_5_response`, `quest_6`, `quest_6_response`, `quest_7`, `quest_7_response`, `quest_8`, `quest_8_response`, `quest_9`, `quest_9_response`, `nursing_assessment`, `goal`, `planning`, `implementation`, `evaluation`, `pain_scale_type`, `painscale_score`, `doctor_name`, `pain`, `pain_location`, `pain_character`, `pain_duration_type`, `pain_duration`, `factor_decreasing_pain`, `factor_increasing_pain`, `action_plan`, `intervention`, `intervention_therapy`, `heparin_therapy`, `quest_10`, `quest_10_response`, `quest_11`, `quest_11_response`, `quest_12`, `quest_12_response`, `quest_13`, `quest_13_response`, `quest_14`, `quest_14_response`, `quest_15`, `quest_15_response` ,`behaviour_pain_1`,`behaviour_pain_2`,`behaviour_pain_3`,`behaviour_pain_score`, `created_on`, `created_by`, `completed_status`, `completed_date` AS complete_datetime,  (CASE WHEN TIMEDIFF(`completed_date`,`created_on`) < 0 THEN 0 ELSE TIME_FORMAT(TIMEDIFF(`completed_date`,`created_on`),'%H hr %i mins') END ) diff_time FROM `nursing_assessment_table` WHERE `is_active`='1' AND `admission_status`='Active' AND `admission_num`=:ipno AND `assessment_type`=:atype AND  `cost_center`=:branch AND `assessment_id`=:id  LIMIT 1");
            $listt->bindParam(':atype', $atype, PDO::PARAM_STR);
            $listt->bindParam(':ipno', $ipno, PDO::PARAM_STR);
            $listt->bindParam(':id', $id, PDO::PARAM_STR);
            $listt->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
            $listt->execute();
            if ($listt->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Found";
                // $sn=0;
                $res = $listt->fetch(PDO::FETCH_ASSOC);
                $response['admission_num'] = $res['admission_num'];
                $response['umr_no'] = $res['umr_no'];
                $response['nursing_id'] = $res['nursing_id'];
                $response['patient_category'] = $res['patient_category'];
                $response['assessment_type'] = $res['assessment_type'];
                $response['assessment_category'] = $res['assessment_category'];
                $response['admitted_status'] = $res['admitted_status'];
                $response['relation_name'] = $res['relation_name'];
                $response['relation_type'] = $res['relation_type'];
                $response['phone_number'] = $res['phone_number'];
                $response['other_relation_name'] = $res['other_relation_name'];
                $response['other_relation_type'] = $res['other_relation_type'];
                $response['other_phone_number'] = $res['other_phone_number'];
                $response['primary_language'] = $res['primary_language'];
                $response['education_type'] = $res['education_type'];
                $response['education_others'] = $res['education_others'];
                $response['reason_for_enquiry'] = $res['reason_for_enquiry'];
                $response['captured_date'] = $res['cdate'];
                $response['captured_time'] = $res['ctime'];
                $response['bp_check_position_1'] = $res['bp_check_position_1'];
                $response['bp_dd_1'] = $res['bp_dd_1'];
                $response['bp_systolic_1'] = $res['bp_systolic_1'];
                $response['bp_diastolic_1'] = $res['bp_diastolic_1'];
                $response['bp_check_position_2'] = $res['bp_check_position_2'];
                $response['bp_dd_2'] = $res['bp_dd_2'];
                $response['bp_systolic_2'] = $res['bp_systolic_2'];
                $response['bp_diastolic_2'] = $res['bp_diastolic_2'];
                $response['bp_check_position_3'] = $res['bp_check_position_3'];
                $response['bp_dd_3'] = $res['bp_dd_3'];
                $response['bp_systolic_3'] = $res['bp_systolic_3'];
                $response['bp_diastolic_3'] = $res['bp_diastolic_3'];
                $response['bp_units'] = $res['bp_units'];
                $response['heart_rate_dd'] = $res['heart_rate_dd'];
                $response['heart_rate'] = $res['heart_rate'];
                $response['pulse_dd'] = $res['pulse_dd'];
                $response['respiratory_rate'] = $res['respiratory_rate'];
                $response['respiratory_units'] = $res['respiratory_units'];
                $response['pulse_rate'] = $res['pulse_rate'];
                $response['height_rate'] = $res['height_rate'];
                $response['weight'] = $res['weight'];
                $response['weight_units'] = $res['weight_units'];
                $response['height_units'] = $res['height_units'];
                $response['bmi_rate'] = $res['bmi_rate'];
                $response['bmi_units'] = $res['bmi_units'];
                $response['bsa_rate'] = $res['bsa_rate'];
                $response['bsa_units'] = $res['bsa_units'];
                $response['temp_dd'] = $res['temp_dd'];
                $response['temp_rate'] = $res['temp_rate'];
                $response['temp_units'] = $res['temp_units'];
                $response['spo2_dd1'] = $res['spo2_dd1'];
                $response['spo2_dd2'] = $res['spo2_dd2'];
                $response['spo2_rate'] = $res['spo2_rate'];
                $response['pain_score'] = $res['pain_score'];
                $response['grbs'] = $res['grbs'];
                $response['grbs_units'] = $res['grbs_units'];
                $response['blood_group'] = $res['blood_group'];
                //2nd module start//
                $response['quest_1'] = $res['quest_1'];
                $response['quest_1_response'] = $res['quest_1_response'];
                $response['quest_2'] = $res['quest_2'];
                $response['quest_2_response'] = $res['quest_2_response'];
                $response['quest_3'] = $res['quest_3'];
                $response['quest_3_response'] = $res['quest_3_response'];
                $response['dentures'] = $res['dentures'];
                $response['dentures_data'] = $res['dentures_data'];
                $response['hearing_aid'] = $res['hearing_aid'];
                $response['hearing_aid_data'] = $res['hearing_aid_data'];
                $response['eye_glasses'] = $res['eye_glasses'];
                $response['eye_glasses_data'] = $res['eye_glasses_data'];
                $response['contact_lens'] = $res['quest_3_response'];
                $response['contact_lens_data'] = $res['contact_lens_data'];
                $response['valubles_type'] = $res['valubles_type'];
                $response['valubles'] = $res['valubles'];
                $response['valubles_data'] = $res['valubles_data'];
                $response['relation_for_patient'] = $res['relation_for_patient'];
                $response['handover_to'] = $res['handover_to'];
                $response['contact_number'] = $res['contact_number'];
                $response['activity'] = $res['activity'];
                $response['bathing'] = $res['bathing'];
                $response['eating'] = $res['eating'];
                $response['dressing'] = $res['dressing'];
                $response['toilet_use'] = $res['toilet_use'];
                $response['others'] = $res['others'];
                $response['orientation'] = $res['orientation'];
                $response['orientation_others'] = $res['orientation_others'];
                $response['allergies_medication'] = $res['allergies_medication'];
                $response['food'] = $res['food'];
                $response['food_data'] = $res['food_data'];
                $response['other_allergies'] = $res['other_allergies'];
                $response['quest_4'] = $res['quest_4'];
                $response['quest_4_response'] = $res['quest_4_response'];
                $response['quest_5'] = $res['quest_5'];
                $response['quest_5_response'] = $res['quest_5_response'];
                $response['quest_6'] = $res['quest_6'];
                $response['quest_6_response'] = $res['quest_6_response'];
                $response['quest_7'] = $res['quest_7'];
                $response['quest_7_response'] = $res['quest_7_response'];
                $response['quest_8'] = $res['quest_8'];
                $response['quest_8_response'] = $res['quest_8_response'];
                $response['quest_9'] = $res['quest_9'];
                $response['quest_9_response'] = $res['quest_9_response'];
                $response['implementation'] = $res['implementation'];
                $response['planning'] = $res['planning'];
                $response['goal'] = $res['goal'];
                $response['nursing_assessment'] = $res['nursing_assessment'];
                $response['evaluation'] = $res['evaluation'];
                $response['pain_scale_type'] = $res['pain_scale_type'];
                $response['painscale_score'] = $res['painscale_score'];
                $response['doctor_name'] = $res['doctor_name'];
                $response['pain'] = $res['pain'];
                $response['pain_location'] = $res['pain_location'];
                $response['pain_character'] = $res['pain_character'];
                $response['pain_duration_type'] = $res['pain_duration_type'];
                $response['pain_duration'] = $res['pain_duration'];
                $response['factor_decreasing_pain'] = $res['factor_decreasing_pain'];
                $response['factor_increasing_pain'] = $res['factor_increasing_pain'];
                $response['action_plan'] = $res['action_plan'];
                $response['intervention'] = $res['intervention'];
                $response['intervention_therapy'] = $res['intervention_therapy'];
                $response['heparin_therapy'] = $res['heparin_therapy'];
                $response['quest_10'] = $res['quest_10'];
                $response['quest_10_response'] = $res['quest_10_response'];
                $response['quest_11'] = $res['quest_11'];
                $response['quest_11_response'] = $res['quest_11_response'];
                $response['quest_12'] = $res['quest_12'];
                $response['quest_12_response'] = $res['quest_12_response'];
                $response['quest_13'] = $res['quest_13'];
                $response['quest_13_response'] = $res['quest_13_response'];
                $response['quest_14'] = $res['quest_14'];
                $response['quest_14_response'] = $res['quest_14_response'];
                $response['quest_15'] = $res['quest_15'];
                $response['quest_15_response'] = $res['quest_15_response'];
                $response['behaviour_pain_1'] = $res['behaviour_pain_1'];
                $response['behaviour_pain_2'] = $res['behaviour_pain_2'];
                $response['behaviour_pain_3'] = $res['behaviour_pain_3'];
                $response['behaviour_pain_score'] = $res['behaviour_pain_score'];
                $response['completed_status'] = $res['completed_status'];
                $response['complete_datetime'] = $res['complete_datetime'];
                $response['created_on'] = $res['created_on'];
                $response['created_by'] = $res['created_by'];
                $response['diff_time'] = $res['diff_time'];
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Sorry! No Data Found";
            }
        } else {
			http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Access denied!";
        }
    } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry! some details are missing ";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed";
}
echo json_encode($response);
$pdoread = null;
?>
