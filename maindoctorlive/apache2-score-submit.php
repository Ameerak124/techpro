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
$history=trim($data->history);
$heart=trim($data->heart);  
$rectal=trim($data->rectal);
$mean=trim($data->mean);
$respiratory=trim($data->respiratory);
$oxygenation=trim($data->oxygenation);
$arterial=trim($data->arterial);
$sodium=trim($data->sodium);
$potassium=trim($data->potassium);
$creatinine=trim($data->creatinine);
$hematocrit=trim($data->hematocrit);
$whiteblood=trim($data->whiteblood);
$glasgow=trim($data->glasgow);
$age_score=trim($data->age_score);
$history_score=trim($data->history_score);
$heart_score=trim($data->heart_score);  
$rectal_score=trim($data->rectal_score);
$respiratory_score=trim($data->respiratory_score);
$oxygenation_score=trim($data->oxygenation_score);
$arterial_score=trim($data->arterial_score);
$sodium_score=trim($data->sodium_score);
$potassium_score=trim($data->potassium_score);
$creatinine_score=trim($data->creatinine_score);
$hematocrit_score=trim($data->hematocrit_score);
$whiteblood_score=trim($data->whiteblood_score);
$glasgow_score=trim($data->glasgow_score);
$mean_score=trim($data->mean_score);
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
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `apache2_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `age`, `age_score`, `history`, `history_score`, `rectal`, `rectal_score`, `mean`, `mean_score`,`heart`, `heart_score`, `respiratory`, `respiratory_score`, `oxygenation`, `oxygenation_score`, `arterial`, `arterial_score`, `sodium`, `sodium_score`, `potassium`, `potassium_score`, `creatinine`, `creatinine_score`, `hematocrit`, `hematocrit_score`, `whiteblood`, `whiteblood_score`, `glasgow`, `glasgow_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :age, :age_score, :history, :history_score, :rectal, :rectal_score, :mean, :mean_score,:heart, :heart_score, :respiratory, :respiratory_score, :oxygenation, :oxygenation_score, :arterial, :arterial_score, :sodium, :sodium_score, :potassium, :potassium_score, :creatinine, :creatinine_score, :hematocrit, :hematocrit_score, :whiteblood, :whiteblood_score, :glasgow, :glasgow_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':age_score', $age_score, PDO::PARAM_STR);
$ninsert->bindParam(':history_score', $history_score, PDO::PARAM_STR);
$ninsert->bindParam(':history', $history, PDO::PARAM_STR);
$ninsert->bindParam(':heart_score', $heart_score, PDO::PARAM_STR);
$ninsert->bindParam(':heart', $heart, PDO::PARAM_STR);
$ninsert->bindParam(':rectal', $rectal, PDO::PARAM_STR);
$ninsert->bindParam(':rectal_score', $rectal_score, PDO::PARAM_STR);
$ninsert->bindParam(':mean', $mean, PDO::PARAM_STR);
$ninsert->bindParam(':mean_score', $mean_score, PDO::PARAM_STR);
$ninsert->bindParam(':respiratory_score', $respiratory_score, PDO::PARAM_STR);
$ninsert->bindParam(':respiratory', $respiratory, PDO::PARAM_STR);
$ninsert->bindParam(':oxygenation_score', $oxygenation_score, PDO::PARAM_STR);
$ninsert->bindParam(':oxygenation', $oxygenation, PDO::PARAM_STR);
$ninsert->bindParam(':arterial', $arterial, PDO::PARAM_STR);
$ninsert->bindParam(':arterial_score', $arterial_score, PDO::PARAM_STR);
$ninsert->bindParam(':sodium', $sodium, PDO::PARAM_STR);
$ninsert->bindParam(':sodium_score', $sodium_score, PDO::PARAM_STR);
$ninsert->bindParam(':potassium', $potassium, PDO::PARAM_STR);
$ninsert->bindParam(':potassium_score', $potassium_score, PDO::PARAM_STR);
$ninsert->bindParam(':creatinine', $creatinine, PDO::PARAM_STR);
$ninsert->bindParam(':creatinine_score', $creatinine_score, PDO::PARAM_STR);
$ninsert->bindParam(':hematocrit', $hematocrit, PDO::PARAM_STR);
$ninsert->bindParam(':hematocrit_score', $hematocrit_score, PDO::PARAM_STR);
$ninsert->bindParam(':whiteblood', $whiteblood, PDO::PARAM_STR);
$ninsert->bindParam(':whiteblood_score', $whiteblood_score, PDO::PARAM_STR);
$ninsert->bindParam(':glasgow', $glasgow, PDO::PARAM_STR);
$ninsert->bindParam(':glasgow_score', $glasgow_score, PDO::PARAM_STR);
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