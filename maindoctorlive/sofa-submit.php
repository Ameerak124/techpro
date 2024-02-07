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
$accesskey = trim($data->accesskey);
// $createdfrom=trim($data->createdfrom);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$respirational=trim($data->respirational);
$coagulation=trim($data->coagulation);
$liver=trim($data->liver);  
$cardiovascular=trim($data->cardiovascular);
$glasgow=trim($data->glasgow);
$renal=trim($data->renal);
$respirational_score=trim($data->respirational_score);
$coagulation_score=trim($data->coagulation_score);
$liver_score=trim($data->liver_score);  
$cardiovascular_score=trim($data->cardiovascular_score);
$glasgow_score=trim($data->glasgow_score);
$renal_score=trim($data->renal_score);
$score=trim($data->score); 
try {
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno)  && ($score) >= 0 && ($score) !=''){  
   $check = $pdoread-> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
   $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
   $check -> execute();
   $result = $check->fetch(PDO::FETCH_ASSOC);
   if($check -> rowCount() > 0){
   //check if patient discharged or not
   $validate = $pdoread-> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
   $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
   $validate -> execute();
   $validates = $validate->fetch(PDO::FETCH_ASSOC);
   if($validate -> rowCount() > 0){ 
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `sofa_score`(`sno`, `createdfrom`,`ipno`, `umrno`,  `respirational`, `respirational_score`,`coagulation`,`coagulation_score`,`liver`,`liver_score`,`cardiovascular`,`cardiovascular_score`,`glasgow`,`glasgow_score`,`renal`, `renal_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :respirationals, :respirational_score,:coagulation,:coagulation_score,:liver,:liver_score,:cardiovascular,:cardiovascular_score,:glasgow,:glasgow_score,:renal, :renal_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
// $ninsert->bindParam(':createdfrom', $createdfrom, PDO::PARAM_STR);
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':respirationals', $respirational, PDO::PARAM_STR);
$ninsert->bindParam(':respirational_score', $respirational_score, PDO::PARAM_STR);
$ninsert->bindParam(':coagulation_score', $coagulation_score, PDO::PARAM_STR);
$ninsert->bindParam(':coagulation', $coagulation, PDO::PARAM_STR);
$ninsert->bindParam(':liver_score', $liver_score, PDO::PARAM_STR);
$ninsert->bindParam(':liver', $liver, PDO::PARAM_STR);
$ninsert->bindParam(':cardiovascular', $cardiovascular, PDO::PARAM_STR);
$ninsert->bindParam(':cardiovascular_score', $cardiovascular_score, PDO::PARAM_STR);
$ninsert->bindParam(':glasgow_score', $glasgow_score, PDO::PARAM_STR);
$ninsert->bindParam(':glasgow', $glasgow, PDO::PARAM_STR);
$ninsert->bindParam(':renal_score', $renal_score, PDO::PARAM_STR);
$ninsert->bindParam(':renal', $renal, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    http_response_code(503);
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
}else{
    http_response_code(400);
    $response['error'] = true;
    $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
  }
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access Denied";
}
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4= null;
$pdoread= null;
?>