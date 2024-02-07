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

try {

if(!empty($accesskey) && !empty($ip)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `username`, `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   $list = $pdoread->prepare("SELECT @a:=@a+1 AS sno, `ip`, `umrno`, `consultant`, `cross_consultants`, `surgery_date`, `diagnosis`, `procedure_done`, `chief_complaint`, `history_present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`,  `general_examination`, `vitals`, `systemic_examination`, `course_hospital`, `investigation_reports`, `preventive_measures`, `urgent_care_instructions`, `dietary_advised`, `follow_up_instructions`, `transfer_shift_discharge_details`,
`medications_advised`, `createdby`, DATE_FORMAT(`createdon`, '%d-%b-%Y %h:%i:%s') as creon from (SELECT @a:=0) AS a, `ip_discharge_summary` where `estatus` = 'Active' And `ip` =:ip order by sno desc limit 1");
        $list->bindParam(':ip', $ip, PDO::PARAM_STR);
        $list->execute();
        if($list -> rowCount() > 0){
			http_response_code(200);
          $response['error'] = false;
          $response['message']= "Data found";
      
        while ($row = $list->fetch(PDO::FETCH_ASSOC)) {
          $response['ip']= $row['ip'];
          $response['umrno']= $row['umrno'];
          $response['consultant']= $row['consultant'];
          $response['cross_consultants']= $row['cross_consultants'];
          $response['surgery_date']= $row['surgery_date'];
          $response['diagnosis']= $row['diagnosis'];
          $response['procedure_done']= $row['procedure_done'];
          $response['chief_complaint']= $row['chief_complaint'];
          $response['history_present_illness']= $row['history_present_illness'];
          $response['past_history']= $row['past_history'];
          $response['treatment_history']= $row['treatment_history'];
          $response['allergies']= $row['allergies'];        
          $response['personal_history']= $row['personal_history'];
          $response['family_history']= $row['family_history'];
          $response['obstetric_history']= $row['obstetric_history'];
          $response['general_examination']= $row['general_examination'];
          $response['vitals']= $row['vitals'];
          $response['systemic_examination']= $row['systemic_examination'];
          $response['course_hospital']= $row['course_hospital'];
          $response['investigation_reports']= $row['investigation_reports'];
          $response['preventive_measures']= $row['preventive_measures'];
          $response['urgent_care_instructions']= $row['urgent_care_instructions'];
          $response['dietary_advised']= $row['dietary_advised'];
          $response['follow_up_instructions']= $row['follow_up_instructions'];   
          $response['transfer_shift_discharge_details']= $row['transfer_shift_discharge_details'];
          $response['medications_advised']= $row['medications_advised'];
          $response['createdby']= $row['createdby'];
          $response['createdon']= $row['creon'];    
                    
              }
          
      
     
        
       

}else{
	http_response_code(503);
  $response['error'] = true;
  $response['message']= "No data found";
}
//Check User Access End
}else{
	http_response_code(400);
    $response['error'] = true; 
      $response['message']= "Access denied";
  }
}else{
	http_response_code(400);
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
$pdoread = null;
?>
