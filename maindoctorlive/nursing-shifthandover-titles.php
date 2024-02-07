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
    "error" => false,
    "message" => "Data Found",     
    "data"=>array( array(
    "main_title"=>"Bed side",
            "sub_list" => array(
                array( "title" => "Hand Off & Receiving Nurse Together Visited the Patient","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
                
                array( "title" => "Informed the Patient regarding the shift & shift Change","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
                
                array("title" => "Identified the Patient name & UMR NO","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
              
                array("title" => "Quick Assessment Of Patient,Lines,Tubes,IV Fluid Done","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
              
                array( "title" => "Stock Medication Identified & Kept Under Lock","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"))),

    array("main_title"=>"Verification With Documentation",
    "sub_list" => array(
                array( "title" => "Handling Over & Receiving Done on ISBAR format","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
                array("title" => "Admitted Back Ground & Current Situation Explained","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
                array("title" => "Current Vitals Signs Verified","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
                array("title" => "Current Condition And Discharge Plan Explained","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"),
                array("title" => "All Drug & Non-Drug Orders Explained and verified","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"true"))),

 array("main_title"=>"", 
"sub_list" => array(
array("title" => "Any New Orders Or Pending Orders Identified","radio_button"=>"false","edit_text"=>"true"),
                                                
array("title" => "Any New Lab Investigations Or Pending Reports","radio_button"=>"false","edit_text"=>"true"),
array("title" => "Any Critical value/Positive Or Culture/Isolation Requirements","radio_button"=>"false","edit_text"=>"true"),
array("title" => "Nursing Documents Up-to Date & NCP Updated","title1" => "Yes","title2" => "No","radio_button"=>"true","edit_text"=>"false"))),
array("main_title"=>"",
"sub_list" => array( 
array("title" => "Remarks","radio_button"=>"false","edit_text"=>"true"))),
array("main_title"=>"",
"sub_list" => array( 
 array("title" => "Handover By Nurse Name","radio_button"=>"false","edit_text"=>"true"),
 array("title" => "Taken Over By Nurse Name","radio_button"=>"false","edit_text"=>"true"))),
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
}
echo json_encode($response);
$pdoread = null;
?>