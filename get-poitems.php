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
try {
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey; 
 $ponumber = $data->ponumber; 
$delete = 'delete';
if(!empty($accesskey) && !empty($ponumber)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //get_items start
     $query = "SELECT `id`,`po_no`, `item_name`, `item_qty`, `unit_price`, `igst`, `sgst`, `cgst`, `total`, `base_total` FROM `po_item` WHERE `po_item`.`po_no` = :ponumber AND `po_item`.`status` != :del";
     $po_items = $pdoread -> prepare($query);
     $po_items -> bindParam(":ponumber", $ponumber, PDO::PARAM_STR);
     $po_items -> bindParam(":del", $delete, PDO::PARAM_STR);
     $po_items -> execute();
     if($po_items -> rowCount() > 0){
		 http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found!";
          $list = $po_items -> fetchAll(PDO::FETCH_ASSOC);
          $response['data'] = $list;
     }
     else{
		  http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
     //get_items end  
}
else
{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}
else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     $response['accesskey'] = $accesskey;
}
} 
catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>