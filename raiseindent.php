<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$indentno = $data->indentno;
$response = array();
try{
if(!empty($indentno) && !empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
    $sbmt = $pdo4 -> prepare("UPDATE `pharmcy_orders` SET  `is_raised`='1' WHERE `order_no` = :indentno");
    $sbmt->bindParam(':indentno', $indentno, PDO::PARAM_STR);
    $sbmt -> execute();
    if($sbmt -> rowCount() > 0){
		http_response_code(200);
		  $response['error'] = false;
        $response['message'] = " order : $indentno is raised";
    }
    else{
		http_response_code(503);
		$response['error'] = true;
        $response['message'] = "something went wrong";
        
    }
}else{
		http_response_code(400);
		$response['error'] = true;
        $response['message'] = "Access denied!";
        
    }

}
else{
	http_response_code(400);
	$response['error'] = true;
$response['message'] = "some details are miising";

}

}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " ;
	$e;
	}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
