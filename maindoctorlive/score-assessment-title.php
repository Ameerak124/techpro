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
		
		  
		$my_array = array("Fall Risk Assessment","Pain Score","Predicting Pressure Ulcer Risk","Daily Pressure Ulcer Risk","Glasgow Comma Scale","Sedation Score","Mini Nutritional Assessment","Sofa Score","Apache Score","MEW Score","PADSS Score","ALDRETE Score","HAS-Bled","STEMI","UA/NSTEMI","PEWS","V.I.P Score","DVT Score","NEWS Score","MEOWS Score","Apache II","VENOUS THROMBOEMBOLISM RISK FACTOR ASSESSMENT","MALNUTRITION SCREENING TOOL(MST)","NUTRIC SCORE");
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    for($x = 0; $x < sizeof($my_array); $x++){	
	$response['scoreassessmentlist'][$x]['title']=$my_array[$x];	
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