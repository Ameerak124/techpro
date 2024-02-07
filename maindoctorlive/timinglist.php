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
		
		  
		$my_array = array("7:00 AM","7:15 AM","7:30 AM","7:45 AM","8:00 AM","8:15 AM","8:30 AM","8:45 AM","9:00 AM","9:15 AM","9:30 AM","9:45 AM","10:00 AM","10:15 AM","10:30 AM","10:45 AM","11:00 AM","11:15 AM","11:30 AM","11:45 AM","12:00 PM","12:15 PM","12:30 PM","12:45 PM","01:00 PM","01:15 PM","01:30 PM","01:45 PM","02:00 PM","02:15 PM","02:30 PM","02:45 PM","03:00 PM","03:15 PM","03:30 PM","03:45 PM","04:00 PM","04:15 PM","04:30 PM","04:45 PM","05:00 PM","05:15 PM","05:30 PM","05:45 PM","06:00 PM","06:15 PM","06:30 PM","06:45 PM","07:00 PM","07:15 PM","07:30 PM","07:45 PM","08:00 PM");
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    for($x = 0; $x < sizeof($my_array); $x++){	
	$response['timinglist'][$x]['time']=$my_array[$x];	
     }		
	



/* // Define the start time and end time for your series
$start_time = strtotime('07:00:00');
$end_time = strtotime('20:00:00'); // You can adjust this end time as needed

// Define the time gap in seconds (15 minutes)
$time_gap_minutes = ;

// Initialize an array to store the generated times


// Generate the series of times
$current_time = $start_time;
while ($current_time <= $end_time) {
    $times[] = date('H:i', $current_time);
    $current_time += $time_gap_seconds;
}

// Display the generated times
 */
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
     
