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
$accesskey=trim($data->accesskey);
$sno=trim($data->sno);
$del_remarks=trim($data->del_remarks);
try {
     if(!empty($accesskey)&& !empty($sno)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){

//insert into the table
$update=$pdo4->prepare("UPDATE `venous_score` SET `estatus`='Inactive' , `modifiedby`=:userid , `modifiedon`=CURRENT_TIMESTAMP, `del_remarks`=:del_remarks WHERE `sno`=:sno AND `cost_center`=:cost_center");
$update->bindParam(':sno', $sno, PDO::PARAM_STR);
$update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$update->bindParam(':del_remarks', $del_remarks, PDO::PARAM_STR);
$update->execute();
if($update->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Deleted";
}else{
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Data Not Found";
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