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
//$form_id= $data->form_id;
$accesskey= $data->accesskey;
$lang= $data->lang;
$consent_id= $data->consentid;
$keyword= $data->keyword;
$transid= $data->transid;
$response = array();
$response1 = array();
$response3 = array();
$checkbox = array();
$radiobuttons = array();
try{
if(!empty($consent_id) && !empty($accesskey) && !empty($lang) && !empty($keyword)){
	$check = $con -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp1 = $check -> fetch(PDO::FETCH_ASSOC);
	$stmt1 = $pdoread->prepare("SELECT `admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(`admittedon`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,registration.cost_center,`tpa_name`,registration.organization_name,branch_master.display_name as branch  FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno  INNER JOIN branch_master on branch_master.cost_center=registration.cost_center WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt1->bindParam(':search', $keyword, PDO::PARAM_STR);
	$stmt1->bindParam(':cost_center', $emp1['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
		$data = $stmt1 -> fetch(PDO::FETCH_ASSOC);
		
$stmt2=$pdoread->prepare("SELECT `title`, `consent_id`,case when :lang='English' then replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_eng`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance) when :lang='Telugu' then replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_tel`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance) when :lang='Hindi' then replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_hin`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance) else ''end as contenttext, `patient_sign_one` as patient_sign,`patient_sign_two` as witness_sign,`emp_sign_one` as doctor_sign,`emp_sign_two` as interpreter_sign, `photo`, `questionnaires`, `questions_id`, `questions_type`, `created_by`, `created_on`, `status`,(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_submit`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance)) as submit_content FROM `consent_form_master` WHERE `status`='1' and consent_id=:consent_id");
	$stmt2->bindParam(':lang', $lang, PDO::PARAM_STR);
	$stmt2->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
	$stmt2->bindParam(':name', $data['patientname'], PDO::PARAM_STR);
	$stmt2->bindParam(':age', $data['Age'], PDO::PARAM_STR);
	$stmt2->bindParam(':gender', $data['gender'], PDO::PARAM_STR);
	$stmt2->bindParam(':date', $data['admitteddate'], PDO::PARAM_STR);
	$stmt2->bindParam(':time',$data['admittedtime'], PDO::PARAM_STR);
	$stmt2->bindParam(':costcenter',$data['branch'], PDO::PARAM_STR);
	$stmt2->bindParam(':ipno',$data['admissionno'], PDO::PARAM_STR);
	$stmt2->bindParam(':bedno',$data['bedno'], PDO::PARAM_STR);
	$stmt2->bindParam(':doctor',$data['consultant'], PDO::PARAM_STR);
	$stmt2->bindParam(':mobile',$data['mobile'], PDO::PARAM_STR);
	$stmt2->bindParam(':address',$data['address'], PDO::PARAM_STR);
	$stmt2->bindParam(':tpa',$data['tpa_name'], PDO::PARAM_STR);
	$stmt2->bindParam(':insurance',$data['organization_name'], PDO::PARAM_STR);
	$stmt2 -> execute(); 
if($stmt2 -> rowCount() > 0){
$emp = $stmt2 -> fetch(PDO::FETCH_ASSOC);
$colortext = "#757b7d";
$colorHeading1 = "#000000";
$colorHeading = "#4886C6";
$colorTitle = "#6096b4";
$colorButtontint = "#5393D1";
$colorButtontext = "#041e1c";
$colorstrock= "#33c1a1";
$colorsbuttontintsignature =  "#FFFFFF";
$colorButtonclear = "#de5246";
$colorstrockcler = "#e90064";
$temppp=[
   "type"=>"paragraph",
    "subtype"=>"p",
		  "textsize"=>"15",
			  "textlabelsize"=>"16",
    "label"=>$emp['contenttext'],
    "submit_content"=>$emp['submit_content'],
	"textcolor"=>$colorHeading1,
    "access"=>false

];
array_push($response1,$temppp);
$temp=[

    "type"=>"headertext",
    "subtype"=> "h1",
    "label"=> "IP NO : ".$data['admissionno'],
	"textcolor"=>$colorHeading,
		  "textsize"=>"15",
			  "textlabelsize"=>"10",
    "access"=> false

];
array_push($response1,$temp);

$stmt21=$pdoread->prepare("SELECT `sno`, Concat(`formid`,'|-',`question_id`,'|-', `question_name`,'|-',`question_type`) AS details,`question_type`, `question_name`, `question_option`, `required_type`,question_id  FROM `consent_form_questions_new` WHERE `formid`=:form_id AND `estatus`='Active'");
$stmt21->bindParam(':form_id', $consent_id, PDO::PARAM_STR);
$stmt21-> execute();
if($stmt21 -> rowCount() > 0){
		 http_response_code(200);
		  $response['error']= false;
            while($row = $stmt21->fetch(PDO::FETCH_ASSOC)){
			
$stmt22=$pdoread->prepare("SELECT `answers` FROM `consent_form_submit_answer` where `transid`=:transid and `questionid`=:questionid");
$stmt22->bindParam(':transid', $transid, PDO::PARAM_STR);
$stmt22->bindParam(':questionid', $row['question_id'], PDO::PARAM_STR);
$stmt22-> execute();	
	if($stmt22 -> rowCount() > 0){
	$rowsresult = $stmt22->fetch(PDO::FETCH_ASSOC);	
	 $answers = $rowsresult['answers'];
	}else{
      $answers='';
	}		
			
			if($row['question_type']=="Paragraph"){
			   $temp2=[
			  "type"=>"textarea",
              "required"=>false,
              "label"=>$row['question_name'],
              "answer"=>$answers,
              "details"=>$row['details'],
              "className"=>"form-control",
              "name"=>"textarea-1640160857581-0",
              "access"=>false,
              "subtype"=>"textarea",
			  "textsize"=>"12",
			  "textlabelsize"=>"10",
			  "textcolor"=>$colortext,
               "nameparam"=>"courseName",
              "rows"=>8
             ];
	   
		array_push($response1,$temp2);	 
			  }else if($row['question_type']=="Short Answer"){
			   $temp222=[
			
    "type"=>"text",
    "required"=>false,
    "label"=>"",
    "placeholder"=>$row['question_name'],
	"answer"=>$answers,
    "details"=>$row['details'],
    "className"=>"form-control",
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
    "name"=>"text-1640082066779-0",
	"textcolor"=>$colortext,
    "access"=>false,
    "subtype"=>"text",
    "maxlength"=>200
      
             ];
	   
		array_push($response1,$temp222);	 
			  }else if($row['question_type']=="Multiple Choice"){
			  
	$expradio=explode('|-',$row['question_option']);
	$radiobuttons=array();
	foreach ($expradio as $expradio1) {
    $tempradio=[
	"label"=>$expradio1,
     "value"=>$expradio1,
     "selected"=>false
	
	];
	array_push($radiobuttons,$tempradio);
    }
	$temp4= [
    "type"=>"radio-group",
    "required"=>false,
    "label"=>$row['question_name'],
	"answer"=>$answers,
     "details"=>$row['details'],
    "inline"=>false,
	"textcolor"=>$colortext,
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
    "name"=>"radio-group-1640082112533-0",
    "access"=>false,
    "other"=>false,
	 "values"=>$radiobuttons
    
  ];
	array_push($response1,$temp4);
}else if($row['question_type']=="Checkboxes"){
	$expcheck=explode('|-',$row['question_option']);
	$checkbox=array();
	foreach ($expcheck as $expcheck1) {
    $tempcheck=[
	"label"=>$expcheck1,
     "value"=>$expcheck1,
     "selected"=>false
	
	];
	array_push($checkbox,$tempcheck);
    }
	$temp4= [
   
      "type"=>"checkbox-group",
    "required"=>false,
    "label"=>$row['question_name'],
	"answer"=>$answers,
	"details"=>$row['details'],
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
    "toggle"=>false,
    "inline"=>false,
	"textcolor"=>$colortext,
    "name"=>"checkbox-group-1640082315632-0",
    "access"=>false,
    "other"=>false,
	  "values"=>$checkbox
    
  ];
	array_push($response1,$temp4);	 			  
}else if($row['question_type']=="Dropdown"){
	
	$exp=explode('|-',$row['question_option']);
	foreach ($exp as $exp1) {
    $tempdrop=[
	"label"=>$exp1,
     "value"=>$exp1,
     "selected"=>true
	
	];
	array_push($response3,$tempdrop);
    }

	$temp4= [
   
    "type"=>"select",
    "required"=>false,
    "label"=>$row['question_name'],
	"answer"=>$answers,
	"details"=>$row['details'],
	"textcolor"=>$colortext,
    "className"=>"form-control",
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
    "name"=>"select-1640082150449-0",
    "access"=>false,
    "multiple"=>false,
	"values"=>$response3
  ];
	array_push($response1,$temp4);	 			  
	  
		  }
}
$temppaintView= [
    "type"=>"paintView",
	 "required"=>false,

    "subtype"=>"paintView",
    "label"=>"Signature",
	"textcolor"=>$colortext,
    "access"=>false
  ];
	array_push($response1,$temppaintView);	
$tempbuttonView= [
     "type"=>"buttonView",
    "subtype"=>"buttoncancel",
    "className"=>"btn-default btn",
    "name"=>"button-1640082377127-0",
    "access"=>false,
	"label"=>"Clear  ✖",
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
	  "textboderscolor"=>$colorstrockcler,
	"textcolor"=>$colorstrockcler,
	"buttontint"=>$colorsbuttontintsignature,
    "style"=>"default"
  
  ];
	array_push($response1,$tempbuttonView);	
  $tempbuttonView2= [
   "type"=>"buttonView",
    "subtype"=>"buttonView",
    "className"=>"btn-default btn",
    "name"=>"button-1640082377047-0",
    "access"=>false,
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
	"label"=>"SAVE ✓",
	  "textboderscolor"=>$colorButtontint,
	  "buttontint"=>$colorButtontint,
	"textcolor"=>$colorButtontext,
	
    "style"=>"default"
  
  ];
	array_push($response1,$tempbuttonView2);
	 $tempimageview= [
    "type"=>"imageview",
	 "textboderscolor"=>"#1E88E5",
	  "buttontint"=>"#1E88E5",
    "subtype"=>"imageview",
    "access"=>false,
	 "name"=>"imageview-1640082377067-0",
    "access"=>false,
	"label"=>"imageview",
	"textcolor"=>$colorsbuttontintsignature,
	  "textlabelsize"=>"10",
	   "textsize"=>"14",
    "style"=>"default"
  
  ];
	array_push($response1,$tempimageview);
	
   $textview= [
     "type"=>"textview",
    "label"=>"textview",
    "subtype"=>"textview",
    "subtype2"=>"textview",
    "className"=>"form-control",
    "name"=>"textview-1640082377674-0",
    "access"=>false,
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
	"textcolor"=>$colortext,
	"buttontint"=>$colorsbuttontintsignature,
    "style"=>"default"
  
  ];
	array_push($response1,$textview);
		 
}
	  $response['message'] = $response1;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
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
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>
