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
	
	$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
		if($head=='Consultant Visited'){
			
			$my_array = array("Date","Time","Doctor Name","Remarks");
			
		}elseif($head=='Cross Consultion Request'){
			
        http_response_code(200);
	    $response = array(
           "error" => "false",
           "message" => "Data Found",
		   
		   "doctorvisitlist" => array(
		    array("title" => "Doctor"),
			
			array(
            "title" => "Appointment Priority",
            "appointmentpriority_list" => array(
                array("subtitle" => "Routine (Within 24hrs)"),
                array("subtitle" => "Priority (Within 6hrs)"),
                array("subtitle" => "Urgent (Within 1 -2hrs)"),
                array("subtitle" => "Immediate")
            )
        ),
			
		    array("title" => "Patients Key Clinical Problems"),
            array("title" => "Reason For Referral"),
			
			array(
            "title" => "Referred For",
            "referredfor_list" => array(
                array("subtitle" => "Examination & Option"),
                array("subtitle" => "Review"),
                array("subtitle" => "Co-Management"),
                array("subtitle" => "Take Over")
            )
        ),
			
		),
		);	
		
		}else{
		http_response_code(503);
        $response['error'] = true; 
        $response['message']= "No Data Found";	
			
		}
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    for($x = 0; $x < sizeof($my_array); $x++){	
	$response['doctorvisitlist'][$x]['title']=$my_array[$x];	
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