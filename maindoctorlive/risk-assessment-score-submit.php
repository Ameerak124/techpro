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
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$assessment_type=trim($data->assessment_type);
$hfall=trim($data->hfall);
$hfall_score=trim($data->hfall_score);
$diagnosis=trim($data->diagnosis);
$diagnosis_score=trim($data->diagnosis_score);
$ambulatory=trim($data->ambulatory);
$ambulatory_score=trim($data->ambulatory_score);
$acess=trim($data->acess);
$acess_score=trim($data->acess_score);
$gait=trim($data->gait);
$gait_score=trim($data->gait_score);
$mental=trim($data->mental);
$mental_score=trim($data->mental_score);
$score=trim($data->score); 
try {

    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !='' ){  
        $check = $pdoread-> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
        //check if patient discharged or not
        $validate = $pdoread-> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND`status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
        $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
        $validate -> execute();
        $validates = $validate->fetch(PDO::FETCH_ASSOC);
        if($validate -> rowCount() > 0){
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `risk_assessment_score`(`sno`, `createdfrom`,`ipno`, `umrno`,  `assessment_type`,   `hfall`, `hfall_score`, `diagnosis`, `diagnosis_score`, `ambulatory`, `ambulatory_score`, `acess`, `acess_score`, `gait`, `gait_score`, `mental`, `mental_score`,  `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno,:assessment_type, :hfall, :hfall_score,:diagnosis, :diagnosis_score,:ambulatory,:ambulatory_score,:acess,:acess_score,:gait,:gait_score,:mental,:mental_score,:score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':assessment_type', $assessment_type, PDO::PARAM_STR);
$ninsert->bindParam(':hfall', $hfall, PDO::PARAM_STR);
$ninsert->bindParam(':hfall_score', $hfall_score, PDO::PARAM_STR);
$ninsert->bindParam(':diagnosis', $diagnosis, PDO::PARAM_STR);
$ninsert->bindParam(':diagnosis_score', $diagnosis_score, PDO::PARAM_STR);
$ninsert->bindParam(':ambulatory', $ambulatory, PDO::PARAM_STR);
$ninsert->bindParam(':ambulatory_score', $ambulatory_score, PDO::PARAM_STR);
$ninsert->bindParam(':acess', $acess, PDO::PARAM_STR);
$ninsert->bindParam(':acess_score', $acess_score, PDO::PARAM_STR);
$ninsert->bindParam(':gait', $gait, PDO::PARAM_STR);
$ninsert->bindParam(':gait_score', $gait_score, PDO::PARAM_STR);
$ninsert->bindParam(':mental', $mental, PDO::PARAM_STR);
$ninsert->bindParam(':mental_score', $mental_score, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    http_response_code(200);
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
        
}else{
    http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
}
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>