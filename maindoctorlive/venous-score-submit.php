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
$age=trim($data->age);
$surgery=trim($data->surgery);
$recent=trim($data->recent);  
$venous=trim($data->venous);
$mobility=trim($data->mobility);
$other=trim($data->other);
$age_score=trim($data->age_score);
$surgery_score=trim($data->surgery_score);
$recent_score=trim($data->recent_score);  
$venous_score=trim($data->venous_score);
$mobility_score=trim($data->mobility_score);
$other_score=trim($data->other_score);
$score=trim($data->score); 
try {
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !=''){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
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
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `venous_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `age`, `age_score`, `surgery`, `surgery_score`, `recent`, `recent_score`, `venous`, `venous_score`, `mobility`, `mobility_score`, `other`, `other_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno,:age, :age_score, :surgery, :surgery_score, :recent, :recent_score, :venous, :venous_score, :mobility, :mobility_score, :other, :other_score, :score,'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter, '') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':age_score', $age_score, PDO::PARAM_STR);
$ninsert->bindParam(':surgery_score', $surgery_score, PDO::PARAM_STR);
$ninsert->bindParam(':surgery', $surgery, PDO::PARAM_STR);
$ninsert->bindParam(':recent_score', $recent_score, PDO::PARAM_STR);
$ninsert->bindParam(':recent', $recent, PDO::PARAM_STR);
$ninsert->bindParam(':venous', $venous, PDO::PARAM_STR);
$ninsert->bindParam(':venous_score', $venous_score, PDO::PARAM_STR);
$ninsert->bindParam(':mobility_score', $mobility_score, PDO::PARAM_STR);
$ninsert->bindParam(':mobility', $mobility, PDO::PARAM_STR);
$ninsert->bindParam(':other', $other, PDO::PARAM_STR);
$ninsert->bindParam(':other_score', $other_score, PDO::PARAM_STR);
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
/*
} catch(PDOException $e) {
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
*/

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessweights();
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>