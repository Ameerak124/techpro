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
$response = array();
$accesskey=trim($data->accesskey);
$doctor_name=($data->doctor_name);
$specialisations=($data->specialisations);
$ward=($data->ward);
$notes=trim($data->notes);
$doc_uid=trim($data->doc_uid);
$umrno=trim($data->umrno);
$ipno=trim($data->ipno);
$shift_type=trim($data->shift_type);
try {
     if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)&& !empty($notes)){    
//echo "Connected successfully";
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//check if details exist 
//insertion of data start
// Strip HTML tags and newline characters
//$notess = strip_tags($notes);
// $notess = str_replace(array("<p>"), '', strip_tags($notes));
// Escape the HTML text to prevent SQL injection
//$notess = $con->real_escape_string($notes);

 //check if patient discharged or not
 $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
 $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
 $validate -> execute();
 $validates = $validate->fetch(PDO::FETCH_ASSOC);
 if($validate -> rowCount() > 0){

$adddata=$pdo4-> prepare ("INSERT IGNORE INTO `doctor_progress_notes`(`sno`, `admissionno`, `umrno`, `doctor_uid`, `doctor_name`, `specialisations`, `ward`, `notes`, `shift_type`,`createdby`, `createdon`, `modifiedby`, `modifiedon`, `estatus`, `cost_center`, `approved_status`, `approvedby`, approvedon) VALUES (NULL,:ipno,:umrno,:doctor_uid,:doctor_name,:specialisations,:ward,:notes,:shift_type,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:branch, '','','')");	
$adddata->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$adddata->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$adddata->bindParam(':doctor_uid', $doc_uid, PDO::PARAM_STR);
$adddata->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
$adddata->bindParam(':specialisations', $specialisations, PDO::PARAM_STR);
$adddata->bindParam(':ward', $ward, PDO::PARAM_STR);
$adddata->bindParam(':notes', $notes, PDO::PARAM_STR);
$adddata->bindParam(':shift_type', $shift_type, PDO::PARAM_STR);
$adddata->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$adddata->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$adddata->execute();
if($adddata -> rowCount() > 0){
	  http_response_code(200);
	$response['error']=false;
	$response['message']="Data Inserted Sucessfully";
	//$response['notes'] = $notes;


}else{
	  http_response_code(503);
	$response['error']=true;
	$response['message']="Please Try Again";
}
//}
}else{
	  http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
}
}else {	
  http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
}
}else {	
  http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>