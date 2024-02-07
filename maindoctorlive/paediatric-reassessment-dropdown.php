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
$status = $data->status;
$response = array();
$response1 = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);

$response["error"] = false;
$response["message"] = "Data found";       


$response["total"] = "Total";
$response["content"] = "No Risk :(0-24) Low Risk : (25-44) High Risk : > 45";
/* $response["title"] = "THE HUMPTY DUMPTY FALL RISK ASSESSMENT TOOL"; */


$listitem = array();
$listitem["title"] = "Age";
$listitem1["title"] = "Gender";
$listitem2["title"] = "Diagnosis";
$listitem3["title"] = "Cognitive Impairments";
$listitem4["title"] = "Environmental Factors";
$listitem5["title"] = "Response to surgery / Sedation/ Anesthesia";
$listitem6["title"] = "Medication usage";

$sublist = array();
$age = array("title1" => "Less than 3 years old", "Count" => "4");
$age1 = array("title1" => "3 to less than 7 years old", "Count" => "3");
$age2 = array("title1" => "7 to less than 13 years old", "Count" => "2");
$age3 = array("title1" => "13 years and above", "Count" => "1");
$gender = array("title1" => "Male", "Count" => "2");
$gender1 = array("title1" => "Female", "Count" => "1");
$diagnosis = array("title1" => "Neurological Diagnosis", "Count" => "4");
$diagnosis1 = array("title1" => "Alterations in oxygenation (Respiratory Diagnosis, Dehydration, Anemia, Anorexia, Syncope/ Dizziness, Etc.)", "Count" => "3");
$diagnosis2= array("title1" => "Psych/ Behavioral Disorders", "Count" => "2");
$diagnosis3= array("title1" => "Other Diagnosis", "Count" => "1");
$cognitive= array("title1" => "Not aware of limitations", "Count" => "3");
$cognitive1= array("title1" => "Forgets limitations", "Count" => "2");
$cognitive2= array("title1" => "Oriented to own ability", "Count" => "1");
$environmental= array("title1" => "History of falls or infant - Toddler placed in Bed", "Count" => "3");
$environmental1= array("title1" => "Patient uses assistive devices or infant - Toddler in Crib or furniture/ Lighting (Tripled Room)", "Count" => "2");
$environmental2= array("title1" => "Patient placed in bed", "Count" => "1");
$ssa= array("title1" => "Within 24 hours", "Count" => "3");
$ssa1= array("title1" => "Within 48 hours", "Count" => "2");
$ssa2= array("title1" => "More than 48 Hours", "Count" => "1");
$medication= array("title1" => "Multiple use of sedatives (excluding ICU patients, sedated and paralyzed) Hypnotics, Barbiturates, Phenothiazines, Anti-depressants, Laxatives/Diuretics, Narcotics", "Count" => "3");
$medication1= array("title1" => "One of the Meds listed above", "Count" => "2");
$medication2= array("title1" => "Other Medications / None", "Count" => "1");

$sublist = array($age,$age1,$age2,$age3);
$sublist1 = array($gender,$gender1);
$sublist2 = array($diagnosis,$diagnosis1,$diagnosis2,$diagnosis3);
$sublist3 = array($cognitive,$cognitive1,$cognitive2);
$sublist4 = array($environmental,$environmental1,$environmental2);
$sublist5 = array($ssa,$ssa1,$ssa2);
$sublist6 = array($medication,$medication1,$medication2);

$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;

$response["frassessmentlist"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6,);

/* $response["title1"] = "Nursing Care Plan"; */
$my_array = array("Nursing assessment","Nursing diagnosis","Planning","Implementation","Evaluation");
for($x = 0; $x < sizeof($my_array); $x++){	
	$response['nuursingcareplan'][$x]['list']=$my_array[$x];	
     }


$list = array();
$list["title"] = "Medication";
$list1["title"] = "Food";
$list2["title"] = "Others";

$sublist = array();
$medication = array("title1" => "Yes");
$medication1 = array("title1" => "No");
$medication2 = array("title1" => "Multi");
$food = array("title1" => "No");
$food1 = array("title1" => "Yes");
$food2 = array("title1" => "Multi");

$sublist = array($medication,$medication1,$medication2);
$sublist1 = array($food,$food1,$food2);

$list["sublist"] = $sublist;
$list1["sublist"] = $sublist1;

$response["allergielist"] = array($list,$list1,$list2);



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
     
     //"Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>