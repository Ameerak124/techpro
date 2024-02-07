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
$searchterm=trim($data->searchterm);


try {
     if(!empty($accesskey)&& !empty($searchterm)){    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	$rolex=$pdoread->prepare("SELECT `service_code` AS service_code, `services_name` AS  service_name, `price` AS service_cost FROM `services_master` WHERE `service_status`='Active' AND `service_valid_to` >= CURRENT_DATE AND `services_name` LIKE :searchterm ");
    $rolex -> bindValue(":searchterm", "{$searchterm}%", PDO::PARAM_STR);
    // $rolex->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
    $rolex->execute();
    if($rolex->rowCount()>0){
		http_response_code(200);
        $response['error']=false;
        $response['message']="Data Found";
        while($saha=$rolex->fetch(PDO::FETCH_ASSOC)){
            $response['servicesearch'][]=$saha;
        }
    }else{
		http_response_code(503);
        $response['error']=true;
        $response['message']="Sorry! No Data Found";
    }

}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	
}
echo json_encode($response);
$pdoread = null;
?>