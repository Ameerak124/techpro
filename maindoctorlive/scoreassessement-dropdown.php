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

if($status=='Fall Risk Assessment'){	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Fall Risk Assessment";
$response["displaytitle"] = "Fall Risk Assessment (Morse Fall Scale)";
$response["total"] = "Total";
$response["content"] = "No Risk :(0-24) Low Risk : (25-44) High Risk : > 45";

$listitem = array();
$listitem["title"] = "History of Fall";
$listitem1["title"] = "Secondary Diagnosis";
$listitem2["title"] = "Ambulatory Aid";
$listitem3["title"] = "IV or IV Acess";
$listitem4["title"] = "Gait";
$listitem5["title"] = "Mental Status";


$sublist = array();
$sublistitem1 = array("title1" => "No", "Count" => "0");
$sublistitem2 = array("title1" => "Yes", "Count" => "25");
$sublistitem3 = array("title1" => "No", "Count" => "0");
$sublistitem4 = array("title1" => "Yes", "Count" => "15");
$sublistitem5 = array("title1" => "None/bed rest/Nurse Assist", "Count" => "0");
$sublistitem6 = array("title1" => "Crutches/Cane/Walker", "Count" => "15");
$sublistitem7 = array("title1" => "Furniture", "Count" => "30");
$sublistitem8 = array("title1" => "No", "Count" => "0");
$sublistitem9 = array("title1" => "Yes", "Count" => "20");
$sublistitem10 = array("title1" => "Normal/Bed Rest/Wheel Chair", "Count" => "0");
$sublistitem11= array("title1" => "Weak", "Count" => "10");
$sublistitem12= array("title1" => "Impared", "Count" => "20");
$sublistitem13= array("title1" => "Oriented own Ability", "Count" => "0");
$sublistitem14= array("title1" => "Over estimates or forgets Limitations", "Count" => "15");

$sublist = array($sublistitem1,$sublistitem2);
$sublist1 = array($sublistitem3,$sublistitem4);
$sublist2 = array($sublistitem5,$sublistitem6,$sublistitem7);
$sublist3 = array($sublistitem8,$sublistitem9);
$sublist4 = array($sublistitem10,$sublistitem11,$sublistitem12);
$sublist5 = array($sublistitem13,$sublistitem14);

$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);
}else if($status=='Pain Score'){
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Behavioral Pain";
$response["displaytitle"] = "Pain Scale (Wong-Baker Pain Scale)";
$response["total"] = "Total Score";
$response["content"] = "No Risk :(0-24) Low Risk : (25-44) High Risk : > 45";

$listitem = array();
$listitem["title"] = "Facial Expression ";
$listitem1["title"] = "Upper limb movements";
$listitem2["title"] = "Compliance with Mechanical Ventilation";

$sublist = array();
$facial = array("title1" => "Relaxed", "Count" => "1");
$facial1 = array("title1" => "Partially Tightened(eg:Brow,Oweing)", "Count" => "2");
$facial2 = array("title1" => "Fully tightened(Eye lid closing)", "Count" => "3");
$facial3 = array("title1" => "Grimacing", "Count" => "4");
$upper = array("title1" => "No Movement", "Count" => "1");
$upper1 = array("title1" => "Partially bent", "Count" => "2");
$upper2 = array("title1" => "Fully bent with finger flexion", "Count" => "3");
$upper3 = array("title1" => "Permanently retracted", "Count" => "4");
$compliance = array("title1" => "Tolerating movement", "Count" => "1");
$compliance1 = array("title1" => "Coughing but tolerating", "Count" => "2");
$compliance2 = array("title1" => "Ventilation for the most of time fighting ventilator", "Count" => "3");
$compliance3 = array("title1" => "Unable to control the ventilation", "Count" => "4");


$sublist = array($facial,$facial1,$facial2,$facial3);
$sublist1 = array($upper,$upper1,$upper2,$upper3);
$sublist2 = array($compliance,$compliance1,$compliance2,$compliance3);

$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;

$response["list"] = array($listitem,$listitem1,$listitem2);
}else if($status=='Predicting Pressure Ulcer Risk'){
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Predicting Pressure Ulcer Risk";
$response["displaytitle"] = "Braden Scale For Pressure Ulcer Risk Score";
$response["total"] = "Total Score";
$response["content"] = "";
$response["doctorname"] = "Doctor name";
$response["remarks"] = "Remarks";

$listitem = array();
$listitem["title"] = "Sensory Perception";
$listitem1["title"] = "Moisture";
$listitem2["title"] = "Activity";
$listitem3["title"] = "Mobility";
$listitem4["title"] = "Nutrition";
$listitem5["title"] = "Friction And Shear";


$sublist = array();
$sensory = array("title1" => "Completely Limited", "Count" => "1");
$sensory1 = array("title1" => "Very Limited", "Count" => "2");
$sensory2 = array("title1" => "Slightly Limited", "Count" => "3");
$sensory3 = array("title1" => "NO Impairment", "Count" => "4");
$moisture = array("title1" => "Constantly Moist", "Count" => "1");
$moisture1 = array("title1" => "Moist", "Count" => "2");
$moisture2 = array("title1" => "Occasionally Moist", "Count" => "3");
$moisture3 = array("title1" => "Rarely Moist", "Count" => "4");
$activity = array("title1" => "Bedfast", "Count" => "1");
$activity1 = array("title1" => "Walks Occasionally", "Count" => "3");
$activity2 = array("title1" => "Chair Fast", "Count" => "2");
$activity3 = array("title1" => "Walks Frequently", "Count" => "4");
$mobility = array("title1" => "Completely Immobile", "Count" => "1");
$mobility1 = array("title1" => "Very Limited", "Count" => "2");
$mobility2 = array("title1" => "Slightly Limited", "Count" => "3");
$mobility3 = array("title1" => "No Limitations", "Count" => "4");
$nutrition = array("title1" => "Very Poor", "Count" => "1");
$nutrition1 = array("title1" => "Probably Inadequate", "Count" => "2");
$nutrition2 = array("title1" => "Adequate", "Count" => "3");
$nutrition3 = array("title1" => "Excellent", "Count" => "4");
$friction = array("title1" => "Problem", "Count" => "1");
$friction1 = array("title1" => "Potential Problem", "Count" => "2");
$friction2 = array("title1" => "No Apparent Problem", "Count" => "3");


$sublist = array($sensory,$sensory1,$sensory2,$sensory3);
$sublist1 = array($moisture,$moisture1,$moisture2,$moisture3);
$sublist2 = array($activity,$activity1,$activity2,$activity3);
$sublist3 = array($mobility,$mobility1,$mobility2,$mobility3);
$sublist4 = array($nutrition,$nutrition1,$nutrition2,$nutrition3);
$sublist5 = array($friction,$friction1,$friction2);

$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);	

}else if($status=='Daily Pressure Ulcer Risk'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Daily Pressure Ulcer Risk";
$response["displaytitle"] = "Pressure Ulcer Risk Score";
$response["list"] = "false";

	
}else if($status=='Glasgow Comma Scale'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Glasgow Comma Scale";
$response["displaytitle"] = "Glasgow Coma Scale";
$response["total"] = "Total Score";
$response["content"] = "";

$listitem = array();
$listitem["title"] = "Motor Response";
$listitem1["title"] = "Verbal Response";
$listitem2["title"] = "Eye Opening";

$sublist = array();
$motor = array("title1" => "No response to pain", "Count" => "1");
$motor1 = array("title1" => "Extensor posturing to pain", "Count" => "2");
$motor2 = array("title1" => "Abnormal flexor response to", "Count" => "3");
$motor3 = array("title1" => "Withdraws to pain", "Count" => "4");
$motor4 = array("title1" => "Localizing response to pain", "Count" => "5");
$motor5 = array("title1" => "Obeying Commands", "Count" => "6");
$verbal = array("title1" => "None", "Count" => "1");
$verbal1 = array("title1" => "Incomprehensible", "Count" => "2");
$verbal2 = array("title1" => "In-appropiate speech", "Count" => "3");
$verbal3 = array("title1" => "Confused conversation", "Count" => "4");
$verbal4 = array("title1" => "Orientated", "Count" => "5");
$verbal5 = array("title1" => "vₜ", "Count" => "0");
$eye = array("title1" => "No eye opening", "Count" => "1");
$eye1 = array("title1" => "Incomprehensible", "Count" => "2");
$eye2 = array("title1" => "Eye opening in response to speech", "Count" => "3");
$eye3 = array("title1" => "Spontaneous eye opening", "Count" => "4");

$sublist = array($motor,$motor1,$motor2,$motor3,$motor4,$motor5);
$sublist1 = array($verbal,$verbal1,$verbal2,$verbal3,$verbal4,$verbal5);
$sublist2 = array($eye,$eye1,$eye2,$eye3);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;

$response["list"] = array($listitem,$listitem1,$listitem2,);

}else if($status=='Sedation Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Sedation Score";
$response["displaytitle"] = "Sedation Score";
$response["list"] = "false";


}else if($status=='Mini Nutritional Assessment'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Mini Nutritional Assessment";
$response["displaytitle"] = "Nutritional Assessment";
$response["total"] = "Total Score";
$response["content"] = "High Risk : (0-8) Medium Risk : (8-11) No Risk : > (12-14)";

$listitem = array();
$listitem["title"] = "Unhealthy weight last 3 months";
$listitem1["title"] = "Mobility";
$listitem2["title"] = "Acute disease in last 3 months";
$listitem3["title"] = "Neuropsychological problem";
$listitem4["title"] = "Body habitus";
$listitem5["title"] = "Has food intake declined over the past 3 months due to lose of appetite, digestive problems, chewing or swallowing difficulties?";

$sublist = array();
$unhealthy = array("title1" => ">3 Kg", "Count" => "0");
$unhealthy1 = array("title1" => "Do not know", "Count" => "1");
$unhealthy2 = array("title1" => "1-3 Kg", "Count" => "2");
$unhealthy3 = array("title1" => "No loss", "Count" => "3");
$mobility = array("title1" => "Bed / chair bound", "Count" => "0");
$mobility1 = array("title1" => "Able to but does not", "Count" => "1");
$mobility2 = array("title1" => "Fully mobile", "Count" => "2");
$acute = array("title1" => "Yes", "Count" => "0");
$acute1 = array("title1" => "No", "Count" => "1");
$neuro = array("title1" => "Severe dementia", "Count" => "0");
$neuro1 = array("title1" => "Mild dementia", "Count" => "1");
$neuro2 = array("title1" => "None", "Count" => "2");
$body = array("title1" => "Cachectic / Morbid", "Count" => "0");
$body1 = array("title1" => "Obese", "Count" => "1");
$body2 = array("title1" => "Thin", "Count" => "2");
$body3 = array("title1" => "Average", "Count" => "3");
$food = array("title1" => "Severe", "Count" => "0");
$food1 = array("title1" => "Moderate", "Count" => "1");
$food2 = array("title1" => "No", "Count" => "2");


$sublist = array($unhealthy,$unhealthy1,$unhealthy2,$unhealthy3);
$sublist1 = array($mobility,$mobility1,$mobility2);
$sublist2 = array($acute,$acute1);
$sublist3 = array($neuro,$neuro1,$neuro2);
$sublist4 = array($body,$body1,$body2,$body3);
$sublist5 = array($food,$food1,$food2);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);		
		
}else if($status=='Sofa Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Sofa Score";
$response["displaytitle"] = "Sofa Score";
$response["total"] = "Total Score";
$response["content"] = "Risk(<10%) : (0-6) Risk(15-20%) : (7-9) Risk(40-50%) : (10-12) Risk(50-60%) : (13-14) Risk(>80%) : 15 Risk(>90%) : (15-24)";

$listitem = array();
$listitem["title"] = "Respirational PaO2/FlO2";
$listitem1["title"] = "Coagulation Platelets(1000)";
$listitem2["title"] = "Liver Billirubin (mg/dL)";
$listitem3["title"] = "Cardiovascular Hypotension(MCG/KG/MIN)";
$listitem4["title"] = "CNS Glasgow Coma Score";
$listitem2["title"] = "Renal Createinine (mg/dL) or urine output (mL/d)";

$sublist = array();
$respirational = array("title1" => ">400", "Count" => "0");
$respirational1 = array("title1" => "<400 221-301", "Count" => "1");
$respirational2 = array("title1" => "<300 142-220", "Count" => "2");
$respirational3 = array("title1" => "<200 67-141", "Count" => "3");
$respirational4 = array("title1" => "<100 <67", "Count" => "4");
$coagulation = array("title1" => ">150", "Count" => "0");
$coagulation1 = array("title1" => "<150", "Count" => "1");
$coagulation2 = array("title1" => "<100", "Count" => "2");
$coagulation3 = array("title1" => "<50", "Count" => "3");
$coagulation4 = array("title1" => "<20", "Count" => "4");
$liver = array("title1" => "<1.2", "Count" => "0");
$liver1 = array("title1" => "1.2-1.9", "Count" => "1");
$liver2 = array("title1" => "2.0-5.9", "Count" => "2");
$liver3 = array("title1" => "6.0-11.9", "Count" => "3");
$liver4 = array("title1" => ">12.0", "Count" => "4");
$cardiovascular = array("title1" => "No hypotension", "Count" => "0");
$cardiovascular1 = array("title1" => "MAP <70", "Count" => "1");
$cardiovascular2 = array("title1" => "Dopamine </=5 or dobutamine(any)", "Count" => "2");
$cardiovascular3 = array("title1" => "Dopamine>5 or norepinephrine </=0.1", "Count" => "3");
$cardiovascular4 = array("title1" => "Dopamine > 15 or norepinephrine >0.1", "Count" => "4");
$cns = array("title1" => "15", "Count" => "0");
$cns1 = array("title1" => "13-14", "Count" => "1");
$cns2 = array("title1" => "10-12", "Count" => "2");
$cns3 = array("title1" => "6-9", "Count" => "3");
$cns4 = array("title1" => "<6", "Count" => "4");
$renal= array("title1" => "<1.2", "Count" => "0");
$renal1 = array("title1" => "1.2-1.9", "Count" => "1");
$renal2 = array("title1" => "2.0-3.4", "Count" => "2");
$renal3 = array("title1" => "3.5-4.9 or <500", "Count" => "3");
$renal4 = array("title1" => ">5.0 or <200", "Count" => "4");


$sublist = array($respirational,$respirational1,$respirational2,$respirational3,$respirational4);
$sublist1 = array($coagulation,$coagulation1,$coagulation2,$coagulation3,$coagulation4);
$sublist2 = array($liver,$liver1,$liver2,$liver3,$liver4);
$sublist3 = array($cardiovascular,$cardiovascular1,$cardiovascular2,$cardiovascular3,$cardiovascular4);
$sublist4 = array($cns,$cns1,$cns2,$cns3,$cns4);
$sublist5 = array($renal,$renal1,$renal2,$renal3,$renal4);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);	

}else if($status=='Apache Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Apache Score";
$response["displaytitle"] = "Apache Score";
$response["list"] = "false";


}else if($status=='MEW Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "MEW Score";
$response["displaytitle"] = "MEW Scores";
$response["total"] = "Total Score";
$response["content"] = "";

$listitem = array();
$listitem["title"] = "L.O.C(AVPU) ( Mmlr )";

$sublist = array();
$loc = array("title1" => "Pain Unresponsive", "Count" => "2");
$loc1 = array("title1" => "Respond To Voice", "Count" => "1");
$loc2 = array("title1" => "Alert", "Count" => "0");
$loc3 = array("title1" => "New Agitation Confusion", "Count" => "1");
$loc4 = array("title1" => "None", "Count" => "3");

$sublist = array($loc,$loc1,$loc2,$loc3,$loc4);

$listitem["sublist"] = $sublist;

$response["list"] = array($listitem);	


}else if($status=='PADSS Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "PADSS Score";
$response["displaytitle"] = "PADSS Score";
$response["total"] = "Total Score";
$response["content"] = "No Risk : (0-24) Low Risk : (25-44) High Risk : > 45";

$listitem = array();
$listitem["title"] = "VITAL SIGNS";
$listitem1["title"] = "AMBULATION";
$listitem2["title"] = "NAUSEA AND VOMITING";
$listitem3["title"] = "PAIN";
$listitem4["title"] = "SURGICAL BLEEDING";

$sublist = array();
$vital = array("title1" => "Within 20% of the Perioperative Value", "Count" => "2");
$vital1 = array("title1" => "Within 20% -40% of the Perioperative Value", "Count" => "1");
$vital2 = array("title1" => "40% of the Pre operative Value", "Count" => "0");
$ambulation = array("title1" => "Steady Gait/No dizziness", "Count" => "2");
$ambulation1 = array("title1" => "With assistance", "Count" => "1");
$ambulation2 = array("title1" => "No Ambulation/Dizziness", "Count" => "0");
$nausea = array("title1" => "Minimal", "Count" => "2");
$nausea1 = array("title1" => "Moderate", "Count" => "1");
$nausea2= array("title1" => "Severe", "Count" => "0");
$pain = array("title1" => "Minimal", "Count" => "2");
$pain1  = array("title1" => "Moderate", "Count" => "1");
$pain2 = array("title1" => "Severe", "Count" => "0");
$surgical = array("title1" => "Minimal", "Count" => "2");
$surgical1  = array("title1" => "Moderate", "Count" => "1");
$surgical2 = array("title1" => "Severe", "Count" => "0");

$sublist = array($vital,$vital1,$vital2);
$sublist1 = array($ambulation,$ambulation1,$ambulation2);
$sublist2 = array($nausea,$nausea1,$nausea2);
$sublist3 = array($pain,$pain1,$pain2);
$sublist4 = array($surgical,$surgical1,$surgical2);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;


$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4);

}else if($status=='ALDRETE Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "ALDRETE Score";
$response["displaytitle"] = "ALDRETE Score";
$response["total"] = "Total Score";
$response["content"] = "";

$listitem = array();
$listitem["title"] = "Air way";
$listitem1["title"] = "Respiration";
$listitem2["title"] = "O2 Saturation";
$listitem3["title"] = "Consciousness";
$listitem4["title"] = "Systolic Bp";
$listitem5["title"] = "Cardiac rhythm";

$sublist = array();
$air = array("title1" => "Does not need airway", "Count" => "2");
$air1 = array("title1" => "Needs airway", "Count" => "1");
$air2 = array("title1" => "Needs and tolerates ETT", "Count" => "0");
$respiration = array("title1" => "Adequate tidal volume", "Count" => "2");
$respiration1 = array("title1" => "Dyspnoea / inadequate VT", "Count" => "1");
$Respiration2 = array("title1" => "No spontaneous respiration", "Count" => "0");
$o2 = array("title1" => "Maintain 92% on room Air", "Count" => "2");
$o21 = array("title1" => ">92%on O2 inhalation", "Count" => "1");
$o22 = array("title1" => ">92% on O2 inhalation", "Count" => "0");
$conscious = array("title1" => "Fully awake", "Count" => "2");
$conscious1 = array("title1" => "Arousable", "Count" => "1");
$conscious2 = array("title1" => "Not responding", "Count" => "0");
$bp = array("title1" => "SBP+/-20 mm / hg (of per op value)", "Count" => "2");
$bp1 = array("title1" => "SBP+/-20 to 40 mm’hg", "Count" => "1");
$bp2 = array("title1" => "SBP+/- >40 mm/hg", "Count" => "0");
$cardic = array("title1" => "Same as pre op", "Count" => "2");
$cardic1 = array("title1" => "New abnormality no Rx", "Count" => "1");
$cardic2 = array("title1" => "New abnormality – request Rx", "Count" => "0");


$sublist = array($air,$air1,$air2);
$sublist1 = array($respiration,$respiration1,$respiration2);
$sublist2 = array($o2,$o21,$o22);
$sublist3 = array($conscious,$conscious1,$conscious2);
$sublist4 = array($bp,$bp1,$bp2);
$sublist5 = array($cardic,$cardic1,$cardic2);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;


$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);	


}else if($status=='HAS-Bled'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "HAS-Bled";
$response["displaytitle"] = "HAS-Bled";
$response["total"] = "Total Score";
$response["content"] = "No Risk : > (0-3) Low Risk : (4-6) High Risk : (7-14)";

$listitem = array();
$listitem["title"] = "Alochol Use";
$listitem1["title"] = "Medication usage predisposing to bleeding";
$listitem2["title"] = "Age >=65";
$listitem3["title"] = "Labile INR";
$listitem4["title"] = "Prior major bleeding or predisposition to bleeding";
$listitem5["title"] = "Stroke history";
$listitem6["title"] = "Liver disease";
$listitem7["title"] = "Renal disease";
$listitem8["title"] = "Hypertension";

$sublist = array();
$alochol = array("title1" => "No", "Count" => "0");
$alochol1 = array("title1" => "Yes", "Count" => "1");
$medication = array("title1" => "No", "Count" => "0");
$medication1 = array("title1" => "Yes", "Count" => "1");
$age = array("title1" => "No", "Count" => "0");
$age1 = array("title1" => "Yes", "Count" => "1");
$labile = array("title1" => "No", "Count" => "0");
$labile1 = array("title1" => "Yes", "Count" => "1");
$prior = array("title1" => "No", "Count" => "0");
$prior1 = array("title1" => "Yes", "Count" => "1");
$stroke = array("title1" => "No", "Count" => "0");
$stroke1 = array("title1" => "Yes", "Count" => "1");
$liver = array("title1" => "No", "Count" => "0");
$liver1 = array("title1" => "Yes", "Count" => "1");
$renal = array("title1" => "No", "Count" => "0");
$renal1 = array("title1" => "Yes", "Count" => "1");
$tension = array("title1" => "No", "Count" => "0");
$tension1 = array("title1" => "Yes", "Count" => "1");


$sublist = array($alochol,$alochol1);
$sublist1 = array($medication,$medication1);
$sublist2 = array($age,$age1);
$sublist3 = array($labile,$labile1);
$sublist4 = array($prior,$prior1);
$sublist5 = array($stroke,$stroke1);
$sublist6 = array($liver,$liver1);
$sublist7 = array($renal,$renal1);
$sublist8 = array($tension,$tension1);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;
$listitem7["sublist"] = $sublist7;
$listitem8["sublist"] = $sublist8;


$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6,$listitem7,$listitem8);

}else if($status=='STEMI'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "STEMI";
$response["displaytitle"] = "STEMI";
$response["total"] = "Total Score";
$response["content"] = "No Risk : > (0-3) Low Risk : (4-6) High Risk : (7-14)";

$listitem = array();
$listitem["title"] = "Time to treatment >4 hours";
$listitem1["title"] = "Anterior ST Elevtion or LBBB";
$listitem2["title"] = "Weight > 67kg (147.7 lbs)";
$listitem3["title"] = "Killip Class II-IV";
$listitem4["title"] = "Heart rate >100";
$listitem5["title"] = "systolic BP > 100 mmHg";
$listitem6["title"] = "Diabetes , Hypertension or Angina";
$listitem7["title"] = "Age";


$sublist = array();
$time = array("title1" => "No", "Count" => "0");
$time1 = array("title1" => "Yes", "Count" => "1");
$anterior = array("title1" => "No", "Count" => "0");
$anterior1 = array("title1" => "Yes", "Count" => "1");
$weight = array("title1" => "No", "Count" => "0");
$weight1 = array("title1" => "Yes", "Count" => "1");
$killip = array("title1" => "No", "Count" => "0");
$killip1 = array("title1" => "Yes", "Count" => "2");
$heart = array("title1" => "No", "Count" => "0");
$heart1 = array("title1" => "Yes", "Count" => "2");
$systolic = array("title1" => "No", "Count" => "0");
$systolic1 = array("title1" => "Yes", "Count" => "3");
$diabetes = array("title1" => "No", "Count" => "0");
$diabetes1 = array("title1" => "Yes", "Count" => "1");
$age = array("title1" => ">65 year", "Count" => "0");
$age1 = array("title1" => "65-74", "Count" => "2");
$age2 = array("title1" => ">=75", "Count" => "3");


$sublist = array($time,$time1);
$sublist1 = array($anterior,$anterior1);
$sublist2 = array($weight,$weight1);
$sublist3 = array($killip,$killip1);
$sublist4 = array($heart,$heart1);
$sublist5 = array($systolic,$systolic1);
$sublist6 = array($diabetes,$diabetes1);
$sublist7 = array($age,$age1,$age2);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;
$listitem7["sublist"] = $sublist7;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6,$listitem7);

}else if($status=='UA/NSTEMI'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "UA/NSTEMI";
$response["displaytitle"] = "UA/NSTEMI";
$response["total"] = "Total Score";
$response["content"] = "No Risk : > (0-3) Low Risk : (4-6) High Risk : (7-14)";

$listitem = array();
$listitem["title"] = "Positive cardiac marker";
$listitem1["title"] = "EKG ST chares >= 0.5mm";
$listitem2["title"] = "Severe angina (>=2 episodes in 24hrs)";
$listitem3["title"] = "ASA use in past 7 days";
$listitem4["title"] = "Known CAD (stenosis >=50%)";
$listitem5["title"] = ">=3 CAD risk factors";
$listitem6["title"] = "Age >65";

$sublist = array();
$positive = array("title1" => "No", "Count" => "0");
$positive1 = array("title1" => "Yes", "Count" => "1");
$ekg = array("title1" => "No", "Count" => "0");
$ekg1 = array("title1" => "Yes", "Count" => "1");
$sever = array("title1" => "No", "Count" => "0");
$sever1 = array("title1" => "Yes", "Count" => "1");
$asa = array("title1" => "No", "Count" => "0");
$asa1 = array("title1" => "Yes", "Count" => "1");
$cad = array("title1" => "No", "Count" => "0");
$cad1 = array("title1" => "Yes", "Count" => "1");
$cadrisk = array("title1" => "No", "Count" => "0");
$cadrisk1 = array("title1" => "Yes", "Count" => "1");
$age = array("title1" => "No", "Count" => "0");
$age1 = array("title1" => "Yes", "Count" => "1");


$sublist = array($positive,$positive1);
$sublist1 = array($ekg,$ekg1);
$sublist2 = array($sever,$sever1);
$sublist3 = array($asa,$asa1);
$sublist4 = array($cad,$cad1);
$sublist5 = array($cadrisk,$cadrisk1);
$sublist6 = array($age,$age1);

$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6);

}else if($status=='PEWS'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "PEWS";
$response["displaytitle"] = "PEWS";
$response["total"] = "Total Score";
$response["content"] = "No Risk : (0-3) Low Risk : (4-6) High Risk : > 7";

$listitem = array();
$listitem["title"] = "Behaviour";
$listitem1["title"] = "Cardiovascular";
$listitem2["title"] = "Respiratory";


$sublist = array();
$behaviour = array("title1" => "Playing/appropriate or sleeping comfortably", "Count" => "0");
$behaviour1 = array("title1" => "Irritable and consolable", "Count" => "1");
$behaviour2 = array("title1" => "Irritable and not consolable", "Count" => "2");
$behaviour3 = array("title1" => "Lethargy confused or reduced response to pain", "Count" => "3");
$cardiovascular = array("title1" => "Pink or capillary refill time <1-2 sec", "Count" => "0");
$cardiovascular1 = array("title1" => "Pale or capillary refill time 3 sec", "Count" => "1");
$cardiovascular2 = array("title1" => "Grey capillary refill time 4 sec or heart rate 20 above or below normal for age", "Count" => "2");
$cardiovascular3 = array("title1" => "Grey, mottled capillary refill time>4sec or heart rate 30 above or below normal for age", "Count" => "3");
$respiratory = array("title1" => "Within normal rate, no retraction and Spo2 98-100% on RA", "Count" => "0");
$respiratory1 = array("title1" => "RR>10 above normal limits, or Spo2- 97% on any O2 device or using accessory muscles", "Count" => "1");
$respiratory2 = array("title1" => "RR>20 above normal limits or SPO2 90-93% on RA or retractions", "Count" => "2");
$respiratory3 = array("title1" => "RR <5 below normal or SPO2 < 90% or Retractions or Grunting", "Count" => "3");


$sublist = array($behaviour,$behaviour1,$behaviour2,$behaviour3);
$sublist1 = array($cardiovascular,$cardiovascular1,$cardiovascular2,$cardiovascular3);
$sublist2 = array($respiratory,$respiratory1,$respiratory2,$respiratory3);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;

$response["list"] = array($listitem,$listitem1,$listitem2);

}else if($status=='V.I.P Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "V.I.P Score";
$response["displaytitle"] = "V.I.P Score(Visual Infusion Phlebitis Score)";
$response["list"] = "false";


}else if($status=='DVT Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "DVT Score";
$response["total"] = "Total Score";
$response["content"] = "Low Risk : (0-10) Moderate Risk : (11-14) High Risk : >15";

$listitem = array();
$listitem["title"] = "AGE GROUP";
$listitem1["title"] = "MOBILITY";
$listitem2["title"] = "BUILD/BMI";
$listitem3["title"] = "SPECIAL RISK CATEGORY";
$listitem4["title"] = "TRAUMA(Score only pre-operative items)";
$listitem5["title"] = "SURGERY(Score only one appropriat items)";
$listitem6["title"] = "HIGH-RISK DISEASES(Score all items)";


$sublist = array();
$age = array("title1" => "10 to 30", "Count" => "0");
$age1 = array("title1" => "31 to 40", "Count" => "1");
$age2 = array("title1" => "41 to 50", "Count" => "2");
$age3 = array("title1" => "51 to 60", "Count" => "3");
$age4 = array("title1" => "61 to 70", "Count" => "4");
$age5 = array("title1" => ">70", "Count" => "5");
$mobility = array("title1" => "Ambulant", "Count" => "0");
$mobility1 = array("title1" => "Limited", "Count" => "1");
$mobility2 = array("title1" => "Very limited,needs help", "Count" => "2");
$mobility3 = array("title1" => "Chair Bound", "Count" => "3");
$mobility4 = array("title1" => "Complete bed rest", "Count" => "4");
$build = array("title1" => "Under weight BMI < 16-19", "Count" => "0");
$build1 = array("title1" => "BMI 20-25", "Count" => "1");
$build2 = array("title1" => "Over weight BMI 26-30", "Count" => "2");
$build3 = array("title1" => "Obese BMI 31-40", "Count" => "3");
$build4 = array("title1" => "Very obese BMI > 40", "Count" => "4");
$risk = array("title1" => "Oral contraceptive 20-35yrs", "Count" => "1");
$risk1 = array("title1" => "Oral contraceptive >35yrs", "Count" => "2");
$risk2 = array("title1" => "Hormone replacement therapy", "Count" => "2");
$risk3 = array("title1" => "Pregnacy & puerperium", "Count" => "3");
$risk4 = array("title1" => "Thrombophilia", "Count" => "4");
$trauma = array("title1" => "Head injury", "Count" => "1");
$trauma1 = array("title1" => "Chest injury", "Count" => "1");
$trauma2 = array("title1" => "Spinal injury", "Count" => "2");
$trauma3 = array("title1" => "Pelvic injury", "Count" => "3");
$trauma4 = array("title1" => "Lower limb injury", "Count" => "4");
$surgery = array("title1" => "N/A", "Count" => "0");
$surgery1 = array("title1" => "Minor surgery >30 mins", "Count" => "1");
$surgery2 = array("title1" => "Planned major surgery", "Count" => "2");
$surgery3 = array("title1" => "Emergency major surgery", "Count" => "3");
$surgery4 = array("title1" => "Thoracic", "Count" => "3");
$surgery5 = array("title1" => "Abdominal surgery", "Count" => "3");
$surgery6 = array("title1" => "Urological surgery", "Count" => "3");
$surgery7 = array("title1" => "Neurosurgery", "Count" => "3");
$surgery8 = array("title1" => "Orthopaedic surgery(below waist)", "Count" => "4");
$surgery9 = array("title1" => "Gynecological surgery", "Count" => "3");
$highrisk = array("title1" => "Ulcerative colitis", "Count" => "1");
$highrisk1 = array("title1" => "Polycythemia", "Count" => "2");
$highrisk2 = array("title1" => "Varicose veins", "Count" => "3");
$highrisk3 = array("title1" => "Chronic heart disease", "Count" => "3");
$highrisk4 = array("title1" => "Acute Myocardial infarction", "Count" => "4");
$highrisk5 = array("title1" => "Malignancy(active cancer)", "Count" => "5");
$highrisk6 = array("title1" => "CVA", "Count" => "6");
$highrisk7 = array("title1" => "Previous DVT", "Count" => "7");

$sublist = array($age,$age1,$age2,$age3,$age4,$age5);
$sublist1 = array($mobility,$mobility1,$mobility2,$mobility3,$mobility4);
$sublist2 = array($build,$build1,$build2,$build3,$build4);
$sublist3 = array($risk,$risk1,$risk2,$risk3,$risk4);
$sublist4 = array($trauma,$trauma1,$trauma2,$trauma3,$trauma4);
$sublist5 = array($surgery,$surgery1,$surgery2,$surgery3,$surgery4,$surgery5,$surgery6,$surgery7,$surgery8,$surgery9);
$sublist6 = array($highrisk,$highrisk1,$highrisk2,$highrisk3,$highrisk4,$highrisk5,$highrisk6,$highrisk7);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6);

}else if($status=='NEWS Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "NEWS Score";
$response["total"] = "Total Score";
$response["content"] = "No Risk : (1-4) Low Risk : (5-7) High Risk : >=8";
$response["informedto"] = "Informed To";

$listitem = array();
$listitem["title"] = "Temperature (°F )";
$listitem1["title"] = "Resp Rate ( Min )";
$listitem2["title"] = "Heart Rate (Bpm)";
$listitem3["title"] = "Spo2 (MmHg )";
$listitem4["title"] = "Neuro Score";
$listitem5["title"] = "Glucose";



$sublist = array();
$temp = array("title1" => ">38", "Count" => "3");
$temp1 = array("title1" => "37.5", "Count" => "2");
$temp2 = array("title1" => "37", "Count" => "2");
$temp3 = array("title1" => "36.5", "Count" => "1");
$temp4 = array("title1" => "36", "Count" => "1");
$temp5 = array("title1" => "<35.5", "Count" => "3");
$rrate = array("title1" => "<30", "Count" => "3");
$rrate1 = array("title1" => "40", "Count" => "1");
$rrate2 = array("title1" => "50", "Count" => "2");
$rrate3 = array("title1" => "60", "Count" => "2");
$rrate4 = array("title1" => "70", "Count" => "2");
$rrate5 = array("title1" => ">80", "Count" => "3");
$hrate = array("title1" => "<60", "Count" => "3");
$hrate1 = array("title1" => "<70", "Count" => "3");
$hrate2 = array("title1" => "80", "Count" => "2");
$hrate3 = array("title1" => "90", "Count" => "2");
$hrate4 = array("title1" => "100", "Count" => "1");
$hrate5 = array("title1" => "110", "Count" => "1");
$hrate6 = array("title1" => "120", "Count" => "1");
$hrate7 = array("title1" => "130", "Count" => "1");
$hrate8 = array("title1" => "140", "Count" => "1");
$hrate9 = array("title1" => "150", "Count" => "2");
$hrate10 = array("title1" => "160", "Count" => "2");
$hrate11 = array("title1" => "170", "Count" => "2");
$hrate12 = array("title1" => ">180", "Count" => "3");
$hrate13 = array("title1" => ">190", "Count" => "3");
$spo = array("title1" => ">=94", "Count" => "1");
$spo1 = array("title1" => "93-91", "Count" => "3");
$spo2 = array("title1" => "<=90", "Count" => "2");
$neuro = array("title1" => "Alert", "Count" => "1");
$neuro1 = array("title1" => "Irritable", "Count" => "2");
$neuro2 = array("title1" => "Jittery", "Count" => "2");
$neuro3 = array("title1" => "Poor Feed", "Count" => "2");
$neuro4 = array("title1" => "Floppy", "Count" => "3");
$neuro5 = array("title1" => "Seizures", "Count" => "3");
$glucose = array("title1" => "<2.6", "Count" => "3");


$sublist = array($temp,$temp1,$temp2,$temp3,$temp4,$temp5);
$sublist1 = array($rrate,$rrate1,$rrate2,$rrate3,$rrate4,$rrate5);
$sublist2 = array($hrate,$hrate1,$hrate2,$hrate3,$hrate4,$hrate5,$hrate6,$hrate7,$hrate8,$hrate9,$hrate10,$hrate11,$hrate12,$hrate13);
$sublist3 = array($spo,$spo1,$spo2);
$sublist4 = array($neuro,$neuro1,$neuro2,$neuro3,$neuro4,$neuro5);
$sublist5 = array($glucose);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);

}else if($status=='MEOWS Score'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "MEOWS Score";
$response["total"] = "Total Score";
$response["content"] = "No Risk : (0- 3) Low Risk : (4-6) High Risk : 8-≥8";

$my_array = array("Morning to Afternoon","Afternoon to Evening","Evening to Night","Night to Morning",);

$listitem = array();
$listitem["title"] = "Resp Rate ( Min )";
$listitem1["title"] = "SP02 ( MmHg )";
$listitem2["title"] = "Temperature (°F )";
$listitem3["title"] = "Systolic BP ( MmHg )";
$listitem4["title"] = "Diastolic BP ( MmHg )";
$listitem5["title"] = "Heart Rate ( Bpm )";
$listitem6["title"] = "Neurological Response";
$listitem7["title"] = "Pain Score";
$listitem8["title"] = "Protenuria";
$listitem9["title"] = "Liquor";
$listitem10["title"] = "Lochia";

$sublist = array();
$rrate = array("title1" => ">=30", "Count" => "3");
$rrate1 = array("title1" => "26-30", "Count" => "2");
$rrate2 = array("title1" => "21-25", "Count" => "1");
$rrate3 = array("title1" => "11-20", "Count" => "0");
$rrate4 = array("title1" => "<=10", "Count" => "3");
$spo = array("title1" => "95-100", "Count" => "0");
$spo1 = array("title1" => "91-94", "Count" => "3");
$spo2 = array("title1" => "<=90", "Count" => "3");
$temp = array("title1" => ">=39", "Count" => "2");
$temp1 = array("title1" => "38-38.9", "Count" => "1");
$temp2 = array("title1" => "37-37.9", "Count" => "0");
$temp3 = array("title1" => "36-36.9", "Count" => "0");
$temp4 = array("title1" => "35-35.9", "Count" => "1");
$temp5 = array("title1" => "<=34.9", "Count" => "2");
$systolic = array("title1" => ">=200", "Count" => "3");
$systolic1 = array("title1" => "160-199", "Count" => "3");
$systolic2 = array("title1" => "150-159", "Count" => "2");
$systolic3 = array("title1" => "140-149", "Count" => "1");
$systolic4 = array("title1" => "100-140", "Count" => "0");
$systolic5 = array("title1" => "90-99", "Count" => "1");
$systolic6 = array("title1" => "70-89", "Count" => "2");
$systolic7 = array("title1" => "50-60", "Count" => "3");
$systolic8 = array("title1" => "<=49", "Count" => "3");
$diastolic = array("title1" => ">=130", "Count" => "3");
$diastolic1 = array("title1" => "110-129", "Count" => "3");
$diastolic2 = array("title1" => "100-109", "Count" => "2");
$diastolic3 = array("title1" => "90-99", "Count" => "1");
$diastolic4 = array("title1" => "50-89", "Count" => "0");
$diastolic5 = array("title1" => "40-49", "Count" => "1");
$diastolic6 = array("title1" => "<=39", "Count" => "1");
$hrate = array("title1" => ">=170", "Count" => "0");
$hrate1 = array("title1" => "130-169", "Count" => "3");
$hrate2 = array("title1" => "110-129", "Count" => "2");
$hrate3 = array("title1" => "100-109", "Count" => "1");
$hrate4 = array("title1" => "60-99", "Count" => "0");
$hrate5 = array("title1" => "40-59", "Count" => "1");
$hrate6 = array("title1" => "<=39", "Count" => "2");
$neuro = array("title1" => "Select");
$neuro1 = array("title1" => "Alert");
$neuro2 = array("title1" => "Voice");
$neuro3 = array("title1" => "Unresponsive");
$neuro4 = array("title1" => "Pain");
$pscore = array("title1" => "Select");
$pscore1 = array("title1" => "3");
$pscore2 = array("title1" => "1-2");
$pscore3 = array("title1" => "0");
$protenuria = array("title1" => "Select");
$protenuria1 = array("title1" => "-2+");
$protenuria2 = array("title1" => ">=2+");
$liquor = array("title1" => "Select");
$liquor1 = array("title1" => "Clear");
$liquor2 = array("title1" => "Pink");
$liquor3 = array("title1" => "Green");
$lochia = array("title1" => "Select");
$lochia1 = array("title1" => "Normal");
$lochia2 = array("title1" => "Heavy");
$lochia3 = array("title1" => "Foul");

$sublist = array($rrate,$rrate1,$rrate2,$rrate3,$rrate4);
$sublist1 = array($spo,$spo1,$spo2);
$sublist2 = array($temp,$temp1,$temp2,$temp3,$temp4,$temp5);
$sublist3 = array($systolic,$systolic1,$systolic2,$systolic3,$systolic4,$systolic5,$systolic6,$systolic7,$systolic8);
$sublist4 = array($diastolic,$diastolic1,$diastolic2,$diastolic3,$diastolic4,$diastolic5,$diastolic6);
$sublist5 = array($hrate,$hrate1,$hrate2,$hrate3,$hrate4,$hrate5,$hrate6);
$sublist6 = array($neuro,$neuro1,$neuro2,$neuro3,$neuro4);
$sublist7 = array($pscore,$pscore1,$pscore2,$pscore3);
$sublist8 = array($protenuria,$protenuria1,$protenuria2);
$sublist9 = array($liquor,$liquor1,$liquor2,$liquor3);
$sublist10 = array($lochia,$lochia1,$lochia2,$lochia3);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;
$listitem7["sublist"] = $sublist7;
$listitem8["sublist"] = $sublist8;
$listitem9["sublist"] = $sublist9;
$listitem10["sublist"] = $sublist10;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6,$listitem7,$listitem8,$listitem9,$listitem10);

}else if($status=='Apache II'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Apache II";
$response["displaytitle"] = "Apache II";
$response["total"] = "Total Score";
$response["content"] = "";

$listitem = array();
$listitem["title"] = "Age, years";
$listitem1["title"] = "History of severe organ insufficiency or immunocompromised";
$listitem2["title"] = "Rectal temperature, °C";
$listitem3["title"] = "Mean arterial pressure, mmHg";
$listitem4["title"] = "Heart rate,beats per minute";
$listitem5["title"] = "Respiratory rate,breats per minute";
$listitem6["title"] = "Oxygenation(use PaO2 if FiO2 <50%,otherwise use A-a gradient)";
$listitem7["title"] = "Arterial pH";
$listitem8["title"] = "Serum sodium,mmol/L";
$listitem9["title"] = "Serum potassium, mmol/L";
$listitem10["title"] = "Serum creatinine, mg/100 mL";
$listitem11["title"] = "Hematocrit%";
$listitem12["title"] = "White blood count,total/cubic mm in 1000's";
$listitem13["title"] = "Glasgow Coma Scale (GCS)";



$sublist = array();
$age = array("title1" => "<=44", "Count" => "0");
$age1 = array("title1" => "45-54", "Count" => "2");
$age2 = array("title1" => "55-64", "Count" => "3");
$age3 = array("title1" => "65-74", "Count" => "5");
$age4 = array("title1" => ">74", "Count" => "6");
$age5 = array("title1" => "<35.5", "Count" => "3");
$severe = array("title1" => "Yes, and nonoperative or emergency postoperative patient", "Count" => "5");
$severe1 = array("title1" => "Yes, and elective postoperative patient", "Count" => "2");
$severe2 = array("title1" => "No", "Count" => "0");
$rectal = array("title1" => ">=41", "Count" => "4");
$rectal1 = array("title1" => "39 to <41", "Count" => "3");
$rectal2 = array("title1" => "38.5 to<39", "Count" => "1");
$rectal3 = array("title1" => "36 to <38.5", "Count" => "0");
$rectal4 = array("title1" => "34 to <36", "Count" => "1");
$rectal5 = array("title1" => "32 to <34", "Count" => "2");
$rectal6 = array("title1" => "30 to <32", "Count" => "3");
$rectal7 = array("title1" => "<30", "Count" => "4");
$arterial = array("title1" => ">159", "Count" => "4");
$arterial1 = array("title1" => ">129-159", "Count" => "3");
$arterial2 = array("title1" => ">109-129", "Count" => "2");
$arterial3 = array("title1" => ">69-109", "Count" => "0");
$arterial4 = array("title1" => ">49-69", "Count" => "2");
$arterial5 = array("title1" => "<=49", "Count" => "4");
$hrate = array("title1" => ">=180", "Count" => "4");
$hrate1 = array("title1" => "140 to <180", "Count" => "3");
$hrate2 = array("title1" => "110 to <140", "Count" => "2");
$hrate3 = array("title1" => "70 to <110", "Count" => "0");
$hrate4 = array("title1" => "55 to <70", "Count" => "2");
$hrate5 = array("title1" => "40 to <55", "Count" => "3");
$hrate6 = array("title1" => "<40", "Count" => "4");
$rrate = array("title1" => ">=50", "Count" => "4");
$rrate1 = array("title1" => "35 to <50", "Count" => "3");
$rrate2 = array("title1" => "25 to <35", "Count" => "1");
$rrate3 = array("title1" => "12 to <25", "Count" => "0");
$rrate4 = array("title1" => "10 to <12", "Count" => "1");
$rrate5 = array("title1" => "6 to <10", "Count" => "2");
$rrate6 = array("title1" => "<6", "Count" => "4");
$oxygen = array("title1" => "A-a gradient >499", "Count" => "4");
$oxygen1 = array("title1" => "A-a gradient 350-499", "Count" => "3");
$oxygen2 = array("title1" => "A-a gradient 200-349", "Count" => "2");
$oxygen3 = array("title1" => "A-a gradient <200 (if FiO2 over 49%) or pO2 >70 (if FiO2 less than 50%)", "Count" => "0");
$oxygen4 = array("title1" => "PaO2 = 61-70", "Count" => "1");
$oxygen5 = array("title1" => "PaO2 = 55-60", "Count" => "3");
$oxygen6 = array("title1" => "PaO2 <55", "Count" => "4");
$arterialph = array("title1" => ">=7.70", "Count" => "4");
$arterialph1 = array("title1" => "7.60 to <7.70", "Count" => "3");
$arterialph2 = array("title1" => "7.50 to <7.60", "Count" => "1");
$arterialph3 = array("title1" => "7.33 to <7.50", "Count" => "0");
$arterialph4 = array("title1" => "7.25 to <7.33", "Count" => "2");
$arterialph5 = array("title1" => "7.15 to <7.25", "Count" => "3");
$arterialph6 = array("title1" => "<7.15", "Count" => "4");
$sodium = array("title1" => ">=180", "Count" => "4");
$sodium1 = array("title1" => "160 to <180", "Count" => "3");
$sodium2 = array("title1" => "155 to <160", "Count" => "2");
$sodium3 = array("title1" => "150 to <155", "Count" => "1");
$sodium4 = array("title1" => "130 to <150", "Count" => "4");
$sodium5 = array("title1" => "120 to <130", "Count" => "5");
$sodium6 = array("title1" => "111 to <120", "Count" => "6");
$sodium7 = array("title1" => "<111", "Count" => "7");
$potassium = array("title1" => ">=7.0", "Count" => "4");
$potassium1 = array("title1" => "6.0 to <7.0", "Count" => "3");
$potassium2 = array("title1" => "5.5 to <6.0", "Count" => "1");
$potassium3 = array("title1" => "3.5 to <5.5", "Count" => "0");
$potassium4 = array("title1" => "3.0 to <3.5", "Count" => "1");
$potassium5 = array("title1" => "2.5 to <3.0", "Count" => "2");
$potassium6 = array("title1" => "<2.5", "Count" => "4");
$creatinine = array("title1" => ">=3.5 and ACUTE renal failure*", "Count" => "8");
$creatinine1 = array("title1" => "2.0 to <3.5 and ACUTE renal failure", "Count" => "6");
$creatinine2 = array("title1" => ">=3.5 and CHRONIC renal failure", "Count" => "4");
$creatinine3 = array("title1" => "1.5 to <2.0 and ACUTE renal failure", "Count" => "4");
$creatinine4 = array("title1" => "2.0 to <3.5 and CHRONIC renal failure", "Count" => "3");
$creatinine5 = array("title1" => "1.5 to <2.0 and CHRONIC renal failure", "Count" => "2");
$creatinine6 = array("title1" => "<0.6", "Count" => "2");
$creatinine7 = array("title1" => "0.6 to <1.5", "Count" => "0");
$hematocrit = array("title1" => ">=60", "Count" => "4");
$hematocrit1 = array("title1" => "50 to <60", "Count" => "2");
$hematocrit2 = array("title1" => "46 to <50", "Count" => "1");
$hematocrit3 = array("title1" => "30 to <46", "Count" => "0");
$hematocrit4 = array("title1" => "20 to <30", "Count" => "2");
$hematocrit5 = array("title1" => "<20", "Count" => "4");
$blood = array("title1" => ">=40", "Count" => "4");
$blood1 = array("title1" => "20 to <40", "Count" => "2");
$blood2 = array("title1" => "15 to <20", "Count" => "1");
$blood3 = array("title1" => "3 to <15", "Count" => "0");
$blood4 = array("title1" => "1 to <3", "Count" => "2");
$blood5 = array("title1" => "<1", "Count" => "4");
$glasgow  = array("title1" => "1", "Count" => "1");
$glasgow1 = array("title1" => "2", "Count" => "2");
$glasgow2= array("title1" => "3", "Count" => "3");
$glasgow3 = array("title1" => "4", "Count" => "4");
$glasgow4= array("title1" => "5", "Count" => "5");
$glasgow5 = array("title1" => "6", "Count" => "6");
$glasgow6 = array("title1" => "7", "Count" => "7");
$glasgow7 = array("title1" => "8", "Count" => "8");
$glasgow8 = array("title1" => "9", "Count" => "9");
$glasgow9 = array("title1" => "10", "Count" => "10");
$glasgow10 = array("title1" => "11", "Count" => "11");
$glasgow11 = array("title1" => "12", "Count" => "12");
$glasgow12 = array("title1" => "13", "Count" => "13");
$glasgow13 = array("title1" => "14", "Count" => "14");
$glasgow14 = array("title1" => "15", "Count" => "15");


$sublist = array($age,$age1,$age2,$age3,$age4,$age5);
$sublist1 = array($severe,$severe1,$severe2);
$sublist2 = array($rectal,$rectal1,$rectal2,$rectal3,$rectal4,$rectal5,$rectal6,$rectal7);
$sublist3 = array($arterial,$arterial1,$arterial2,$arterial3,$arterial4,$arterial5);
$sublist4 = array($hrate,$hrate1,$hrate2,$neuro2,$hrate3,$hrate4,$hrate5,$hrate6);
$sublist5 = array($rrate,$rrate1,$rrate2,$rrate3,$rrate4,$rrate5,$rrate6);
$sublist6 = array($oxygen,$oxygen1,$oxygen2,$oxygen3,$oxygen4,$oxygen5,$oxygen6);
$sublist7 = array($arterialph,$arterialph1,$arterialph2,$arterialph3,$arterialph4,$arterialph5,$arterialph6);
$sublist8 = array($sodium,$sodium1,$sodium2,$sodium3,$sodium4,$sodium5,$sodium6,$sodium7);
$sublist9 = array($potassium,$potassium1,$potassium2,$potassium3,$potassium4,$potassium5,$potassium6);
$sublist10 = array($creatinine,$creatinine1,$creatinine2,$creatinine3,$creatinine4,$creatinine5,$creatinine6,$creatinine7);
$sublist11 = array($hematocrit,$hematocrit1,$hematocrit2,$hematocrit3,$hematocrit4,$hematocrit5);
$sublist12 = array($blood,$blood1,$blood2,$blood3,$blood4,$blood5);
$sublist13 = array($glasgow,$glasgow1,$glasgow2,$glasgow3,$glasgow4,$glasgow5,$glasgow6,$glasgow7,$glasgow8,$glasgow9,$glasgow10,$glasgow11,$glasgow12,  $glasgow13,$glasgow14);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;
$listitem6["sublist"] = $sublist6;
$listitem7["sublist"] = $sublist7;
$listitem8["sublist"] = $sublist8;
$listitem9["sublist"] = $sublist9;
$listitem10["sublist"] = $sublist10;
$listitem11["sublist"] = $sublist11;
$listitem12["sublist"] = $sublist12;
$listitem13["sublist"] = $sublist13;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5,$listitem6,$listitem7,$listitem8,$listitem9,$listitem10,$listitem11,$listitem12,$listitem13);

}else if($status=='VENOUS THROMBOEMBOLISM RISK FACTOR ASSESSMENT'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "VENOUS THROMBOEMBOLISM RISK FACTOR ASSESSMENT";
$response["displaytitle"] = "VENOUS THROMBOEMBOLISM RISK FACTOR ASSESSMENT";
$response["total"] = "Total Score";
$response["content"] = "No Risk : (0-3) Low Risk : (4-6) High Risk : > 7";

$listitem = array();
$listitem["title"] = "Age(years)";
$listitem1["title"] = "Type of surgery";
$listitem2["title"] = "Recent(<1 month)event";
$listitem3["title"] = "Venous disease or clotting disorder";
$listitem4["title"] = "Mobility";
$listitem5["title"] = "Other present and past history";



$sublist = array();
$age = array("title1" => "<=40", "Count" => "0");
$age1 = array("title1" => "41-60", "Count" => "1");
$age2 = array("title1" => "61-74", "Count" => "2");
$age3 = array("title1" => "=75", "Count" => "3");
$surgery = array("title1" => "Minor", "Count" => "1");
$surgery1 = array("title1" => "Major >45 min,laparoscopic >45 min,arthroscopic", "Count" => "2");
$surgery2 = array("title1" => "Elective major lower extremity arthroplasty", "Count" => "5");
$recent = array("title1" => "None", "Count" => "0");
$recent1 = array("title1" => "Major surgery,CHF,sepsis,pneumonia,pregnancy or postpartum(if female)", "Count" => "1");
$recent2 = array("title1" => "Immobilizing plaster cast", "Count" => "2");
$recent3 = array("title1" => "Hip,pelvis,or leg fracture;stroke;multiple trauma;acute spinal cord injury causing paralysis", "Count" => "5");
$venous = array("title1" => "None", "Count" => "0");
$venous1 = array("title1" => "Varicose veins,current swollen legs", "Count" => "1");
$venous2 = array("title1" => "Current central venous access", "Count" => "2");
$venous3 = array("title1" => "History of DVT/PE,family history of thrombosis,positive Factor V Leiden,positive prothrombin 20210A,elevated serum homocysteine,
 positive lupus anticoagulant,elevated anticardiolipin antibody,heparin-induced thrombocytopenia,other congenital or acquired thrombophilia", "Count" => "3");
$mobility = array("title1" => "Normal, out of bed", "Count" => "0");
$mobility1 = array("title1" => "Medical patient currently on bed rest", "Count" => "1");
$mobility2 = array("title1" => "Patient confined to bed > 72 hours", "Count" => "2");
$presentpast = array("title1" => "None", "Count" => "0");
$presentpast1 = array("title1" => "History of inflammatory bowel disease, BMI >25, Acute MI, COPD, other risk factors,on oral contraceptives or hormone replacement(if female),history of unexplained stillborn,>=3 spontaneous abortions,or premature birth with toxemia or growth-restricted infant (if female)", "Count" => "1");
$presentpast2 = array("title1" => "Present or previous Malignancy", "Count" => "2");

$sublist = array($age,$age1,$age2,$age3);
$sublist1 = array($surgery,$surgery1,$surgery2);
$sublist2 = array($recent,$recent1,$recent2,$recent3);
$sublist3 = array($venous,$venous1,$venous2,$venous3);
$sublist4 = array($mobility,$mobility1,$mobility2);
$sublist5 = array($presentpast,$presentpast1,$presentpast2);


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["list"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);

}else if($status=='MALNUTRITION SCREENING TOOL(MST)'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "MALNUTRITION SCREENING TOOL(MST)";
$response["displaytitle"] = "MALNUTRITION SCREENING TOOL(MST)";
$response["total"] = "Total Score";
$response["content"] = "Low : > (0-3) Moderate : (2) High : (3-7)";

$listitem = array();
$listitem["title"] = "Have you/the client lost weight recently without trying?";
$listitem1["title"] = "If yes how much weight patient has lost ?";
$listitem2["title"] = "Have you/the client been eating poorly because of a decreased appetite?";




$sublist = array();
$client = array("title1" => "No", "Count" => "0");
$client1 = array("title1" => "UNSURE", "Count" => "2");
$patient = array("title1" => "0.5-5kg", "Count" => "1");
$patient1 = array("title1" => ">5-10kg", "Count" => "2");
$patient2 = array("title1" => ">10-15kg", "Count" => "3");
$patient3 = array("title1" => ">15kg", "Count" => "4");
$patient4 = array("title1" => "UNSURE", "Count" => "2");
$appetite = array("title1" => "No", "Count" => "0");
$appetite1 = array("title1" => "Yes", "Count" => "1");


$sublist = array($client,$client1);
$sublist1 = array($patient,$patient1,$patient2,$patient3,$patient4);
$sublist2 = array($appetite,$appetite1);



$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;


$response["list"] = array($listitem,$listitem1,$listitem2);

}else if($status=='NUTRIC SCORE'){
	
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "NUTRIC SCORE";
$response["displaytitle"] = "NUTRIC SCORE VARIABLES";
$response["total"] = "Total Score";
$response["content"] = "";

$listitem = array();
$listitem["title"] = "AGE";
$listitem1["title"] = "APACHE II";
$listitem2["title"] = "NUMBER OF CO- MORBIDITIES";
$listitem3["title"] = "DAYS FROM HOSPITAL TO ICU ADMISSION";




$sublist = array();
$age = array("title1" => "<50Y", "Count" => "0");
$age1 = array("title1" => "50-<75Y", "Count" => "1");
$age2 = array("title1" => ">75Y", "Count" => "2");
$morbidities = array("title1" => ">15", "Count" => "0");
$morbidities1 = array("title1" => "15- <20", "Count" => "1");
$morbidities2 = array("title1" => "20-28", "Count" => "2");
$morbidities3 = array("title1" => ">28", "Count" => "3");
$morbidities4 = array("title1" => "<6", "Count" => "0");
$morbidities5 = array("title1" => "6- <10", "Count" => "1");
$morbidities6 = array("title1" => ">10", "Count" => "2");
$admission = array("title1" => "1-<1", "Count" => "0");
$admission1 = array("title1" => ">1", "Count" => "1");


$sublist = array($age,$age1,$age2);
$sublist1 = array($morbidities,$morbidities1,$morbidities2,$morbidities3,$morbidities4,$morbidities5,$morbidities6);
$sublist2 = array($admission,$admission1);



$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;


$response["list"] = array($listitem,$listitem1,$listitem2);
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
     
     //"Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>