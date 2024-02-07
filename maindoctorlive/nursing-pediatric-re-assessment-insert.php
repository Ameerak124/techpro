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
$umr_no = trim($data->umr_no);
$shift_type = trim($data->shift_type);
$captured_date_and_time = ($data->captured_date_and_time);
$captured_date_and_time = str_replace("/", " ", $captured_date_and_time);
$bp_check_position_1 = trim($data->bp_check_position_1);
$bp_dd_1 = trim($data->bp_dd_1);
$bp_systolic_1 = trim($data->bp_systolic_1);
$bp_diastolic_1 = trim($data->bp_diastolic_1);
$bp_check_position_2 = trim($data->bp_check_position_2);
$bp_dd_2 = trim($data->bp_dd_2);
$bp_systolic_2 = trim($data->bp_systolic_2);
$bp_diastolic_2 = trim($data->bp_diastolic_2);
$bp_check_position_3 = trim($data->bp_check_position_3);
$bp_dd_3 = trim($data->bp_dd_3);
$bp_systolic_3 = trim($data->bp_systolic_3);
$bp_diastolic_3 = trim($data->bp_diastolic_3);
$bp_units = trim($data->bp_units);
$pulse_dd = trim($data->pulse_dd);
$pulse_rate = trim($data->pulse_rate);
$pulse_units = trim($data->pulse_units);
$heart_rate_dd = trim($data->heart_rate_dd);
$heart_rate = trim($data->heart_rate);
$heart_rate_units = trim($data->heart_rate_units);
$respiratory_rate = trim($data->respiratory_rate);
$respiratory_units = trim($data->respiratory_units);
$weight = trim($data->weight);
$weight_units = trim($data->weight_units);
$height_rate = trim($data->height_rate);
$height_units = trim($data->height_units);
$bmi_rate = trim($data->bmi_rate);
$bmi_units = trim($data->bmi_units);
$bsa_rate = trim($data->bsa_rate);
$bsa_units = trim($data->bsa_units);
$temp_dd = trim($data->temp_dd);
$temp_rate = trim($data->temp_rate);
$temp_units = trim($data->temp_units);
$spo2_dd1 = trim($data->spo2_dd1);
$spo2_dd2 = trim($data->spo2_dd2);
$spo2_rate = trim($data->spo2_rate);
$pain_score = trim($data->pain_score);
$grbs = trim($data->grbs);
$grbs_units = trim($data->grbs_units);
$blood_group = trim($data->blood_group);
$pain_type = trim($data->pain_scale_type);
$pain_rate = trim($data->painscale_score);
$doc_name = trim($data->doctor_name);
$pain = trim($data->pain);
$pain_loc = trim($data->pain_location);
$pain_char = trim($data->pain_character);
$acute_chornic = trim($data->pain_acute_chronic);
$pain_duration = trim($data->pain_duration);
$dec_pain = trim($data->factor_decreasing_pain);
$inc_pain = trim($data->factor_increasing_pain);
$action_plan = trim($data->action_plan);
$intervention = trim($data->intervention);
$interventiond = trim($data->intervention_therapy);
$heparin_therapy = trim($data->heparin_therapy);
$facial_expression = trim($data->facial_expression);
$upper_limb_movements = trim($data->upper_limb_movements);
$compliance_mechanical_ventilation = trim($data->compliance_mechanical_ventilation);
$behaviour_pain_total = trim($data->behaviour_pain_total);
$history_fall_response = trim($data->history_fall_response);
$diagnosis_response = trim($data->diagnosis_response);
$ambulatory_aid_response = trim($data->ambulatory_aid_response);
$acess_response = trim($data->acess_response);
$gait_response = trim($data->gait_response);
$mental_status_response = trim($data->mental_status_response);
$nursing_assessment = trim($data->nursing_assessment);
$goal = trim($data->goal);
$planning = trim($data->planning);
$implementation = trim($data->implementation);
$evaluation = trim($data->evaluation);
$allergies_medication = trim($data->allergies_medication);
$allergies_medication_data = trim($data->allergies_medication_data);
$food = trim($data->food);
$food_data = trim($data->food_data);
$other_allergies = trim($data->other_allergies);
$cost_center = trim($data->cost_center);
$totals = trim($data->totals);
$age = trim($data->age);
$gender = trim($data->gender);
$diagnosis = trim($data->diagnosis);
$cognitive_impairments = trim($data->cognitive_impairments);
$environmental_factors = trim($data->environmental_factors);
$response_to_surgery = trim($data->response_to_surgery);
$medication_usage = trim($data->medication_usage);
$total_score = trim($data->total_score);
$admission_num = trim($data->admission_num);
try {
  //Check empty Parameters Start
  if (!empty($accesskey) && !empty($admission_num) && !empty($shift_type) && !empty($cost_center)) {
    //Check User Access Start
    $check = $pdoread->prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check->execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
    if ($check->rowCount() > 0) {
      //check if data exists already
      //check patient discharged or not on ip
      $check_ip = $pdoread->prepare("SELECT `admissionno` FROM `registration` WHERE `admissionstatus` NOT IN('Discharged') AND `status`='Visible' AND `admissionno`=:ipno ");
      $check_ip->bindParam(':ipno', $admission_num, PDO::PARAM_STR);
      $check_ip->execute();
      if ($check_ip->rowCount() > 0) {
        $admissionno_check = $pdoread->prepare("SELECT `admission_num` FROM `nursing_pediatric_re_assmnt_table` WHERE `admission_num`=:admission_num AND `cost_center`=:costcenter AND `estatus`='Active' ");
        $admissionno_check->bindParam(':admission_num', $admission_num, PDO::PARAM_STR);
        $admissionno_check->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
        $admissionno_check->execute();
        if ($admissionno_check->rowCount() > 0) {
          $response['error'] = true;
          $response['message'] = "Data Added Previously on " . $admission_num;
        } else {
          $nid = $pdoread->prepare("SELECT IFNULL(MAX(`nursingid`),CONCAT('NID',DATE_FORMAT(CURRENT_DATE,'%y%m'),'000000')) AS nursingid FROM `nursing_pediatric_re_assmnt_table` WHERE `nursingid` LIKE '%NID%' ");
          $nid->execute();
          if ($nid->rowCount() > 0) {
            $res = $nid->fetch(PDO::FETCH_ASSOC);
            $nursing = $res['nursingid'];
            $nursingid = ++$nursing;
          } else {
            $nursingid = $nursingno;
          }
          $vs_id = $pdoread->prepare("SELECT IFNULL(MAX(`page_id`),CONCAT('PR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS page_id  FROM `vital_signs_table` WHERE `page_id` LIKE '%PR%'");
          $vs_id->execute();
          if ($vs_id->rowCount() > 0) {
            $vs = $vs_id->fetch(PDO::FETCH_ASSOC);
            $vsid =  $vs['page_id'];
            $vsids = ++$vsid;
          } else {
            $vsids = $vsidno;
          }

          $pn_id = $pdoread->prepare("SELECT IFNULL(MAX(`page_id`),CONCAT('PR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS page_id  FROM `pain_scores_table` WHERE `page_id` LIKE '%PR%'");
          $pn_id->execute();
          if ($pn_id->rowCount() > 0) {
            $pn = $pn_id->fetch(PDO::FETCH_ASSOC);
            $pnid =  $pn['page_id'];
            $pnids = ++$pnid;
          } else {
            $pnids = $pnidno;
          }
          //nursing id created
          //insert into the table
          $insert = $pdo4->prepare("INSERT IGNORE INTO `nursing_pediatric_re_assmnt_table`(`sno`, `admission_num`, `umr_no`, `nursingid`, `shift_type`, `totals`, `vitals_signs`, `pain_scores`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation`, `behaviour_pain_total`,`history_fall_response`, `diagnosis_response`, `ambulatory_aid_response`,  `acess_response`, `gait_response`, `mental_status_response`, `nursing_assessment`, `goal`, `planning`, `implementation`, `evaluation`, `allergies_medication`, `allergies_medication_data`, `food`, `food_data`, `other_allergies`, `cost_center`, `created_on`, `created_by`, `modified_on`, `modified_by`, `estatus`, `age`, `gender`, `diagnosis`, `cognitive_impairments`, `environmental_factors`, `response_to_surgery`, `medication_usage`,`total_score`) VALUES(NULL, :admission_num, :umr_no, :nursingid, :shift_type, :totals, :vsids, :pnids, :facial_expression, :upper_limb_movements, :compliance_mechanical_ventilation, :behaviour_pain_total,:history_fall_response, :diagnosis_response, :ambulatory_aid_response, :acess_response, :gait_response, :mental_status_response, :nursing_assessment, :goal, :planning, :implementation, :evaluation, :allergies_medication, :allergies_medication_data, :food, :food_data, :other_allergies, :cost_center, CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP, :userid, 'Active' , :age, :gender, :diagnosis, :cognitive_impairments, :environmental_factors, :response_to_surgery, :medication_usage,:total_score)");
          $insert->bindParam(':admission_num', $admission_num, PDO::PARAM_STR);
          $insert->bindParam(':vsids', $vsids, PDO::PARAM_STR);
          $insert->bindParam(':pnids', $pnids, PDO::PARAM_STR);
          $insert->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
          $insert->bindParam(':nursingid', $nursingid, PDO::PARAM_STR);
          $insert->bindParam(':shift_type', $shift_type, PDO::PARAM_STR);
          $insert->bindParam(':totals', $totals, PDO::PARAM_STR);
          $insert->bindParam(':facial_expression', $facial_expression, PDO::PARAM_STR);
          $insert->bindParam(':upper_limb_movements', $upper_limb_movements, PDO::PARAM_STR);
          $insert->bindParam(':compliance_mechanical_ventilation', $compliance_mechanical_ventilation, PDO::PARAM_STR);
          $insert->bindParam(':behaviour_pain_total', $behaviour_pain_total, PDO::PARAM_STR);
          $insert->bindParam(':history_fall_response', $history_fall_response, PDO::PARAM_STR);
          $insert->bindParam(':diagnosis_response', $diagnosis_response, PDO::PARAM_STR);
          $insert->bindParam(':ambulatory_aid_response', $ambulatory_aid_response, PDO::PARAM_STR);
          $insert->bindParam(':acess_response', $acess_response, PDO::PARAM_STR);
          $insert->bindParam(':gait_response', $gait_response, PDO::PARAM_STR);
          $insert->bindParam(':mental_status_response', $mental_status_response, PDO::PARAM_STR);
          $insert->bindParam(':nursing_assessment', $nursing_assessment, PDO::PARAM_STR);
          $insert->bindParam(':goal', $goal, PDO::PARAM_STR);
          $insert->bindParam(':planning', $planning, PDO::PARAM_STR);
          $insert->bindParam(':implementation', $implementation, PDO::PARAM_STR);
          $insert->bindParam(':evaluation', $evaluation, PDO::PARAM_STR);
          $insert->bindParam(':allergies_medication', $allergies_medication, PDO::PARAM_STR);
          $insert->bindParam(':allergies_medication_data', $allergies_medication_data, PDO::PARAM_STR);
          $insert->bindParam(':food', $food, PDO::PARAM_STR);
          $insert->bindParam(':food_data', $food_data, PDO::PARAM_STR);
          $insert->bindParam(':other_allergies', $other_allergies, PDO::PARAM_STR);
          $insert->bindParam(':cost_center', $cost_center, PDO::PARAM_STR);
          $insert->bindParam(':age', $age, PDO::PARAM_STR);
          $insert->bindParam(':gender', $gender, PDO::PARAM_STR);
          $insert->bindParam(':diagnosis', $diagnosis, PDO::PARAM_STR);
          $insert->bindParam(':cognitive_impairments', $cognitive_impairments, PDO::PARAM_STR);
          $insert->bindParam(':environmental_factors', $environmental_factors, PDO::PARAM_STR);
          $insert->bindParam(':response_to_surgery', $response_to_surgery, PDO::PARAM_STR);
          $insert->bindParam(':medication_usage', $medication_usage, PDO::PARAM_STR);
          $insert->bindParam(':total_score', $total_score, PDO::PARAM_STR);
          $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
          $insert->execute();
          if ($insert->rowCount() > 0) {
            $response['error'] = false;
            $response['message'] = "Data Inserted Successfully";
            $vsinsert = $pdo4->prepare("INSERT INTO `vital_signs_table`(`sno`, `page_id`, `assessment_type`, `admission_num`, `doctor_id`, `captured_date_and_time`,  `bp_position_sleep`, `bp_type_sleep`, `bp_systolic_sleep`, `bp_diastolic_sleep`, `bp_position_sit`, `bp_type_sit`, `bp_systolic_sit`, `bp_diastolic_sit`, `bp_position_stand`, `bp_type_stand`, `bp_systolic_stand`, `bp_diastolic_stand`, `bp_units`, `pulse_type`, `pulse_rate`, `pulse_units`, `heart_rate_type`, `heart_rate`, `heart_rate_units`, `respiratory_rate`, `respiratory_units`, `weight`, `weight_units`, `height_rate`, `height_units`, `bmi_rate`, `bmi_units`, `bsa_rate`, `bsa_units`, `temp_type`, `temp_rate`, `temp_units`, `spo2_type`, `spo2_type1`, `spo2_rate`, `pain_score`, `grbs`, `grbs_units`, `blood_group`, `created_on`, `created_by`, `modified_on`, `modified_by`, `estatus`, `umr_no`) VALUES (NULL, :vsids, 'nurs-pead-reass', :admission_num, :nursingid, :captured_date_and_time, :bp_check_position_1, :bp_dd_1, :bp_systolic_1, :bp_diastolic_1, :bp_check_position_2, :bp_dd_2, :bp_systolic_2, :bp_diastolic_2, :bp_check_position_3, :bp_dd_3, :bp_systolic_3, :bp_diastolic_3, :bp_units, :pulse_dd, :pulse_rate, :pulse_units, :heart_rate_dd, :heart_rate, :heart_rate_units, :respiratory_rate, :respiratory_units, :weigh, :weight_units, :height_rate, :height_units, :bmi_rate, :bmi_units, :bsa_rate, :bsa_units, :temp_dd, :temp_rate, :temp_units, :spo2_dd1, :spo2_dd2, :spo2_rate, :pain_score, :grbs, :grbs_units, :blood_group,  CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP, :userid, 'Active', :umr_no) ");
            $vsinsert->bindParam(':admission_num', $admission_num, PDO::PARAM_STR);
            $vsinsert->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
            $vsinsert->bindParam(':vsids', $vsids, PDO::PARAM_STR);
            $vsinsert->bindParam(':nursingid', $nursingid, PDO::PARAM_STR);
            $vsinsert->bindParam(':captured_date_and_time', $captured_date_and_time, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_check_position_1', $bp_check_position_1, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_dd_1', $bp_dd_1, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_systolic_1', $bp_systolic_1, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_diastolic_1', $bp_diastolic_1, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_check_position_2', $bp_check_position_2, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_dd_2', $bp_dd_2, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_systolic_2', $bp_systolic_2, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_diastolic_2', $bp_diastolic_2, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_check_position_3', $bp_check_position_3, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_dd_3', $bp_dd_3, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_systolic_3', $bp_systolic_3, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_diastolic_3', $bp_diastolic_3, PDO::PARAM_STR);
            $vsinsert->bindParam(':bp_units', $bp_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':pulse_dd', $pulse_dd, PDO::PARAM_STR);
            $vsinsert->bindParam(':pulse_rate', $pulse_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':pulse_units', $pulse_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':heart_rate_dd', $heart_rate_dd, PDO::PARAM_STR);
            $vsinsert->bindParam(':heart_rate', $heart_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':heart_rate_units', $heart_rate_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':respiratory_rate', $respiratory_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':respiratory_units', $respiratory_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':weigh', $weight, PDO::PARAM_STR);
            $vsinsert->bindParam(':weight_units', $weight_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':height_rate', $height_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':height_units', $height_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':bmi_rate', $bmi_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':bmi_units', $bmi_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':bsa_rate', $bsa_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':bsa_units', $bsa_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':temp_dd', $temp_dd, PDO::PARAM_STR);
            $vsinsert->bindParam(':temp_rate', $temp_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':temp_units', $temp_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':spo2_dd1', $spo2_dd1, PDO::PARAM_STR);
            $vsinsert->bindParam(':spo2_dd2', $spo2_dd2, PDO::PARAM_STR);
            $vsinsert->bindParam(':spo2_rate', $spo2_rate, PDO::PARAM_STR);
            $vsinsert->bindParam(':pain_score', $pain_score, PDO::PARAM_STR);
            $vsinsert->bindParam(':grbs', $grbs, PDO::PARAM_STR);
            $vsinsert->bindParam(':grbs_units', $grbs_units, PDO::PARAM_STR);
            $vsinsert->bindParam(':blood_group', $blood_group, PDO::PARAM_STR);
            $vsinsert->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
            $vsinsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $vsinsert->execute();
            if ($vsinsert->rowCount() > 0) {
              $response['error'] = false;
              $response['message'] = "Data inserted";
              //add to pain score
              $psinsert = $pdo4->prepare("INSERT IGNORE INTO `pain_scores_table`(`sno`, `page_id`, `assessment_type`, `admission_num`, `umr_no`, `doctor_uid`, `pain_type`, `pain_rate`, `doc_name`, `pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`) VALUES (NULL,:pnids,'nurs-pead-reass',:admission_num, :umr_no, :nursingid,:pain_type, :pain_rate, :doc_name, :pain, :pain_loc, :pain_char, :acute_chornic, :pain_duration, :dec_pain, :inc_pain, :action_plan, :intervention, :interventiond, :heparin_therapy,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active')");
              $psinsert->bindParam(':admission_num', $admission_num, PDO::PARAM_STR);
              $psinsert->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
              $psinsert->bindParam(':pnids', $pnids, PDO::PARAM_STR);
              $psinsert->bindParam(':nursingid', $nursingid, PDO::PARAM_STR);
              $psinsert->bindParam(':pain_type', $pain_type, PDO::PARAM_STR);
              $psinsert->bindParam(':pain_rate', $pain_rate, PDO::PARAM_STR);
              $psinsert->bindParam(':doc_name', $doc_name, PDO::PARAM_STR);
              $psinsert->bindParam(':pain', $pain, PDO::PARAM_STR);
              $psinsert->bindParam(':pain_loc', $pain_loc, PDO::PARAM_STR);
              $psinsert->bindParam(':pain_char', $pain_char, PDO::PARAM_STR);
              $psinsert->bindParam(':acute_chornic', $acute_chornic, PDO::PARAM_STR);
              $psinsert->bindParam(':pain_duration', $pain_duration, PDO::PARAM_STR);
              $psinsert->bindParam(':dec_pain', $dec_pain, PDO::PARAM_STR);
              $psinsert->bindParam(':inc_pain', $inc_pain, PDO::PARAM_STR);
              $psinsert->bindParam(':action_plan', $action_plan, PDO::PARAM_STR);
              $psinsert->bindParam(':intervention', $intervention, PDO::PARAM_STR);
              $psinsert->bindParam(':interventiond', $interventiond, PDO::PARAM_STR);
              $psinsert->bindParam(':heparin_therapy', $heparin_therapy, PDO::PARAM_STR);
              $psinsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
              $psinsert->execute();
              if ($psinsert->rowCount() > 0) {
                http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data inserted";
              } else {
                http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Sorry! Data not inserted in PS";
              }
            } else {
              http_response_code(503);
              $response['error'] = true;
              $response['message'] = "Sorry! Data not inserted in VS";
            }
          } else {
            http_response_code(503);
            $response['error'] = true;
            $response['message'] = "Sorry! Data not inserted";
          }
        }
      } else {
        http_response_code(503);
        $response["error"] = true;
        $response["message"] = "Patient Checked Out";
      }

      //Check User Access End
    } else {
      http_response_code(400);
      $response['error'] = true;
      $response['message'] = "Access Denied";
    }
  } else {
    http_response_code(400);
    $response['error'] = true;
    $response['message'] = "Sorry! some details are missing";
  }
  //Check empty Parameters End
} catch (PDOException $e) {
  http_response_code(503);
  $response['error'] = true;
  $response['message'] = "Connection failed" . $e->getMessage();;
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>