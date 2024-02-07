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
$tempu_score=trim($data->tempu_score);
$systolic_score=trim($data->systolic_score);
$heart_score=trim($data->heart_score);  
$resp_score=trim($data->resp_score);
$loc_score=trim($data->loc_score);
$spo_score=trim($data->spo_score);
$tempu=trim($data->tempu);
$systolic=trim($data->systolic);
$heart=trim($data->heart);  
$resp=trim($data->resp);
$loc=trim($data->loc);
$spo=trim($data->spo);
$score=trim($data->score);
 
try {
   
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !=''){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){

if($score < 3){
    $display_name='Frequent observation minimum of every 6 hours & inform nurse incharge';
    $color='background: #ffe200;';
}else if($score >= 3 && $score < 5){
    $display_name='Inform DMO & Primary Consultant - Initiate Treament Accordingly Document MEWS Hourly';
    $color='background: #fe9365;';
}else{
    $display_name='Immediate call to Critical Care Team & Primary Consultant - shift the patient to HDU/ICU';
    $color='background: #fe5d70;';
}
            
  //check if patient discharged or not
   $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
   $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
   $validate -> execute();
   $validates = $validate->fetch(PDO::FETCH_ASSOC);
   if($validate -> rowCount() > 0){
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `mew_score`(`sno`, `createdfrom`,`ipno`, `umrno`,   `tempu_score`,`systolic_score`,`heart_score`,`resp_score`,`loc_score`, `spo_score`, `tempu`,`systolic`,`heart`,`resp`,`loc`, `spo`,`score`,`display_name`, `color`,`estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno,  :tempu_score,:systolic_score,:heart_score,:resp_score,:loc_score, :spo_score, :tempu,:systolic,:heart,:resp,:loc, :spo, :score, :display_name, :color,'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':tempu_score', $tempu_score, PDO::PARAM_STR);
$ninsert->bindParam(':systolic_score', $systolic_score, PDO::PARAM_STR);
$ninsert->bindParam(':heart_score', $heart_score, PDO::PARAM_STR);
$ninsert->bindParam(':resp_score', $resp_score, PDO::PARAM_STR);
$ninsert->bindParam(':loc_score', $loc_score, PDO::PARAM_STR);
$ninsert->bindParam(':spo_score', $spo_score, PDO::PARAM_STR);
$ninsert->bindParam(':tempu', $tempu, PDO::PARAM_STR);
$ninsert->bindParam(':systolic', $systolic, PDO::PARAM_STR);
$ninsert->bindParam(':heart', $heart, PDO::PARAM_STR);
$ninsert->bindParam(':resp', $resp, PDO::PARAM_STR);
$ninsert->bindParam(':loc', $loc, PDO::PARAM_STR);
$ninsert->bindParam(':spo', $spo, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':display_name', $display_name, PDO::PARAM_STR);
$ninsert->bindParam(':color', $color, PDO::PARAM_STR);
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