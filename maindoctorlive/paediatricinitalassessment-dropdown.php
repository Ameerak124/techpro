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
$response = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);

	http_response_code(200);
	//start
	$response = array(
    "error" => "false",
    "message" => "Data Found",
    //ADMISSION DATA
    "ADMISSIONDATA" => array(
        array(
            "title" => "Admission Mode",
            "admissionmode_list" => array(
                array("paediatricinital_title" => "Ambulatory"),
                array("paediatricinital_title" => "Wheel chair"),
                array("paediatricinital_title" => "Stretcher"),
                array("paediatricinital_title" => "Cuddled"),
                array("paediatricinital_title" => "Other")
            )
        ),
        array(
            "title" => "Information Obtained From",
            "admissionmode_list" => array(
                array("paediatricinital_title" => "Patient"),
                array("paediatricinital_title" => "Family/Friend"),
                array("paediatricinital_title" => "Other")
            )
        ),
        array(
            "title" => "Child Accompanied by",
            "admissionmode_list" => array(
                array("paediatricinital_title" => "Parents"),
                array("paediatricinital_title" => "Guardian"),
                array("paediatricinital_title" => "Other")
            )
        ),
        array(
            "title" => "Language Spoken",
            "admissionmode_list" => array(
                array("paediatricinital_title" => "Hindi"),
                array("paediatricinital_title" => "English"),
                array("paediatricinital_title" => "Telugu"),
                array("paediatricinital_title" => "Other")
            )
        ),
		
		),
			 "vitalsignslist" => array(
		        
                array("paediatricinital_title" => "Temperature", "units" => "OF"),
                array("paediatricinital_title" => "Respiration", "units" => "/min"),
                array("paediatricinital_title" => "Blood Pressure", "units" => ""),
                array("paediatricinital_title" => "Pulse", "units" => "/min"),
                array("paediatricinital_title" => "SPO2", "units" => "%"),
                array("paediatricinital_title" => "Weight", "units" => "kg"),
                array("paediatricinital_title" => "Height", "units" => "cm"),
                array("paediatricinital_title" => "BMI", "units" => "")
            ),
		        "orientationunitlist" => array(
		        
                array("paediatricinital_title" => "I.D. Band"),
                array("paediatricinital_title" => "Bathroom"),
                array("paediatricinital_title" => "Nurse Call"),
                array("paediatricinital_title" => "Meal Times"),
                array("paediatricinital_title" => "Visiting Hours"),
                array("paediatricinital_title" => "TV Control"),
                array("paediatricinital_title" => "Non-Smoking Policy"),
                array("paediatricinital_title" => "Patient equipment"),
                array("paediatricinital_title" => "Telephone"),
                array("paediatricinital_title" => "Fall Precautions (side rails, bed height, wet floor…etc.)"),
                array("paediatricinital_title" => "Parent/caregiver instructed to keep side rails up at all time unless immediately at patient bedside") 
            ),
		
		
		        "patientsrightslist" => array(
                array("paediatricinital_title" => "Patient"),
                array("paediatricinital_title" => "Parents"),
                array("paediatricinital_title" => "Other")
            ),
			
			    "valuables_equipmentlist" => array(
                array("paediatricinital_title" => "Given to Name"),
                array("paediatricinital_title" => "Relation")
            ),	 
    
        //HEALTH HISTORY
		        "currenthealthproblems" => array(
                array("paediatricinital_title" => "No Previous Problems"),
                array("paediatricinital_title" => "Bone Problem/Fracture"),
                array("paediatricinital_title" => "Swallowing problems"),
                array("paediatricinital_title" => "Arthritis/Neck Problem"),
                array("paediatricinital_title" => "Thyroid problem"),
                array("paediatricinital_title" => "Pneumonia"),
                array("paediatricinital_title" => "Blood Pressure Problems (high/low)"),
                array("paediatricinital_title" => "Ulcer/Rectal bleeding"),
                array("paediatricinital_title" => "Phlebitis/Clots"),
                array("paediatricinital_title" => "Kidney/Urinary problems"),
                array("paediatricinital_title" => "Difficulty breathing/lung disease"),
				array("paediatricinital_title" => "Congenital heart disease"),
				array("paediatricinital_title" => "Dizziness/fainting spells"),
				array("paediatricinital_title" => "Seizures"),
				array("paediatricinital_title" => "Asthma"),
				array("paediatricinital_title" => "Diabetes"),
				array("paediatricinital_title" => "Bleeding"),
				array("paediatricinital_title" => "Hepatitis/Jaundice"),
				array("paediatricinital_title" => "Cancer/Radiation/Chemotherapy"),
				array("paediatricinital_title" => " Mental illness"),
				array("paediatricinital_title" => "Present chronic cold/cough"),
				array("paediatricinital_title" => "Other")
            ),
			
		
		        "exposureinfectiousdiseases" => array(
                 array("paediatricinital_title" => "Chicken pox - Date"),
                array("paediatricinital_title" => "TB - Date"),
                array("paediatricinital_title" => "Pertussis - Date"),
                array("paediatricinital_title" => "Meningitis - Date"),
                array("paediatricinital_title" => "Influenza A - Date")
            ),
			
			  "immunizationlist" => array(
                array("paediatricinital_title" => "Up to date"),
                array("paediatricinital_title" => "Unsure"),
                array("paediatricinital_title" => "No")
            ),
			
		        "allergieslist" => array(
                array("paediatricinital_title" => "Medication"),
                array("paediatricinital_title" => "Food"),
                array("paediatricinital_title" => "Others"),
                array("paediatricinital_title" => "If any allergy, Allergy band applied"),
                array("paediatricinital_title" => "Yes"),
                array("paediatricinital_title" => "N/A")
            ),	
           
		        "dispositionofmedication" => array(
                array("paediatricinital_title" => "No medication brought to hospital"),
                array("paediatricinital_title" => "Sent to Pharmacy"),
                array("paediatricinital_title" => "To be verified by Clinical Pharmacologist for patient use on unit"),
                array("paediatricinital_title" => "Sent Home with")
            ),	
			    "sleeprestlist" => array(
                array("paediatricinital_title" => "Crib"),
                array("paediatricinital_title" => "Bed"),
                array("paediatricinital_title" => "Sleep alone"),
                array("paediatricinital_title" => "Sleep with parent or sibling"),
                array("paediatricinital_title" => "Good"),
                array("paediatricinital_title" => "Disturbed")
            ),	
			    "psychological_sociolist" => array(
                array("paediatricinital_title" => "No Problem Identified"),
                array("paediatricinital_title" => "Change in behavior"),
                array("paediatricinital_title" => "Hyperactive"),
                array("paediatricinital_title" => "Withdrawn"),
                array("paediatricinital_title" => "Depression"),
                array("paediatricinital_title" => "School anxiety"),
                array("paediatricinital_title" => "Nervousness"),
                array("paediatricinital_title" => "Anxiety"),
                array("paediatricinital_title" => "UncooperativeAngry"),
                array("paediatricinital_title" => "Agitated"),
                array("paediatricinital_title" => "Combative")
            ),	
			 "religiousassessmentlist" => array(
                array("paediatricinital_title" => "Islam","isedit" => "False"),
                array("paediatricinital_title" => "Christianity","isedit" => "False"),
                array("paediatricinital_title" => "Hinduism","isedit" => "False"),
                array("paediatricinital_title" => "Others(Specify)","isedit" => "True"),
                array("paediatricinital_title" => "Any other","isedit" => "True")
                /* array("paediatricinital_title" => "Are there any religious, cultural practices that need to be a part of your care?") */
            ),	
			"GROWTH_DEVELOPMENT" => array(
                "milestones_list" => array(
                array("paediatricinital_title" => "Social smile"),
                array("paediatricinital_title" => "Teething"),
                array("paediatricinital_title" => "Sat alone unsupported"),
                array("paediatricinital_title" => "unsupported"),
                array("paediatricinital_title" => "Walked alone"),
                array("paediatricinital_title" => "Used words"),
                array("paediatricinital_title" => "Used sentences"),
                array("paediatricinital_title" => "Toilet training"),
                array("paediatricinital_title" => "Puberty")
            )
            ),	
			   "painidentify_list" => array(
                array("paediatricinital_title" => "NIPS"),
                array("paediatricinital_title" => "FACES PAIN SCALE"),
                array("paediatricinital_title" => "FLACC"),
            ),
			
			//MISCELLANEOUS
			 
			"FUNCTIONAL_ASSESSMENT" => array(
                "category_list" => array(
                array("paediatricinital_title" => "Feeding"),
                array("paediatricinital_title" => "Toileting"),
                array("paediatricinital_title" => "Dressing"),
                array("paediatricinital_title" => "Grooming"),
                array("paediatricinital_title" => "Walking"),
                array("paediatricinital_title" => "Transfer"),
                array("paediatricinital_title" => "Mobility")
            )
            ),	
			
			"educationalassessment_list" => array(
                array("paediatricinital_title" => "No needs identified"),
                array("paediatricinital_title" => "Use of medication"),
                array("paediatricinital_title" => "Use of medical equipment"),
                array("paediatricinital_title" => "Potential interactions between medications and food"),
                array("paediatricinital_title" => "Diet and nutrition"),
                array("paediatricinital_title" => "Rehabilitation Techniques"),
                array("paediatricinital_title" => "Pain, and other symptoms management"),
                array("paediatricinital_title" => "Others")
            ),
			
			"BRADENSKINSCALE" => array(
        array(
            "title" => "Sensory Perception",
            "bradenscale_list" => array(
                array("paediatricinital_title" => "Stuporous Comatose"),
                array("paediatricinital_title" => "Confused"),
                array("paediatricinital_title" => "Apathetic"),
                array("paediatricinital_title" => "Alert")
            )
        ),
        array(
            "title" => "Moisture",
            "bradenscale_list" => array(
                array("paediatricinital_title" => "Constantly Moist"),
                array("paediatricinital_title" => "Very Moist"),
                array("paediatricinital_title" => "Occasionally Moist"),
                array("paediatricinital_title" => "Rarely Moist")
            )
        ),
        array(
            "title" => "Activity",
            "bradenscale_list" => array(
                array("paediatricinital_title" => "Bed ridden"),
                array("paediatricinital_title" => "Chair fast"),
                array("paediatricinital_title" => "Walks Occasionally"),
                array("paediatricinital_title" => "Walks frequently")
            )
        ),
        array(
            "title" => "Mobility",
            "bradenscale_list" => array(
                array("paediatricinital_title" => "Completely Immobile"),
                array("paediatricinital_title" => "Very Limited"),
                array("paediatricinital_title" => "Slightly limited"),
                array("paediatricinital_title" => "No Limitations")
            )
        ),
		array(
            "title" => "Nutrition",
            "bradenscale_list" => array(
                array("paediatricinital_title" => "Very poor"),
                array("paediatricinital_title" => "Probably Inadequate"),
                array("paediatricinital_title" => "Adequate"),
                array("paediatricinital_title" => "Excellent")
            )
        ),
		array(
            "title" => "Friction / Shear",
            "bradenscale_list" => array(
                array("paediatricinital_title" => "Problem"),
                array("paediatricinital_title" => "Potential Problem"),
                array("paediatricinital_title" => "No Apparent Problem")
            )
        ),
		
		),
		
		"HUMPTYDUMPTY_ASSESSMENT" => array(
        array(
            "title" => "Age",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "Less than 3 years old"),
                array("paediatricinital_title" => "3 to less than 7 years old"),
                array("paediatricinital_title" => "7 to less than 13 years old"),
                array("paediatricinital_title" => "13 years and above")
            )
        ),
        array(
            "title" => "Gender",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "Male"),
                array("paediatricinital_title" => "Female")
            )
        ),
        array(
            "title" => "Diagnosis",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "Neurological Diagnosis"),
                array("paediatricinital_title" => "Alterations in oxygenation (Respiratory Diagnosis, Dehydration, Anemia, Anorexia, Syncope/ Dizziness, Etc.)"),
                array("paediatricinital_title" => "Psych/ Behavioral Disorders"),
				array("paediatricinital_title" => "Other Diagnosis")
            )
        ),
        array(
            "title" => "Cognitive Impairments",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "Not aware of limitations"),
                array("paediatricinital_title" => "Forgets limitations"),
                array("paediatricinital_title" => "Oriented to own ability")
            )
        ),
		array(
            "title" => "Environmental Factors",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "History of falls or infant - Toddler placed in Bed"),
                array("paediatricinital_title" => "Patient uses assistive devices or infant - Toddler in Crib or furniture/ Lighting (Tripled Room)"),
                array("paediatricinital_title" => "Patient placed in bed")
            )
        ),
		array(
            "title" => "Response to surgery / Sedation/ Anesthesia",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "Within 24 hours"),
                array("paediatricinital_title" => "Within 48 hours"),
                array("paediatricinital_title" => "More than 48 Hours")
            )
        ),
		array(
            "title" => "Medication usage",
            "humptydumpty_list" => array(
                array("paediatricinital_title" => "Multiple use of sedatives (excluding ICU patients, sedated and paralyzed) Hypnotics, Barbiturates, Phenothiazines, Anti-depressants, Laxatives/Diuretics, Narcotics"),
                array("paediatricinital_title" => "One of the Meds listed above"),
                array("paediatricinital_title" => "Other Medications / None")
            )
        ),
		
		),
    //end	
);
     
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