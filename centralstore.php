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
$accesskey= $data->accesskey;
$itemcode= $data->itemcode;
$batchno= $data->batch;
$response = array();
try{
if(!empty($accesskey) && !empty($itemcode)  && !empty($batchno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
     $stmt=$pdoread->prepare("SELECT central_store.sno AS cen_sno,central_store.itemcode, central_store.itemdesc, central_store.expiry, central_store.batch, central_store.onhand, central_store.purchase_rate, central_store.sale_rate, central_store.purchase_value, central_store.sale_value, central_store.tax_per,central_store.tax_value FROM `central_store`INNER JOIN department_indent_items ON central_store.itemcode=department_indent_items.itemcode WHERE central_store.itemcode=:itemcode and central_store.batch like :batchno AND  central_store.onhand>0 Group BY central_store.itemcode,central_store.batch");
	 $stmt->bindParam(':itemcode', $itemcode, PDO::PARAM_STR);
	 $stmt->bindParam(':batchno', $bacthno_t, PDO::PARAM_STR);
	 $bacthno_t='%'.$batchno.'%';
	 $stmt -> execute();
     if($stmt -> rowCount() > 0){
          $data = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		  http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['centralstorelist']= $data;
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
	$response['message']="Access denied! please try to re-login again";
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
   unset($pdoread);
?>

	 