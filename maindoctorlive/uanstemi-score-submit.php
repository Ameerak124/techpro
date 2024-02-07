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
$cardiac=trim($data->cardiac);
$ekg=trim($data->ekg);
$age=trim($data->age);  
$angina=trim($data->angina);
$asa=trim($data->asa);
$cad=trim($data->cad);
$cadrisk=trim($data->cadrisk);
$cardiac_score=trim($data->cardiac_score);
$ekg_score=trim($data->ekg_score);
$age_score=trim($data->age_score);  
$angina_score=trim($data->angina_score);
$asa_score=trim($data->asa_score);
$cad_score=trim($data->cad_score);
$cadrisk_score=trim($data->cadrisk_score);
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
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `uanstemi_score`(`sno`, `createdfrom`, `ipno`, `umrno`, `cardiac`, `cardiac_score`, `ekg`, `ekg_score`, `angina`, `angina_score`, `asa`, `asa_score`, `cad`, `cad_score`, `cadrisk`, `cadrisk_score`, `age`, `age_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :cardiac, :cardiac_score, :ekg, :ekg_score, :angina, :angina_score, :asa, :asa_score, :cad, :cad_score, :cadrisk, :cadrisk_score, :age, :age_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':cardiac', $cardiac, PDO::PARAM_STR);
$ninsert->bindParam(':cardiac_score', $cardiac_score, PDO::PARAM_STR);
$ninsert->bindParam(':ekg_score', $ekg_score, PDO::PARAM_STR);
$ninsert->bindParam(':ekg', $ekg, PDO::PARAM_STR);
$ninsert->bindParam(':age_score', $age_score, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':angina', $angina, PDO::PARAM_STR);
$ninsert->bindParam(':angina_score', $angina_score, PDO::PARAM_STR);
$ninsert->bindParam(':asa_score', $asa_score, PDO::PARAM_STR);
$ninsert->bindParam(':asa', $asa, PDO::PARAM_STR);
$ninsert->bindParam(':cad_score', $cad_score, PDO::PARAM_STR);
$ninsert->bindParam(':cad', $cad, PDO::PARAM_STR);
$ninsert->bindParam(':cadrisk_score', $cadrisk_score, PDO::PARAM_STR);
$ninsert->bindParam(':cadrisk', $cadrisk, PDO::PARAM_STR);
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
        

}else {	
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else{
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
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