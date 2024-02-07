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
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$alcohol=trim($data->alcohol);
$medication=trim($data->medication);
$age=trim($data->age);  
$labile=trim($data->labile);
$bleeding=trim($data->bleeding);
$stroke=trim($data->stroke);
$liver=trim($data->liver);
$renal=trim($data->renal);
$hypertension=trim($data->hypertension);
$alcohol_score=trim($data->alcohol_score);
$medication_score=trim($data->medication_score);
$age_score=trim($data->age_score);  
$labile_score=trim($data->labile_score);
$bleeding_score=trim($data->bleeding_score);
$stroke_score=trim($data->stroke_score);
$liver_score=trim($data->liver_score);
$renal_score=trim($data->renal_score);
$hypertension_score=trim($data->hypertension_score);
$score=trim($data->score);
 
try {
   
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !=''){ 
	
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
                  //check if patient discharged or not
$validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
$validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
$validate -> execute();
$validates = $validate->fetch(PDO::FETCH_ASSOC);
                  if($validate -> rowCount() > 0){
    
//insert query
$ninsert=$pdo4->prepare("INSERT INTO `hasbled_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `alcohol`, `alcohol_score`, `medication`, `medication_score`, `age`, `age_score`, `labile`, `labile_score`, `bleeding`, `bleeding_score`, `stroke`, `stroke_score`, `liver`, `liver_score`, `renal`, `renal_score`, `hypertension`, `hypertension_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :alcohol, :alcohol_score, :medication, :medication_score, :age, :age_score, :labile, :labile_score, :bleeding, :bleeding_score, :stroke, :stroke_score, :liver, :liver_score, :renal, :renal_score, :hypertension, :hypertension_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':alcohol', $alcohol, PDO::PARAM_STR);
$ninsert->bindParam(':alcohol_score', $alcohol_score, PDO::PARAM_STR);
$ninsert->bindParam(':medication_score', $medication_score, PDO::PARAM_STR);
$ninsert->bindParam(':medication', $medication, PDO::PARAM_STR);
$ninsert->bindParam(':age_score', $age_score, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':labile', $labile, PDO::PARAM_STR);
$ninsert->bindParam(':labile_score', $labile_score, PDO::PARAM_STR);
$ninsert->bindParam(':bleeding_score', $bleeding_score, PDO::PARAM_STR);
$ninsert->bindParam(':bleeding', $bleeding, PDO::PARAM_STR);
$ninsert->bindParam(':stroke_score', $stroke_score, PDO::PARAM_STR);
$ninsert->bindParam(':stroke', $stroke, PDO::PARAM_STR);
$ninsert->bindParam(':liver', $liver, PDO::PARAM_STR);
$ninsert->bindParam(':liver_score', $liver_score, PDO::PARAM_STR);
$ninsert->bindParam(':renal', $renal, PDO::PARAM_STR);
$ninsert->bindParam(':renal_score', $renal_score, PDO::PARAM_STR);
$ninsert->bindParam(':hypertension', $hypertension, PDO::PARAM_STR);
$ninsert->bindParam(':hypertension_score', $hypertension_score, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
}else{
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
}       

}else {	
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
} catch(PDOException $e) {
	// http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
/*
} catch(PDOException $e) {
	// http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	$errorlog = $pdo -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
*/
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>