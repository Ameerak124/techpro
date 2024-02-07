<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$ipno = trim($data->ipno);
$umrno = trim($data->umrno);

$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
$mybrowser = get_browser(null, true);
try {
	
	$check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
	$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$check->execute();
	$result = $check->fetch(PDO::FETCH_ASSOC);
	if ($check->rowCount() > 0) {
		//get data
		$phase_01 = $pdoread->prepare(" SELECT `serialno`,`ip_no`, `umr_no`, `category`, `is_altered_sensorium`, `altered_sensorium`, `is_facialpalsyasymmetry`, `facialpalsyasymmetry`, `is_impairedgait`, `impairedgait`, `isnumbne`, `numbnes`, `is_paralysi`, `paralysi`, `istingling`, `tingling`, `isvertigo`, `vertigo`, `weakness`, `paresis`, `rightpupilsize`, `leftpupilsize`, `scleracolorwhite`, `pupillaryreactionright`, `pupillaryreactionleft`, `others`, `pressure_ulcer_category`, `antomy_location_of_wound`, `age_of_wound`, `size_of_wound`, `depth_of_ulcer`, `stage_of_ulcer`, `exu_dates`, `pressure_ulcer_remarks`, `predicting_a1`, `predicting_a2`, `predicting_a3`, `predicting_a4`, `predicting_a5`, `predicting_a6`, `urinary_a1`, `urinary_a1_res`, `urinary_a2`, `urinary_a2_res`, `urinary_a3`, `urinary_a3_res`, `urinary_a4`, `urinary_a4_res`, `urinary_a5`, `urinary_a5_res`, `urinary_a6`, `urinary_a6_res`, `urinary_a7`, `urinary_a7_res`, `urinary_a8`, `urinary_a8_res`, `predicting_total`, `createdon` FROM `systemic_assesment` WHERE `estatus`='Active' AND `umr_no`=:umrno AND `ip_no`=:ipno ORDER BY `createdon` DESC");
		$phase_01->bindParam(':ipno', $ipno, PDO::PARAM_STR);
		$phase_01->bindParam(':umrno', $umrno, PDO::PARAM_STR);
		$phase_01->execute();
		if ($phase_01->rowCount() > 0) {
			$response['error'] = false;
			$response['message'] = "Data Found";
			$s = 0;
			while ($res1 = $phase_01->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s]['createdon'] = $res1['createdon'];
				$response['historylist'][$s]['sysassessment'] = $res1;
				$s++;
			}

			//pain scores
			$phase_02 = $pdoread->prepare("SELECT `sno`, `page_id`, `assessment_type`, `admission_num`, `umr_no`, `doctor_uid`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy` FROM `pain_scores_table` WHERE `status`='Active' AND `umr_no`=:umrno AND `admission_num`=:ipno ORDER BY `createdon` DESC ");
			$phase_02->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$phase_02->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$phase_02->execute();
			$s1 = 0;
			while ($res2 = $phase_02->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s1]['pain_scores_table'] = $res2;
				$s1++;
			}

			//behaviour scores
			$phase = $pdoread->prepare("SELECT `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`, `behaviour_total` FROM `nursing_re_assessment_table` WHERE `estatus`='Active' AND `umr_no`=:umrno AND `admission_num`=:ipno ORDER BY `created_on` DESC");
			$phase->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$phase->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$phase->execute();
			$s6 = 0;
			while ($res = $phase->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s6]['behaviour_pain'] = $res;
				$s6++;
			}

			//vital signs 
			$phase_03 = $pdoread->prepare("SELECT `sno`, `page_id`, `assessment_type`, `admission_num`, `umr_no`, `doctor_id`,DATE_FORMAT( `captured_date_and_time`,'%d-%b-%Y')AS captured_date,DATE_FORMAT(`captured_date_and_time`,'%h:%i:%p')AS captured_time, `bp_position_sleep`, `bp_type_sleep`, `bp_systolic_sleep`, `bp_diastolic_sleep`, `bp_position_sit`, `bp_type_sit`, `bp_systolic_sit`, `bp_diastolic_sit`, `bp_position_stand`, `bp_type_stand`, `bp_systolic_stand`, `bp_diastolic_stand`, `bp_units`, `pulse_type`, `pulse_rate`, `pulse_units`, `heart_rate_type`, `heart_rate`, `heart_rate_units`, `respiratory_rate`, `respiratory_units`, `weight`, `weight_units`, `height_rate`, `height_units`, `bmi_rate`, `bmi_units`, `bsa_rate`, `bsa_units`, `temp_type`, `temp_rate`, `temp_units`, `spo2_type`, `spo2_type1`, `spo2_rate`, `pain_score`, `grbs`, `grbs_units`, `blood_group` FROM `vital_signs_table` WHERE `estatus`='Active' AND `umr_no`=:umrno AND `admission_num`=:ipno ORDER BY `created_on` DESC  ");
			$phase_03->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$phase_03->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$phase_03->execute();
			$s2 = 0;
			while ($res3 = $phase_03->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s2]['vital_signs_table'] = $res3;
				$s2++;
			}
			//nursing re assessment
			$phase_04 = $pdoread->prepare("SELECT `sno`, `admission_num`, `umr_no`, `nursing_id`, `assessment_type`, `vital_sign_id`, `fra_history`, `fra_history_response`, `fra_diagnosis`, `fra_diagnosis_response`, `fra_ambulatory`, `fra_ambulatory_response`, `fra_acess`, `fra_access_response`, `fra_gait`, `fra_gait_response`, `fra_mental`, `fra_mental_response`, `fra_total`,`nursing_assessment`, `goal`, `planning`, `implementation`, `evaluation`, `pain_id`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`, `allergies_medication`, `allergies_medication_data`, `food`, `food_data`, `other_allergies`, `rep_bradypnea`, `rep_bradypnea_response`, `rep_dyspnea`, `rep_dyspnea_response`, `rep_laboured`, `rep_laboured_response`, `rep_normal`, `rep_normal_response`, `rep_tachypnea`, `rep_tachypnea_response`, `rep_tracheostomya`, `rep_tracheostomy_response`, `rep_ventilator`, `rep_ventilator_response`, `cough_absent`, `cough_absent_response`, `cough_present`, `cough_present_response`, `cough_non_productive`, `cough_non_productive_response`, `cough_productive`, `cough_productive_response`, `cough_others`, `gi_bleeding`, `gi_bleeding_response`, `gi_constipation`, `gi_constipation_response`, `gi_diarrhoea`, `gi_diarrhoea_response`, `gi_hematemesis`, `gi_hematemesis_response`, `gi_malena`, `gi_malena_response`, `gi_nausea_vomiting`, `gi_nausea_vomiting_response`, `gi_others`, `ms_fracture`, `ms_fracture_response`, `ms_joint_stiffness`, `ms_joint_stiffness_response`, `ms_musleatropy`, `ms_musleatropy_response`, `ms_paralysis`, `ms_paralysis_response`, `ms_swelling`, `ms_swelling_response`, `ms_traction`, `ms_traction_response`, `ms_vitals_signs`, `ms_vitals_signs_response`, `ms_wound`, `ms_wound_response`, `ms_Others`, `motor_response_score`, `verbal_response_score`, `eye_opening_score`, `glasgowtotal` FROM `nursing_re_assessment_table` WHERE `estatus`='Active' AND `admission_num`=:ipno AND `umr_no`=:umrno ORDER BY `modified_on` DESC ");
			$phase_04->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$phase_04->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$phase_04->execute();
			$s3 = 0;
			while ($res4 = $phase_04->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s3]['nursing_re_assessment_table'] = $res4;
				$s3++;
			}
			//care plan 01
			$phase_05 = $pdoread->prepare("SELECT `sno`, `umr_no`, `ip_no`, `a1`, `a2`, `a3`, `a4`, `b1`, `b2`, `b3`, `b4`, `c1`, `c2`, `c3`, `c4`, `d1`, `d2`, `d3`, `d4`, `e1`, `e2`, `e3`, `e4`, `f1`, `f2`, `f3`, `f4`, `g1`, `g2`, `g3`, `g4`, `h1`, `h2`, `h3`, `h4`, `i1`, `i2`, `i3`, `i4`, `j1`, `j2`, `j3`, `j4`, `k1`, `k2`, `k3`, `k4` FROM `care_plan_01` WHERE `esatus`='Active' AND `umr_no`=:umrno AND `ip_no`=:ipno ORDER BY `created_on` DESC LIMIT 1");
			$phase_05->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$phase_05->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$phase_05->execute();
			$s4 = 0;
			while ($res5 = $phase_05->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s4]['care_plan_01'] = $res5;
				$s4++;
			}

			//care plan 02
			$phase_06 = $pdoread->prepare(" SELECT `sno`, `umr_no`, `ip_num`, `l1`, `l2`, `l3`, `l4`, `m1`, `m2`, `m3`, `m4`, `n1`, `n2`, `n3`, `n4`, `o1`, `o2`, `o3`, `o4`, `p1`, `p2`, `p3`, `p4`, `q1`, `q2`, `q3`, `q4`, `r1`, `r2`, `r3`, `r4`, `s1`, `s2`, `s3`, `s4`, `t1`, `t2`, `t3`, `t4`, `u1`, `u2`, `u3`, `u4`, `v1`, `v2`, `v3`, `v4` FROM `care_plan_02` WHERE `estatus`='Active' AND `umr_no`=:umrno AND `ip_num`=:ipno ORDER BY `created_on` DESC LIMIT 1 ");
			$phase_06->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$phase_06->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$phase_06->execute();
			$s5 = 0;
			while ($res6 = $phase_06->fetch(PDO::FETCH_ASSOC)) {
				$response['historylist'][$s5]['care_plan_02'] = $res6;
				$s5++;
			}
			//end
		} else {
			$response['error'] = true;
			$response['message'] = "Data Not Found";
		}
	} else {
		$response['error'] = true;
		$response['message'] = "Access denied!";
	}
	// } else {
	// 	$response['error'] = true;
	// 	$response['message'] = "Sorry! some details are missing";
	// }
} catch (PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message'] = $e->getMessage();
	$errorlog = $pdoread->prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
	$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
	$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
	$errorlog->execute();
}

echo json_encode($response);
$pdoread = null;
?>