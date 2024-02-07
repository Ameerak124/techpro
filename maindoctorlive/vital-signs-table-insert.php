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
$umr_no = trim($data->umr_no);
$doc_id = trim($data->doc_id);
$captured_date_and_time = $data->captured_date_and_time;
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
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
//Check empty Parameters Start
if(!empty($accesskey) && !empty($ip) && !empty($captured_date_and_time)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
    $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
    $validate->bindParam(':ip', $ip, PDO::PARAM_STR);
    $validate -> execute();
    $validates = $validate->fetch(PDO::FETCH_ASSOC);
    if($validate -> rowCount() > 0){     
      
      $vs_id = $pdoread -> prepare("SELECT IFNULL(MAX(`page_id`),CONCAT('vs',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS page_id  FROM `vital_signs_table` WHERE `page_id` LIKE '%PR%'");
$vs_id -> execute();
if($vs_id -> rowCount() > 0){
  $vs = $vs_id->fetch(PDO::FETCH_ASSOC);
    $vsid =  $vs['page_id'];
   $vsids = ++$vsid;
  }else{
  $vsids = $vsidno;
  }  
             
  $vsinsert = $pdo4->prepare("INSERT INTO `vital_signs_table`(`sno`, `page_id`, `assessment_type`, `admission_num`, `doctor_id`, `captured_date_and_time`,  `bp_position_sleep`, `bp_type_sleep`, `bp_systolic_sleep`, `bp_diastolic_sleep`, `bp_position_sit`, `bp_type_sit`, `bp_systolic_sit`, `bp_diastolic_sit`, `bp_position_stand`, `bp_type_stand`, `bp_systolic_stand`, `bp_diastolic_stand`, `bp_units`, `pulse_type`, `pulse_rate`, `pulse_units`, `heart_rate_type`, `heart_rate`, `heart_rate_units`, `respiratory_rate`, `respiratory_units`, `weight`, `weight_units`, `height_rate`, `height_units`, `bmi_rate`, `bmi_units`, `bsa_rate`, `bsa_units`, `temp_type`, `temp_rate`, `temp_units`, `spo2_type`, `spo2_type1`, `spo2_rate`, `pain_score`, `grbs`, `grbs_units`, `blood_group`, `created_on`, `created_by`, `modified_on`, `modified_by`, `estatus`, `umr_no`) VALUES (NULL, :vsids, 'vital signs', :admission_num, :doc_id, :captured_date_and_time, ':bp_check_position_1', :bp_dd_1, :bp_systolic_1, :bp_diastolic_1, :bp_check_position_2, :bp_dd_2, :bp_systolic_2, :bp_diastolic_2, :bp_check_position_3, :bp_dd_3, :bp_systolic_3, :bp_diastolic_3, :bp_units, :pulse_dd, :pulse_rate, :pulse_units, :heart_rate_dd, :heart_rate, :heart_rate_units, :respiratory_rate, :respiratory_units, :weigh, :weight_units, :height_rate, :height_units, :bmi_rate, :bmi_units, :bsa_rate, :bsa_units, :temp_dd, :temp_rate, :temp_units, :spo2_dd1, :spo2_dd2, :spo2_rate, :pain_score, :grbs, :grbs_units, :blood_group,  CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP, :userid, 'Active', :umr_no) ");
  $vsinsert->bindParam(':admission_num', $ip, PDO::PARAM_STR);
  $vsinsert->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
  $vsinsert->bindParam(':vsids', $vsids, PDO::PARAM_STR);
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
  $vsinsert -> execute();
  if($vsinsert -> rowCount() > 0){
    $response['error']= false;
  $response['message']= "Data inserted";
             
              }else{
                  $response['error'] = true;
                  $response['message']= "Data Not Inserted";
              }
            }else{
              $response['error'] = true;
                $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
          }
//Check User Access End
}else{
    $response['error'] = true;
      $response['message']= "Access Denied";
  }
}else{
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();;
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
