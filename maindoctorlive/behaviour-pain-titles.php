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
$head = $data->head;
$response = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);

		if($head=='Facial Expression'){
		  
		$my_array = array("Relaxed","Partially Tightened(eg:Brow,Oweing)","Fully tightened(Eye lid closing)","Grimacing");
		  
			
		}elseif($head=='Upper limb movements'){
			
			$my_array = array("No Movement","Partially bent","Fully bent with finger flexion","Permanently retracted");
        
		}elseif($head=='Compliance with Mechanical Ventilation'){
			
			$my_array = array("Tolerating movement","Coughing but tolerating","Ventilation for the most of time fighting ventilator","Unable to control the ventilation");
			
		}
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
	$response['total_score']= "Total Score";
	$response['note']= "No Risk :(0-24) Low Risk : (25-44) High Risk : > 45";
	
    for($x = 0; $x < sizeof($my_array); $x++){	
	$response['behaviourpainlist'][$x]['score']=$my_array[$x];	
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