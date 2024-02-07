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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$pressure_ulcer_risk=trim($data->pressure_ulcer_risk);
$pressure_ulcer_risk_score=trim($data->pressure_ulcer_risk_score);
$anatomic=trim($data->anatomic);
$age=trim($data->age);
$size=trim($data->size);
$depth=trim($data->depth);
$stage=trim($data->stage);
$exudates=trim($data->exudates);
$remarks=trim($data->remarks); 
try {
    if(!empty($accesskey) && (!empty($pressure_ulcer_risk) || !empty($anatomic) || !empty($age) || !empty($size) || !empty($depth) || !empty($stage) || !empty($exudates) || !empty($remarks) )){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `daily_pressure_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `pressure_ulcer_risk`,`pressure_ulcer_risk_score`, `anatomic`, `age`, `size`, `depth`, `stage`, `exudates`, `remarks`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :pressure_ulcer_risk,:pressure_ulcer_risk_score, :anatomic,:age, :size,:depth,:stage,:exudates, :remarks,'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':pressure_ulcer_risk', $pressure_ulcer_risk, PDO::PARAM_STR);
$ninsert->bindParam(':pressure_ulcer_risk_score', $pressure_ulcer_risk_score, PDO::PARAM_STR);
$ninsert->bindParam(':anatomic', $anatomic, PDO::PARAM_STR);
$ninsert->bindParam(':age', $age, PDO::PARAM_STR);
$ninsert->bindParam(':size', $size, PDO::PARAM_STR);
$ninsert->bindParam(':depth', $depth, PDO::PARAM_STR);
$ninsert->bindParam(':stage', $stage, PDO::PARAM_STR);
$ninsert->bindParam(':exudates', $exudates, PDO::PARAM_STR);
$ninsert->bindParam(':remarks', $remarks, PDO::PARAM_STR);
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
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>