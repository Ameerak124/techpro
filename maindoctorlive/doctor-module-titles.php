<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$response = array();
try{
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);


   
http_response_code(200);
$response['error'] = false; 
$response['message']= "Data Found";
$response['is_vip']= "Is VIP";
$response['time_left']= "Time Left";
$response['searchno']= "Search Mobile No.";
$response['visittype']= "Visit Type";
$response['gender']= "Gender *";
$response['patname']= "Patient Name *";
$response['email']= "Email";
$response['mobnum']= "Mobile Number";
$response['reason']= "Reason for Visit";
$response['apptype']= "Appointment Type *";
$response['contype']= "Consultation Type *";
$response['dob']= "Date of Birth";
$response['altmob']= "Alternative Mobile";
$response['from_area']= "From Area";
$response['ref_by']= "Referred By";
$response['note']= "Note";
$response['cancel']= "CANCEL";
$response['submit']= "SUBMIT";
$response['sel_date_time']= "Selected Date and Time";
$response['type']= "Type";
$response['name']= "Name";
$response['location']= "Location";
$response['desg']= "Designation";

}else{
http_response_code(400);
$response['error'] = true;
$response['message']="Access denied!";
}  
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
}
} 
catch(PDOException $e) {
http_response_code(503);
$response['error'] = true;
$response['message']= "Connection failed ".$e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>