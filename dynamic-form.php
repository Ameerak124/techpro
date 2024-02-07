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
$form_id= $data->form_id;
$response = array();
$response1 = array();
$response3 = array();
$checkbox = array();
$radiobuttons = array();
try{
if(!empty($form_id)){
$check = $pdoread -> prepare("SELECT `sno`, `form_id`, `formname`, Concat(`formdescription` ,'( ',`department`,' )') AS formdescription, `usage_type`, `frequency`, `freqcount`, `url_name`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `estatus`, `costcenter` FROM `main_dynamic_form` WHERE `form_id`=:form_id AND `estatus`='Active'");
$check->bindParam(':form_id', $form_id, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
$colortext = "#757b7d";
$colorHeading = "#3e54ac";
$colorTitle = "#6096b4";
$colorButtontint = "#33c1a1";
$colorButtontext = "#041e1c";
$colorstrock= "#33c1a1";
$colorsbuttontintsignature =  "#FFFFFF";
$colorButtonclear = "#de5246";
$colorstrockcler = "#e90064";
$temp=[
    "type"=>"header",
    "subtype"=> "h1",
    "label"=> $emp['formname'],
	  "textcolor"=>$colorHeading,
		  "textsize"=>"18",
			  "textlabelsize"=>"10",
    "access"=> false

];
array_push($response1,$temp);
$temppp=[
   "type"=>"paragraph",
    "subtype"=>"p",
		  "textsize"=>"12",
			  "textlabelsize"=>"10",
    "label"=>$emp['formdescription'],
	"textcolor"=>$colorTitle,
    "access"=>false
];
array_push($response1,$temppp);
$stmt2=$pdoread->prepare("SELECT `sno`, Concat(`formid`,'|-',`question_id`,'|-', `question_name`,'|-',`question_type`) AS details,`question_type`, `question_name`, `question_option`, `required_type`  FROM `dynamic_form_question` WHERE `formid`=:form_id AND `estatus`='Active'");
$stmt2->bindParam(':form_id', $form_id, PDO::PARAM_STR);
$stmt2-> execute();
if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
		  $response['error']= false;
        while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){

			if($row['question_type']=="Paragraph"){
			   $temp2=[
			  "type"=>"textarea",
              "required"=>false,
              "label"=>$row['question_name'],
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
    "label"=>$row['question_name'],
    "placeholder"=>$row['question_name'],
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

    $button= [
     "type"=>"button",
    "label"=>"Signature 🖋️",
    "subtype"=>"alertbtn",
    "subtype2"=>"alertkt",
    "className"=>"btn-default btn",
    "name"=>"button-1640082377047-0",
    "access"=>false,
				  "textsize"=>"12",
			  "textlabelsize"=>"10",
			  "textboderscolor"=>$colorstrock,
	"textcolor"=>$colorButtontext,
	"buttontint"=>$colorsbuttontintsignature,
    "style"=>"default"
  ];
	array_push($response1,$button);
   $button1= [
   
  "type"=>"button",
    "label"=>"Submit form",
    "subtype"=>"button",
    "subtype2"=>"button",
    "className"=>"btn-default btn",
    "name"=>"button-1640082377047-0",
    "access"=>false,
	 "textboderscolor"=>$colorButtontint,
				  "textsize"=>"14",
			  "textlabelsize"=>"10",
	"textcolor"=>$colorButtontext,
	"buttontint"=>$colorButtontint,
    "style"=>"default"
  ];
	array_push($response1,$button1);
		$response['message'] = $response1;	 
}
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
