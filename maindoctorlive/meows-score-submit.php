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
$systolic=trim($data->systolic);
$diastolic=trim($data->diastolic);
$tempu_score=trim($data->tempu_score);
$resp_score=trim($data->resp_score);
$heart_score=trim($data->heart_score);  
$spo_score=trim($data->spo_score);
$systolic_score=trim($data->systolic_score);
$diastolic_score=trim($data->diastolic_score);
$neurological=trim($data->neurological);
$pain=trim($data->pain);
$protenuria=trim($data->protenuria);
$liquor=trim($data->liquor);
$lochia=trim($data->lochia);
$urine=trim($data->urine);
$infection=trim($data->infection);
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
      $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
      $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
      $validate -> execute();
      $validates = $validate->fetch(PDO::FETCH_ASSOC);
      if($validate -> rowCount() > 0){
//insert query
$ninsert=$pdo4->prepare("INSERT INTO `meows_score`(`sno`, `createdfrom`, `ipno`, `umrno`, `resp`, `resp_score`, `spo`, `spo_score`, `tempu`, `tempu_score`, `systolic`, `systolic_score`, `diastolic`, `diastolic_score`, `heart`, `heart_score`, `neurological`, `pain`, `protenuria`, `liquor`, `lochia`, `urine`, `infection`, `informedto`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :resp, :resp_score, :spo, :spo_score, :tempu, :tempu_score, :systolic, :systolic_score, :diastolic, :diastolic_score, :heart, :heart_score, :neurological, :pain, :protenuria, :liquor, :lochia, :urine, :infection, :informedto, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
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
$ninsert->bindParam(':systolic_score', $systolic_score, PDO::PARAM_STR);
$ninsert->bindParam(':systolic', $systolic, PDO::PARAM_STR);
$ninsert->bindParam(':diastolic_score', $diastolic_score, PDO::PARAM_STR);
$ninsert->bindParam(':diastolic', $diastolic, PDO::PARAM_STR);
$ninsert->bindParam(':neurological', $neurological, PDO::PARAM_STR);
$ninsert->bindParam(':pain', $pain, PDO::PARAM_STR);
$ninsert->bindParam(':protenuria', $protenuria, PDO::PARAM_STR);
$ninsert->bindParam(':liquor', $liquor, PDO::PARAM_STR);
$ninsert->bindParam(':lochia', $lochia, PDO::PARAM_STR);
$ninsert->bindParam(':urine', $urine, PDO::PARAM_STR);
$ninsert->bindParam(':infection', $infection, PDO::PARAM_STR);
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