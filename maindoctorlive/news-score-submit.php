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
$tempu=trim($data->tempu);
$resp=trim($data->resp);
$heart=trim($data->heart);  
$spo=trim($data->spo);
$neuro=trim($data->neuro);
$glucose=trim($data->glucose);
$tempu_score=trim($data->tempu_score);
$resp_score=trim($data->resp_score);
$heart_score=trim($data->heart_score);  
$spo_score=trim($data->spo_score);
$neuro_score=trim($data->neuro_score);
$glucose_score=trim($data->glucose_score);
$score=trim($data->score);
$informedto=trim($data->informedto);  
try {
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !=''){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
                  //check if patient discharged or not
                  $validate = $pdo -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
                  $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
                  $validate -> execute();
                  $validates = $validate->fetch(PDO::FETCH_ASSOC);
                  if($validate -> rowCount() > 0){
    
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `news_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `tempu`, `tempu_score`, `resp`, `resp_score`, `heart`, `heart_score`, `spo`, `spo_score`, `neuro`, `neuro_score`, `glucose`, `glucose_score`, `score`, `informedto`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :tempu, :tempu_score, :resp, :resp_score, :heart, :heart_score, :spo, :spo_score, :neuro, :neuro_score, :glucose, :glucose_score, :score, :informedto, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':tempu', $tempu, PDO::PARAM_STR);
$ninsert->bindParam(':tempu_score', $tempu_score, PDO::PARAM_STR);
$ninsert->bindParam(':resp_score', $resp_score, PDO::PARAM_STR);
$ninsert->bindParam(':resp', $resp, PDO::PARAM_STR);
$ninsert->bindParam(':heart_score', $heart_score, PDO::PARAM_STR);
$ninsert->bindParam(':heart', $heart, PDO::PARAM_STR);
$ninsert->bindParam(':spo', $spo, PDO::PARAM_STR);
$ninsert->bindParam(':spo_score', $spo_score, PDO::PARAM_STR);
$ninsert->bindParam(':neuro_score', $neuro_score, PDO::PARAM_STR);
$ninsert->bindParam(':neuro', $neuro, PDO::PARAM_STR);
$ninsert->bindParam(':glucose_score', $glucose_score, PDO::PARAM_STR);
$ninsert->bindParam(':glucose', $glucose, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':informedto', $informedto, PDO::PARAM_STR);
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