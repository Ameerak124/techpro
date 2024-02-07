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
$keyword = $data->keyword;
$accesskey = $data->accesskey;
$response = array();
try{	
if(!empty($keyword) && !empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$sbmt = $pdoread->prepare("SELECT `sno`, `itemcode`, `item_desc`, `uom`, `code`, `batch_no`, `code2`, `barcode`, `hsn`, `on_hand`, `purchase`, `sale` FROM `central_pharmacy` WHERE `item_desc` LIKE :keyword LIMIT 50");
$sbmt -> bindvalue(':keyword',"%{$keyword}%", PDO::PARAM_STR);
$sbmt -> execute();
if($sbmt -> rowCount() > 0){
     $data = $sbmt -> fetchAll(PDO::FETCH_ASSOC);
     $response['error'] = false;
     $response['message'] = "Data found";
     $response['drugsdata'] = $data;
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
$pdoread = null;
?>
