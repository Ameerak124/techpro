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
$mobility=trim($data->mobility);
$bmi=trim($data->bmi);
$age=trim($data->age);  
$risk=trim($data->risk);
$trauma=trim($data->trauma);
$surgery=trim($data->surgery);
$diseases=trim($data->diseases);
$mobility_score=trim($data->mobility_score);
$bmi_score=trim($data->bmi_score);
$age_score=trim($data->age_score);  
$risk_score=trim($data->risk_score);
$trauma_score=trim($data->trauma_score);
$surgery_score=trim($data->surgery_score);
$diseases_score=trim($data->diseases_score);
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
$ninsert=$pdo4->prepare("INSERT INTO `dvt_score`(`sno`, `createdfrom`, `ipno`, `umrno`, `age`, `age_score`, `mobility`, `mobility_score`, `bmi`, `bmi_score`, `risk`, `risk_score`, `trauma`, `trauma_score`, `surgery`, `surgery_score`, `diseases`, `diseases_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :age, :age_score, :mobility, :mobility_score, :bmi, :bmi_score, :risk, :risk_score, :trauma, :trauma_score, :surgery, :surgery_score, :diseases, :diseases_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':mobility', $mobility, PDO::PARAM_STR);
$ninsert->bindParam(':mobility_score', $mobility_score, PDO::PARAM_STR);
$ninsert->bindParam(':bmi_score', $bmi_score, PDO::PARAM_STR);
$ninsert->bindParam(':bmi', $bmi, PDO::PARAM_STR);
$ninsert->bindParam(':age_score', $age_score, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':risk', $risk, PDO::PARAM_STR);
$ninsert->bindParam(':risk_score', $risk_score, PDO::PARAM_STR);
$ninsert->bindParam(':trauma_score', $trauma_score, PDO::PARAM_STR);
$ninsert->bindParam(':trauma', $trauma, PDO::PARAM_STR);
$ninsert->bindParam(':surgery_score', $surgery_score, PDO::PARAM_STR);
$ninsert->bindParam(':surgery', $surgery, PDO::PARAM_STR);
$ninsert->bindParam(':diseases_score', $diseases_score, PDO::PARAM_STR);
$ninsert->bindParam(':diseases', $diseases, PDO::PARAM_STR);
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
	$response['message']= "Connection failed: " . $e->getMessweights();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>