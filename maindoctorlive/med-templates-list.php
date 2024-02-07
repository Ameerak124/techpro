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
$accesskey = trim($data->accesskey);
$template_name = trim($data->template_name);
$response = array();
try {
if(!empty($accesskey)){

$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
//Access key verified
if($check -> rowCount() > 0){
$query=$pdoread->prepare("SELECT `template_name` FROM `medication_templates` WHERE `status`='Active' AND `category`='MEDICATION' GROUP BY `template_name` ");
// $query->bindParam(':template_name', $template_name, PDO::PARAM_STR);
$query->execute();
if($query->rowCount()>0){
$sn=0;
http_response_code(200);
    $response['error']=false;
    $response['message']="Data found";
    while($res=$query->fetch(PDO::FETCH_ASSOC)){
        $response['data'][$sn]['template_name']=$res['template_name'];
        $sn++;
    }

}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="No Data found";
}
}else{
	http_response_code(400);
    $response['error']= true;
	$response['message']="Access denied!";
}

}else{
	http_response_code(400);
    $response['error']= true;
	$response['message']="Sorry! some details are missing";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>