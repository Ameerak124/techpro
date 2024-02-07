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
$accesskey = $data->accesskey;
$response = array();
$admission_num = trim($data->admission_num);
try {
	if (!empty($accesskey) && !empty($admission_num)) {
		$check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check->execute();
		$result = $check->fetch(PDO::FETCH_ASSOC);
		if ($check->rowCount() > 0) {
			$query = $pdoread->prepare("SELECT `nursing_pediatric_re_assmnt_table`.`admission_num`, `nursing_pediatric_re_assmnt_table`.`umr_no`, `nursingid`, `shift_type`, `totals`, DATE(`captured_date_and_time`) as captured_date, TIME(`captured_date_and_time`) as captured_time,`bp_position_sleep`, `bp_type_sleep`, `bp_systolic_sleep`, `bp_diastolic_sleep`, `bp_position_sit`, `bp_type_sit`, `bp_systolic_sit`, `bp_diastolic_sit`, `bp_position_stand`, `bp_type_stand`, `bp_systolic_stand`, `bp_diastolic_stand`, `bp_units`, `pulse_type`, `pulse_rate`, `pulse_units`, `heart_rate_type`, `heart_rate`, `heart_rate_units`, `respiratory_rate`, `respiratory_units`, `weight`, `weight_units`, `height_rate`, `height_units`, `bmi_rate`, `bmi_units`, `bsa_rate`, `bsa_units`, `temp_type`, `temp_rate`, `temp_units`, `spo2_type`, `spo2_type1`, `spo2_rate`, `pain_score`, `grbs`, `grbs_units`, `blood_group`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`,`behaviour_pain_total`, `history_fall_response`, `diagnosis_response`, `ambulatory_aid_response`, `acess_response`, `gait_response`, `mental_status_response`, `nursing_assessment`, `goal`, `planning`, `implementation`, `evaluation`, `allergies_medication`, `allergies_medication_data`, `food`, `food_data`, `other_allergies`, `cost_center`, `nursing_pediatric_re_assmnt_table`.`created_on`, `nursing_pediatric_re_assmnt_table`.`created_by`, DATE_FORMAT(`nursing_pediatric_re_assmnt_table`.`modified_on`, '%d-%b-%y %h:%i:%s') as modified_on, `nursing_pediatric_re_assmnt_table`.`modified_by`, `nursing_pediatric_re_assmnt_table`.`estatus`
`age`,`gender`,`diagnosis`,`cognitive_impairments`,`environmental_factors`,`response_to_surgery`,`medication_usage`,`total_score`
 FROM `nursing_pediatric_re_assmnt_table` LEFT JOIN `vital_signs_table` ON `vital_signs_table`.`page_id` = `nursing_pediatric_re_assmnt_table`.`vitals_signs` AND `vital_signs_table`.`admission_num`= :ip AND `vital_signs_table`.`doctor_id`= `nursing_pediatric_re_assmnt_table`.`nursingid` left join `pain_scores_table` ON `pain_scores_table`.`page_id`=`nursing_pediatric_re_assmnt_table`.`pain_scores` AND `pain_scores_table`.`admission_num` = :ip AND `pain_scores_table`.`doctor_uid` = `nursing_pediatric_re_assmnt_table`.`nursingid` WHERE `nursing_pediatric_re_assmnt_table`.`admission_num` = :ip and `nursing_pediatric_re_assmnt_table`.`estatus` = 'Active'");
			$query->bindParam(':ip', $admission_num, PDO::PARAM_STR);
			$query->execute();
			if ($query->rowCount() > 0) {
				http_response_code(200);
				$response['error'] = false;
				$response['message'] = "Data Found";
				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$response['admission_num'] = $row['admission_num'];
					$response['umr_no'] = $row['umr_no'];
					$response['nursingid'] = $row['nursingid'];
					$response['shift_type'] = $row['shift_type'];
					$response['totals'] = $row['totals'];
					$response['captured_date'] = $row['captured_date'];
					$response['captured_time'] = $row['captured_time'];
					$response['bp_check_position_1'] = $row['bp_position_sleep'];
					$response['bp_dd_1'] = $row['bp_type_sleep'];
					$response['bp_systolic_1'] = $row['bp_systolic_sleep'];
					$response['bp_diastolic_1'] = $row['bp_diastolic_sleep'];
					$response['bp_check_position_2'] = $row['bp_position_sit'];
					$response['bp_dd_2'] = $row['bp_type_sit'];
					$response['bp_systolic_2'] = $row['bp_systolic_sit'];
					$response['bp_diastolic_2'] = $row['bp_diastolic_sit'];
					$response['bp_check_position_3'] = $row['bp_position_stand'];
					$response['bp_dd_3'] = $row['bp_type_stand'];
					$response['bp_systolic_3'] = $row['bp_systolic_stand'];
					$response['bp_diastolic_3'] = $row['bp_diastolic_stand'];
					$response['bp_units'] = $row['bp_units'];
					$response['pulse_dd'] = $row['pulse_type'];
					$response['pulse_rate'] = $row['pulse_rate'];
					$response['pulse_units'] = $row['pulse_units'];
					$response['heart_rate_dd'] = $row['heart_rate_type'];
					$response['heart_rate'] = $row['heart_rate'];
					$response['heart_rate_units'] = $row['heart_rate_units'];
					$response['respiratory_rate'] = $row['respiratory_rate'];
					$response['respiratory_units'] = $row['respiratory_units'];
					$response['weight'] = $row['weight'];
					$response['weight_units'] = $row['weight_units'];
					$response['height_rate'] = $row['height_rate'];
					$response['height_units'] = $row['height_units'];
					$response['bmi_rate'] = $row['bmi_rate'];
					$response['bmi_units'] = $row['bmi_units'];
					$response['bsa_rate'] = $row['bsa_rate'];
					$response['bsa_units'] = $row['bsa_units'];
					$response['temp_dd'] = $row['temp_type'];
					$response['temp_rate'] = $row['temp_rate'];
					$response['temp_units'] = $row['temp_units'];
					$response['spo2_dd1'] = $row['spo2_type'];
					$response['spo2_dd2'] = $row['spo2_type1'];
					$response['spo2_rate'] = $row['spo2_rate'];
					$response['pain_score'] = $row['pain_score'];
					$response['grbs'] = $row['grbs'];
					$response['grbs_units'] = $row['grbs_units'];
					$response['blood_group'] = $row['blood_group'];
					$response['pain_scale_type'] = $row['pain_type'];
					$response['painscale_score'] = $row['pain_rate'];
					$response['doctor_name'] = $row['doc_name'];
					$response['pain'] = $row['pain'];
					$response['pain_location'] = $row['pain_loc'];
					$response['pain_character'] = $row['pain_char'];
					$response['pain_acute_chronic'] = $row['acute_chornic'];
					$response['pain_duration'] = $row['pain_duration'];
					$response['factor_decreasing_pain'] = $row['dec_pain'];
					$response['factor_increasing_pain'] = $row['inc_pain'];
					$response['action_plan'] = $row['action_plan'];
					$response['intervention'] = $row['intervention'];
					$response['intervention_therapy'] = $row['interventiond'];
					$response['heparin_therapy'] = $row['heparin_therapy'];
					$response['facial_expression'] = $row['facial_expression'];
					$response['upper_limb_movements'] = $row['upper_limb_movements'];
					$response['compliance_mechanical_ventilation'] = $row['compliance_mechanical_ventilation'];
					$response['behaviour_pain_total'] = $row['behaviour_pain_total'];
					$response['history_fall_response'] = $row['history_fall_response'];
					$response['diagnosis_response'] = $row['diagnosis_response'];
					$response['ambulatory_aid_response'] = $row['ambulatory_aid_response'];
					$response['acess_response'] = $row['acess_response'];
					$response['gait_response'] = $row['gait_response'];
					$response['mental_status_response'] = $row['mental_status_response'];
					$response['nursing_assessment'] = $row['nursing_assessment'];
					$response['goal'] = $row['goal'];
					$response['planning'] = $row['planning'];
					$response['implementation'] = $row['implementation'];
					$response['evaluation'] = $row['evaluation'];
					$response['allergies_medication'] = $row['allergies_medication'];
					$response['allergies_medication_data'] = $row['allergies_medication_data'];
					$response['food'] = $row['food'];
					$response['food_data'] = $row['food_data'];
					$response['other_allergies'] = $row['other_allergies'];
					$response['age'] = $row['age'];
					$response['gender'] = $row['gender'];
					$response['diagnosis'] = $row['diagnosis'];
					$response['cognitive_impairments'] = $row['cognitive_impairments'];
					$response['environmental_factors'] = $row['environmental_factors'];
					$response['response_to_surgery'] = $row['response_to_surgery'];
					$response['medication_usage'] = $row['medication_usage'];
					$response['total_score'] = $row['total_score'];
				}
			} else {
				http_response_code(503);
				$response['error'] = true;
				$response['message'] = "No Data Found";
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