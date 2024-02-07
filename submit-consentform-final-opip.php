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
$accesskey= $data->accesskey;
$umrno= $data->ipno;
$answers= $data->answers;
$consent_id= $data->consent_id;
$patient_sign_one= $data->patient_sign_one;
$patient_name= $data->patient_name;
$patient_relation= $data->patient_relation;
$witness_sign=$data->witness_sign;
$witness_name=$data->witness_name;
$witness_relation=$data->witness_relation;
$doctor_sign=$data->doctor_sign;
$doctor_name=$data->doctor_name;
$content2=strip_tags($data->consent);
$content1=str_replace("&nbsp;"," ",$content2);
$content=preg_replace('~>\s*\n\s*<~', '><',$content1);
$interpreter_sign=$data->interpreter_sign;
$interpreter_name=$data->interpreter_name;
$patienttitle=$data->patienttitle;
$patientnametitle=$data->patientnametitle;
$patientrelationtitle=$data->patientrelationtitle;
$doctortitle=$data->doctortitle;
$doctornametitle=$data->doctornametitle;
$witnesstitle=$data->witnesstitle;
$witnessnametitle=$data->witnessnametitle;
$witnessrelationtitle=$data->witnessrelationtitle;
$interpreternametitle=$data->interpreternametitle;
$interpretertitle=$data->interpretertitle;
$image = $data->image;
$status = $data->status;
$response = array();
try{
if(!empty($accesskey) && !empty($umrno)  && !empty($patient_name)  && !empty($patient_sign_one) && !empty($status)){
	
$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
$check1 = $pdo4 -> prepare("update `consent_patient_data` SET `status`='Inactive', `modified_by`=:userid, `modified_on`=CURRENT_TIMESTAMP where `ipno`=:ipno and `consent_id`=:consent_id AND status='Active'");
$check1->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
$check1->bindParam(':ipno', $umrno, PDO::PARAM_STR);
$check1->bindParam(':userid', $emp['userid'], PDO::PARAM_STR);
$check1 -> execute();
if($status=='OPD'){
	$stmt2 = $pdoread->prepare("SELECT op_billing_generate.umrno as `admissionno`,op_billing_generate.`umrno`,'' as `billno`,DATE_FORMAT(`created_on`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(op_billing_generate.`patient_name`) AS patientname,DATE_FORMAT(op_billing_generate .`created_on`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(op_billing_generate.`created_on`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),op_billing_generate.`patient_age`)), '%Y')+0 AS Age,op_billing_generate.`patient_gender` AS gender,op_billing_generate.`mobile` AS mobile,'' AS ward,'' AS bedno,'' AS consultant,'' AS department,'' AS surgery,'info@medicoverhospitals.in' AS emailid,'' AS category,'' as `nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,op_billing_generate.cost_center,'' as `tpa_name`,op_billing_generate.organization_name  FROM `op_billing_generate` INNER join umr_registration on umr_registration.umrno=op_billing_generate.umrno WHERE op_billing_generate.`invoice_no` LIKE :search  AND op_billing_generate.`status` = 'Confirmed'  AND op_billing_generate.cost_center=:cost_center ORDER BY `umrno` DESC;");
}else{
	$stmt2 = $pdoread->prepare("SELECT `admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(`admittedon`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,registration.cost_center,`tpa_name`,registration.organization_name  FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible'  AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");
}
	$stmt2->bindParam(':search', $umrno, PDO::PARAM_STR);
	$stmt2->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt2 -> execute(); 
	$data = $stmt2 -> fetch(PDO::FETCH_ASSOC);
	
	$stmt1_transid = $pdoread->prepare("SELECT Concat('CONSA',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX( transid),'CONSA230700000'),Concat('CONSA',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS transid FROM `consent_patient_data` where transid like concat('%CONSA',DATE_FORMAT(CURRENT_DATE,'%y'),'%') ORDER BY sno DESC LIMIT 1");
	$stmt1_transid -> execute(); 
	$transiddata = $stmt1_transid -> fetch(PDO::FETCH_ASSOC);
	
	
	  $answersdata=explode("|*",(str_replace('"','',$answers)));
				  
				  if($answers!="No Data"){
				   foreach ($answersdata as $answersdata11) {
				
				  $answersdata1=explode("|-",$answersdata11);
		           $remarks=str_replace("|","",$answersdata1[4]);
				
				   }
	
				  }else{
					  $remarks="";
				  }
	$stmt1=$pdo4->prepare("INSERT INTO `consent_patient_data`(transid,ipno,`umrno`,billno, `consent_id`, `patient_sign_one`, `patient_name`, `patient_on`, `witness_sign`, `witness_on`, `doctor_sign`, `doctor_name`, `doctor_on`, `interpreter_sign`, `interpreter_on`, `status`,content,`patient_relation`,`witness_relation`,witness_name,interpreter_name,consent_remarks, `created_by`, `created_on`,`patient_heading`,`witness_heading`,`doctor_heading`,`interpreter_heading`,`patientnametitle`, `patientrelationtitle`,`witnessnametitle`, `witnessrelationtitle`,`doctornametitle`,`interpreternametitle`,`form_status`) VALUES (:transid,:ipno,:umrno,:billno,:consent_id,:patient_sign_one,:patientname,CURRENT_TIMESTAMP,:witness_sign,CURRENT_TIMESTAMP,:doctor_sign,:doctor_name,CURRENT_TIMESTAMP,:interpreter_sign,CURRENT_TIMESTAMP,'Active',:content,:patient_relation,:witness_relation,:witness_name,:interpreter_name,:remarks,:userid,CURRENT_TIMESTAMP,:patienttitle,:witnesstitle,:doctortitle,:interpretertitle,:patientnametitle,:patientrelationtitle,:witnessnametitle,:witnessrelationtitle,:doctornametitle,:interpreternametitle,:status)");
	  $stmt1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
	$stmt1->bindParam(':ipno', $umrno, PDO::PARAM_STR);
	$stmt1->bindParam(':umrno', $data['umrno'], PDO::PARAM_STR);
	$stmt1->bindParam(':content', $content, PDO::PARAM_STR);
	$stmt1->bindParam(':billno', $data['billno'], PDO::PARAM_STR);
	$stmt1->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
	$stmt1->bindParam(':patient_sign_one', $patient_sign_one, PDO::PARAM_STR);
	$stmt1->bindParam(':patientname',  $patient_name, PDO::PARAM_STR);
	$stmt1->bindParam(':witness_sign', $witness_sign, PDO::PARAM_STR);
	$stmt1->bindParam(':witness_name', $witness_name, PDO::PARAM_STR);
	$stmt1->bindParam(':doctor_sign', $doctor_sign, PDO::PARAM_STR);
	$stmt1->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
	$stmt1->bindParam(':interpreter_name', $interpreter_name, PDO::PARAM_STR);
	$stmt1->bindParam(':interpreter_sign', $interpreter_sign, PDO::PARAM_STR);
	$stmt1->bindParam(':patient_relation', $patient_relation, PDO::PARAM_STR);
	$stmt1->bindParam(':witness_relation', $witness_relation, PDO::PARAM_STR);
	$stmt1->bindParam(':patienttitle', $patienttitle, PDO::PARAM_STR);
	$stmt1->bindParam(':witnesstitle', $witnesstitle, PDO::PARAM_STR);
	$stmt1->bindParam(':doctortitle', $doctortitle, PDO::PARAM_STR);
	$stmt1->bindParam(':interpretertitle', $interpretertitle, PDO::PARAM_STR);
	$stmt1->bindParam(':patientnametitle', $patientnametitle, PDO::PARAM_STR);
	$stmt1->bindParam(':patientrelationtitle', $patientrelationtitle, PDO::PARAM_STR);
	$stmt1->bindParam(':witnessnametitle', $witnessnametitle, PDO::PARAM_STR);
	$stmt1->bindParam(':witnessrelationtitle', $witnessrelationtitle, PDO::PARAM_STR);
	$stmt1->bindParam(':doctornametitle', $doctornametitle, PDO::PARAM_STR);
	$stmt1->bindParam(':interpreternametitle', $interpreternametitle, PDO::PARAM_STR);
	$stmt1->bindParam(':remarks', $remarks, PDO::PARAM_STR);
	$stmt1->bindParam(':status', $status, PDO::PARAM_STR);
	$stmt1->bindParam(':userid', $emp['userid'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){		
         http_response_code(200);
        $response['error']= false;
	    $response['message']="Sucessfully updated";
      }
	 else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="Already submitted";
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
} 
catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>