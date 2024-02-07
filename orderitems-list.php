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
$accesskey = $data->accesskey;
$orderno =  $data->orderno;
$response = array();
try{
if(!empty($accesskey) && !empty($orderno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
$query = $pdoread -> prepare( "SELECT `sno`, `umr_no`, `ip_no`, `order_no`, `drug_code`, `drug_name`, `quantity`, `drug_price`, `batch_no`, `hsn`,`apr_qty` FROM `pharmcy_indent` WHERE `order_no` = :orderno");
 $query->bindParam(":orderno", $orderno, PDO::PARAM_STR);
 $query->execute();
 if($query -> rowCount() > 0){
	 $data = $query -> fetchAll(PDO::FETCH_ASSOC);
		 http_response_code(200);  
		 $response['error']= false;
		 $response['message']="Data Found";
	      $response['orderitemslist']= $data;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
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
 }catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>