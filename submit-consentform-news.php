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
$consent_id= $data->consent_id;
$answers= $data->answers;
$patient_sign_one= $data->patient_sign_one;
$patient_name= $data->patient_name;
$patient_relation= $data->patient_relation;
$witness_sign=$data->witness_sign;
$witness_name=$data->witness_name;
$witness_relation=$data->witness_relation;
$doctor_sign=$data->doctor_sign;
$doctor_name=$data->doctor_name;
$content=$data->consent;
$interpreter_sign=$data->interpreter_sign;
$interpreter_name=$data->interpreter_name;
$image = $data->image;
$response = array();
try{
if(!empty($accesskey) && !empty($umrno) && !empty($patient_sign_one)  && !empty($doctor_sign)){
	
$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
$check1 = $pdoread -> prepare("SELECT * FROM `consent_patient_data` where `ipno`=:ipno and `consent_id`=:consent_id");
$check1->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
$check1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$check1 -> execute();
if($check1 -> rowCount() > 0){
	http_response_code(503);
    $response['error']= true;
  	$response['message']="Already submitted";
	
}else{
	$stmt2 = $pdoread->prepare("SELECT `admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(`admittedon`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,registration.cost_center,`tpa_name`,registration.organization_name  FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt2->bindParam(':search', $umrno, PDO::PARAM_STR);
	$stmt2->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt2 -> execute(); 
	$data = $stmt2 -> fetch(PDO::FETCH_ASSOC);
	
	$stmt1_transid = $pdoread->prepare("SELECT Concat('CONSA',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX( transid),'CONSA230700000'),Concat('CONSA',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS transid FROM `consent_patient_data` where transid like concat('%CONSA',DATE_FORMAT(CURRENT_DATE,'%y'),'%') ORDER BY sno DESC LIMIT 1");
	$stmt1_transid -> execute(); 
	$transiddata = $stmt1_transid -> fetch(PDO::FETCH_ASSOC);
	
	$stmt1=$pdo4->prepare("INSERT INTO `consent_patient_data`(transid,ipno,`umrno`,billno, `consent_id`, `patient_sign_one`, `patient_name`, `patient_on`, `witness_sign`, `witness_on`, `doctor_sign`, `doctor_name`, `doctor_on`, `interpreter_sign`, `interpreter_on`, `status`,content,`patient_relation`,`witness_relation`,witness_name,interpreter_name) VALUES (:transid,:ipno,:umrno,:billno,:consent_id,:patient_sign_one,:patientname,CURRENT_TIMESTAMP,:witness_sign,CURRENT_TIMESTAMP,:doctor_sign,:doctor_name,CURRENT_TIMESTAMP,:interpreter_sign,CURRENT_TIMESTAMP,'Active',:content,:patient_relation,:witness_relation,:witness_name,:interpreter_name)");
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
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){

	              $answersdata=explode("|*",(str_replace('"','',$answers)));
				  
				  if($answers!="No Data"){
				   foreach ($answersdata as $answersdata11) {
				
				  $answersdata1=explode("|-",$answersdata11);
		
			    $submitquery1 = "INSERT INTO `consent_form_submit_answer`(`transid`, `formid`, `questionid`, `question_name`, `question_type`, `answers`, `createdon`, `createdby`, `status`) VALUES (:transid,:formid,:questionid,:question_name,:question_type,:answers,CURRENT_TIMESTAMP,:userid,'Pending')"; 
               $dynamic_sbmt1 = $pdo4 -> prepare($submitquery1);
               $dynamic_sbmt1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":formid",$answersdata1[0],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":userid",$emp['userid'],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":questionid",$answersdata1[1],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":question_name",$answersdata1[2],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":question_type",$answersdata1[3],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":answers",$answersdata1[4],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> execute();
				
				   }
				   
				   $stmtans=$pdoread->prepare("SELECT GROUP_CONCAT(`answers` SEPARATOR ', ') AS ansdata  FROM `consent_form_submit_answer`  WHERE consent_form_submit_answer.`transid`=:transid"); 
				  $stmtans -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
                  $stmtans -> execute();
				  if($stmtans->rowCount()>0){
	             $ansresult = $stmtans -> fetch(PDO::FETCH_ASSOC);
				 
				  $answerdata2=explode(",",$ansresult['ansdata']);
				if($consent_id == 'CONS00033'){
 $stmtans1=$pdoread->prepare("SELECT replace(replace(replace(replace(replace(replace(replace(:content,'1ksir',:answer),'2ksir',:answers),'3ksir',:answerss) ,'1complications',:compans),'2complications',:companss),'3complications',:compansss),'skramer', replace(replace(:remarks,'n|',''),'|','')) as contentsubmit FROM `consent_form_submit_answer` where `transid`=:transid limit 1"); 
		$stmtans1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":content",$content,PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answer",  $answerdata2[0],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answers",  $answerdata2[1],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answerss",  $answerdata2[2],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compans",  $answerdata2[3],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":companss",  $answerdata2[4],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss",  $answerdata2[5],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":remarks",  $answerdata2[6],PDO::PARAM_STR); 
        $stmtans1 -> execute();
		
}else if(($consent_id == 'CONS00005')||($consent_id == 'CONS00008')){
		$stmtans1=$pdoread->prepare("SELECT replace(replace(replace(replace(replace(:content,'1ksir',:answer),'2ksir',:answers),'3ksir',:answerss) ,'1complications',:compans),'skramer', replace(replace(:remarks,'n|',''),'|','')) as contentsubmit FROM `consent_form_submit_answer` where `transid`=:transid limit 1"); 
		$stmtans1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":content",$content,PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answer",  $answerdata2[0],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answers",  $answerdata2[1],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answerss",  $answerdata2[2],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compans",  $answerdata2[3],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":remarks",  $answerdata2[4],PDO::PARAM_STR);
        $stmtans1 -> execute();			
}else if(($consent_id == 'CONS00034')){
		$stmtans1=$pdoread->prepare("SELECT replace(replace(replace(replace(replace(replace(replace(replace(:content,'1ksir',:answer),'2ksir',:answers),'3ksir',:answerss) ,'1complications',:compans),'2complications',:companss),'3complications',:compansss),'4complications',:compansss4),'skramer', replace(replace(:remarks,'n|',''),'|','')) as contentsubmit FROM `consent_form_submit_answer` where `transid`=:transid limit 1"); 
		$stmtans1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":content",$content,PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answer",  $answerdata2[0],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answers",  $answerdata2[1],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answerss",  $answerdata2[2],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compans",  $answerdata2[3],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":companss",  $answerdata2[4],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss",  $answerdata2[5],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss4",  $answerdata2[6],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":remarks",  $answerdata2[7],PDO::PARAM_STR); 
        $stmtans1 -> execute();				
}else if($consent_id == 'CONS00009'){					
				$stmtans1=$pdoread->prepare("SELECT replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(:content,'1ksir',:answer),'2ksir',:answers),'3ksir',:answerss) ,'1complications',:compans),'2complications',:companss),'3complications',:compansss),'4complications',:compansss4),'5complications',:compansss5),'6complications',:compansss6),'7complications',:compansss7),'8complications',:compansss8) ,'9complications',:compansss9),'10complications',:compansss10),'11complications',:compansss11),'12complications',:compansss12),'13complications',:compansss13),'14complications',:compansss14),'skramer', replace(replace(:remarks,'n|',''),'|','')) as contentsubmit FROM `consent_form_submit_answer` where `transid`=:transid limit 1"); 
				  $stmtans1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
				  	$stmtans1 -> bindParam(":content",$content,PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answer",  $answerdata2[0],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answers",  $answerdata2[1],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":answerss",  $answerdata2[2],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compans",  $answerdata2[3],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":companss",  $answerdata2[4],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss",  $answerdata2[5],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss4",  $answerdata2[6],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss5",  $answerdata2[7],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss6",  $answerdata2[8],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss7",  $answerdata2[9],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss8",  $answerdata2[10],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss9",  $answerdata2[11],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss10",  $answerdata2[12],PDO::PARAM_STR);
		$stmtans1 -> bindParam(":compansss11",  $answerdata2[13],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss12",  $answerdata2[14],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss13",  $answerdata2[15],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":compansss14",  $answerdata2[16],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":remarks",  $answerdata2[17],PDO::PARAM_STR);
        $stmtans1 -> execute();
}else{
 $stmtans1=$pdoread->prepare("SELECT replace(:content,'skramer', replace(replace(:remarks,'n|',''),'|','')) as contentsubmit FROM `consent_form_submit_answer` where `transid`=:transid limit 1"); 
		$stmtans1 -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":content",$content,PDO::PARAM_STR); 
		$stmtans1 -> bindParam(":remarks",  $answerdata2[0],PDO::PARAM_STR); 
        $stmtans1 -> execute();
}
		$ansresult1 = $stmtans1 -> fetch(PDO::FETCH_ASSOC);	

		$updcontent=$pdo4->prepare("UPDATE `consent_patient_data` SET `content`= :content where `transid`=:transid"); 
		$updcontent -> bindParam(":transid",$transiddata['transid'],PDO::PARAM_STR); 
		$updcontent -> bindParam(":content",$ansresult1['contentsubmit'],PDO::PARAM_STR); 
        $updcontent -> execute();  
  }
 }			  
				
         http_response_code(200);
        $response['error']= false;
	    $response['message']="Sucessfully updated";
	    $response['content']=$ansresult1['contentsubmit'];
      }
	 else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="Already submitted";
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