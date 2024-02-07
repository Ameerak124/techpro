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
$treatment=trim($data->treatment);
$anterior=trim($data->anterior);
$age=trim($data->age);  
$weights=trim($data->weights);
$killip=trim($data->killip);
$heart=trim($data->heart);
$systolic=trim($data->systolic);
$diabetes=trim($data->diabetes);
$treatment_score=trim($data->treatment_score);
$anterior_score=trim($data->anterior_score);
$age_score=trim($data->age_score);  
$weights_score=trim($data->weights_score);
$killip_score=trim($data->killip_score);
$heart_score=trim($data->heart_score);
$systolic_score=trim($data->systolic_score);
$diabetes_score=trim($data->diabetes_score);
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
$ninsert=$pdo4->prepare("INSERT INTO `stemi_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `treatment`, `treatment_score`, `anterior`, `anterior_score`, `weights`, `weights_score`, `killip`, `killip_score`, `heart`, `heart_score`, `systolic`, `systolic_score`, `diabetes`, `diabetes_score`, `age`, `age_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :treatment, :treatment_score, :anterior, :anterior_score, :weights, :weights_score, :killip, :killip_score, :heart, :heart_score, :systolic, :systolic_score, :diabetes, :diabetes_score, :age, :age_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':treatment', $treatment, PDO::PARAM_STR);
$ninsert->bindParam(':treatment_score', $treatment_score, PDO::PARAM_STR);
$ninsert->bindParam(':anterior_score', $anterior_score, PDO::PARAM_STR);
$ninsert->bindParam(':anterior', $anterior, PDO::PARAM_STR);
$ninsert->bindParam(':age_score', $age_score, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':weights', $weights, PDO::PARAM_STR);
$ninsert->bindParam(':weights_score', $weights_score, PDO::PARAM_STR);
$ninsert->bindParam(':killip_score', $killip_score, PDO::PARAM_STR);
$ninsert->bindParam(':killip', $killip, PDO::PARAM_STR);
$ninsert->bindParam(':heart_score', $heart_score, PDO::PARAM_STR);
$ninsert->bindParam(':heart', $heart, PDO::PARAM_STR);
$ninsert->bindParam(':systolic', $systolic, PDO::PARAM_STR);
$ninsert->bindParam(':systolic_score', $systolic_score, PDO::PARAM_STR);
$ninsert->bindParam(':diabetes', $diabetes, PDO::PARAM_STR);
$ninsert->bindParam(':diabetes_score', $diabetes_score, PDO::PARAM_STR);
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
	$response['message']= "Connection failed";
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>