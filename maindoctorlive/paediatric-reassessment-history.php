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
// $contact = "Tel. No: 040 6833 4455 (24/7)";
// $ipaddress = $_SERVER['REMOTE_ADDR'];
// $apiurl = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
// $mybrowser = get_browser(null, true);
try {
    if (!empty($accesskey) && !empty($admission_num)) {
        $check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {
            $query = $pdoread->prepare("SELECT `nursing_pediatric_re_assmnt_table`.`admission_num`, `nursing_pediatric_re_assmnt_table`.`umr_no`, `nursingid`, `shift_type`, `totals`, DATE(`captured_date_and_time`) as captured_date, TIME(`captured_date_and_time`) as captured_time,`bp_position_sleep`, `bp_type_sleep`, `bp_systolic_sleep`, `bp_diastolic_sleep`, `bp_position_sit`, `bp_type_sit`, `bp_systolic_sit`, `bp_diastolic_sit`, `bp_position_stand`, `bp_type_stand`, `bp_systolic_stand`, `bp_diastolic_stand`, `bp_units`, `pulse_type`, `pulse_rate`, `pulse_units`, `heart_rate_type`, `heart_rate`, `heart_rate_units`, `respiratory_rate`, `respiratory_units`, `weight`, `weight_units`, `height_rate`, `height_units`, `bmi_rate`, `bmi_units`, `bsa_rate`, `bsa_units`, `temp_type`, `temp_rate`, `temp_units`, `spo2_type`, `spo2_type1`, `spo2_rate`, `pain_score`, `grbs`, `grbs_units`, `blood_group`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`, `behaviour_pain_total`,`history_fall_response`, `diagnosis_response`, REGEXP_REPLACE(REGEXP_REPLACE(`ambulatory_aid_response`, '[\n]', ' '), ' +', ' ') AS ambulatory_aid_response, `acess_response`, REGEXP_REPLACE(REGEXP_REPLACE(`gait_response`, '[\n]', ' '), ' +', ' ') AS gait_response, `mental_status_response`, `nursing_assessment`, `goal`, `planning`, `implementation`, `evaluation`, `allergies_medication`, `allergies_medication_data`, `food`, `food_data`, `other_allergies`, `cost_center`, DATE_FORMAT(`nursing_pediatric_re_assmnt_table`.`created_on`, '%d-%b-%Y %H:%i') as  created_on, `nursing_pediatric_re_assmnt_table`.`created_by`, DATE_FORMAT(`nursing_pediatric_re_assmnt_table`.`modified_on`, '%d-%b-%Y %H:%i') as modified_on, `nursing_pediatric_re_assmnt_table`.`modified_by`, `nursing_pediatric_re_assmnt_table`.`estatus` ,`age`,`gender`,`diagnosis`,`cognitive_impairments`,`environmental_factors`,`response_to_surgery`,`medication_usage`,`total_score`				
				FROM `nursing_pediatric_re_assmnt_table` LEFT JOIN `vital_signs_table` ON `vital_signs_table`.`page_id` = `nursing_pediatric_re_assmnt_table`.`vitals_signs` AND `vital_signs_table`.`admission_num`= :ip AND `vital_signs_table`.`doctor_id`= `nursing_pediatric_re_assmnt_table`.`nursingid` left join `pain_scores_table` ON `pain_scores_table`.`page_id`=`nursing_pediatric_re_assmnt_table`.`pain_scores` AND `pain_scores_table`.`admission_num` = :ip AND `pain_scores_table`.`doctor_uid` = `nursing_pediatric_re_assmnt_table`.`nursingid` WHERE `nursing_pediatric_re_assmnt_table`.`admission_num` = :ip and `nursing_pediatric_re_assmnt_table`.`estatus` = 'Active' ORDER BY `nursing_pediatric_re_assmnt_table`.`created_on` DESC");
            $query->bindParam(':ip', $admission_num, PDO::PARAM_STR);
            $query->execute();
            if ($query->rowCount() > 0) {
                $response['error'] = false;
                $response['message'] = "Data Found";
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $response['list'][] = $row;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "No Data Found";
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Access denied!";
        }
    } else {
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