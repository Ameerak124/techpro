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
$vital_score=trim($data->vital_score);
$ambulation_score=trim($data->ambulation_score);
$nausea_score=trim($data->nausea_score);  
$pain_score=trim($data->pain_score);
$surgical_score=trim($data->surgical_score);
$vital=trim($data->vital);
$ambulation=trim($data->ambulation);
$nausea=trim($data->nausea);  
$pain=trim($data->pain);
$surgical=trim($data->surgical);
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
$ninsert=$pdo4->prepare("INSERT INTO `padss_score`(`sno`, `createdfrom`, `ipno`, `umrno`, `vital`, `vital_score`, `ambulation`, `ambulation_score`, `nausea`, `nausea_score`, `pain`, `pain_score`, `surgical`, `surgical_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`) VALUES (NULL,'Score Assessment', :ipno, :umrno,  :vital, :vital_score, :ambulation, :ambulation_score, :nausea, :nausea_score, :pain, :pain_score, :surgical, :surgical_score, :score,'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter) ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
 $ninsert->bindParam(':vital_score', $vital_score, PDO::PARAM_STR);
$ninsert->bindParam(':ambulation_score', $ambulation_score, PDO::PARAM_STR);
$ninsert->bindParam(':nausea_score', $nausea_score, PDO::PARAM_STR);
$ninsert->bindParam(':pain_score', $pain_score, PDO::PARAM_STR);
$ninsert->bindParam(':surgical_score', $surgical_score, PDO::PARAM_STR);
$ninsert->bindParam(':spo_score', $renal_score, PDO::PARAM_STR);
$ninsert->bindParam(':vital', $vital, PDO::PARAM_STR);
$ninsert->bindParam(':ambulation', $ambulation, PDO::PARAM_STR);
$ninsert->bindParam(':nausea', $nausea, PDO::PARAM_STR);
$ninsert->bindParam(':pain', $pain, PDO::PARAM_STR);
$ninsert->bindParam(':surgical', $surgical, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    $painonse['error']=false;
    $painonse['message']="Data Inserted Successfully";
}else{
    $painonse['error']=true;
    $painonse['message']="Data Not Inserted";
}
}else{
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
  }

}else {	
    $painonse['error'] = true;
	$painonse['message']= "Access denied!";
}

}else {	
    $painonse['error'] = true;
	$painonse['message']= "Sorry! some details are missing";
}

} catch(PDOException $e) {
	// http_painonse_code(503);
	$painonse['error'] = true;
	$painonse['message']= "Connection failed";
}

echo json_encode($painonse);
$pdo4 = null;
$pdoread = null;
?>