<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$ip = $data->ip;
$response = array();
try{
if(!empty($accesskey)){
	
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
		  
 $fetch = $pdoread -> prepare("SELECT IFNULL(`consultant`,'') AS consultant, IFNULL(`cross_consultants`,'') AS cross_consultants, IFNULL(`surgery_date`,'') AS surgery_date, IFNULL(`diagnosis`,'') AS diagnosis, IFNULL(`procedure_done`,'') AS procedure_done, IFNULL(`chief_complaint`,'') AS chief_complaint, IFNULL(`history_present_illness`,'') AS history_present_illness, IFNULL(`past_history`,'') AS past_history, IFNULL(`treatment_history`,'') AS treatment_history, IFNULL(`allergies`,'') AS allergies, IFNULL(`personal_history`,'') AS personal_history, IFNULL(`family_history`,'') AS family_history, IFNULL(`obstetric_history`,'') AS obstetric_history, IFNULL(`general_examination`,'') AS general_examination, IFNULL(`vitals`,'') AS vitals, IFNULL(`systemic_examination`,'') AS systemic_examination, IFNULL(`course_hospital`,'') AS course_hospital, IFNULL(`investigation_reports`,'') AS investigation_reports, IFNULL(`preventive_measures`,'') AS preventive_measures, IFNULL(`urgent_care_instructions`,'') AS urgent_care_instructions,IFNULL(`dietary_advised`,'') AS dietary_advised, IFNULL(`follow_up_instructions`,'') AS follow_up_instructions, IFNULL(`transfer_shift_discharge_details`,'') AS transfer_shift_discharge_details, IFNULL(`medications_advised`,'') AS medications_advised, IFNULL(`birth_history`,'') AS birth_history, IFNULL(`immunization_history`,'') AS immunization_history, IFNULL(`developmental_history`,'') AS developmental_history, IFNULL(`createdby`,'') AS createdby, IFNULL(`createdon`,'') AS createdon, IFNULL(`modifiedby`,'') AS modifiedby, IFNULL(`modifiedon`,'') AS modifiedon, IFNULL(`estatus`,'') AS estatus, IFNULL(`is_dead`,'') AS is_dead FROM `ip_discharge_summary` WHERE ip=:ip");
 $fetch->bindParam(':ip', $ip, PDO::PARAM_STR);
 $fetch -> execute();
 if($fetch -> rowCount() > 0){
 $titles = $fetch->fetch(PDO::FETCH_ASSOC);

$my_array = array("Consultants","Cross Consultants","Diagnosis","Procedure Done","Chief Complaint","History Of Present Illness","Past History","Treatment History","Allergies","Personal History","Family History","Obstetric History","General Examination","Vitals","Systemic Examination","Course In The Hospital","Preventive Measures","Urgent Care Instructions","Follow Up Instructions","Transfer/Shiftout/Discharge Details","Medications Advised","Dietary Advised","Investigation Reports");
$my_array1 = array($titles['consultant'],$titles['cross_consultants'],$titles['diagnosis'],$titles['procedure_done'],$titles['chief_complaint'],$titles['history_present_illness'],$titles['past_history'],$titles['treatment_history'],$titles['allergies'],$titles['personal_history'],$titles['family_history'],$titles['obstetric_history'],$titles['general_examination'],$titles['vitals'],$titles['systemic_examination'],$titles['course_hospital'],$titles['preventive_measures'],$titles['urgent_care_instructions'],$titles['follow_up_instructions'],$titles['transfer_shift_discharge_details'],$titles['medications_advised'],$titles['dietary_advised'],$titles['investigation_reports']);
$my_array2 = array("Yes","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No");

$my_array3 = array("","","","","chief_complaint","","past_history","treatment_history","allergies","personal_history","family_history","obstetric_history","general_examination","vitals","systemic_examination","","","","","","","","");

   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    $response['normaldischarge']= "Normal Discharge";
    $response['dead']= "Dead";
    $response['lama']= "Lama";
    $response['dama']= "Dama";
    $response['is_dead']= $titles['is_dead'];
    for($x = 0; $x < sizeof($my_array); $x++){
	$response['ipdischargesummarytitles'][$x]['title']=$my_array[$x];	
	$response['ipdischargesummarytitles'][$x]['Value']=$my_array1[$x];	
	$response['ipdischargesummarytitles'][$x]['category']=$my_array3[$x];	
	$response['ipdischargesummarytitles'][$x]['view_btn']="Yes";	
	$response['ipdischargesummarytitles'][$x]['save_btn']="Yes";	
	$response['ipdischargesummarytitles'][$x]['history_btn']="No";	
	$response['ipdischargesummarytitles'][$x]['add_doc_btn']=$my_array2[$x];	
     }
	 
	 
}else{
    http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    $response['normaldischarge']= "Normal Discharge";
    $response['dead']= "Dead";
    $response['lama']= "Lama";
    $response['dama']= "Dama";
    $response['is_dead']= "";
	
	$my_array = array("Consultants","Cross Consultants","Diagnosis","Procedure Done","Chief Complaint","History Of Present Illness","Past History","Treatment History","Allergies","Personal History","Family History","Obstetric History","General Examination","Vitals","Systemic Examination","Course In The Hospital","Preventive Measures","Urgent Care Instructions","Follow Up Instructions","Transfer/Shiftout/Discharge Details","Medications Advised","Dietary Advised","Investigation Reports");
    $my_array1 = array($titles['consultant'],$titles['cross_consultants'],$titles['diagnosis'],$titles['procedure_done'],$titles['chief_complaint'],$titles['history_present_illness'],$titles['past_history'],$titles['treatment_history'],$titles['allergies'],$titles['personal_history'],$titles['family_history'],$titles['obstetric_history'],$titles['general_examination'],$titles['vitals'],$titles['systemic_examination'],$titles['course_hospital'],$titles['preventive_measures'],$titles['urgent_care_instructions'],$titles['follow_up_instructions'],$titles['transfer_shift_discharge_details'],$titles['medications_advised'],$titles['dietary_advised'],$titles['investigation_reports']);
    $my_array2 = array("Yes","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No","No");

    $my_array3 = array("","","","","chief_complaint","","past_history","treatment_history","allergies","personal_history","family_history","obstetric_history","general_examination","vitals","systemic_examination","","","","","","","","");
	
    for($x = 0; $x < sizeof($my_array); $x++){
	$response['ipdischargesummarytitles'][$x]['title']=$my_array[$x];	
	$response['ipdischargesummarytitles'][$x]['Value']="";	
	$response['ipdischargesummarytitles'][$x]['category']=$my_array3[$x];	
	$response['ipdischargesummarytitles'][$x]['view_btn']="Yes";	
	$response['ipdischargesummarytitles'][$x]['save_btn']="Yes";	
	$response['ipdischargesummarytitles'][$x]['history_btn']="No";	
	$response['ipdischargesummarytitles'][$x]['add_doc_btn']=$my_array2[$x];	
     }
}
	 
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
   }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
} 
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();

}
echo json_encode($response);
$pdoread = null;
?>