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
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey= $data->accesskey;
$term= $data->keyword;
$itemsno= $data->itemsno;
$response = array();
try {
if(!empty($accesskey)&&!empty($itemsno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $getitems_query = "UPDATE `pharmcy_indent` SET `is_delete`='1' WHERE `sno` = :itemsno";   
     $getitems_sbmt = $pdo4 -> prepare($getitems_query);
     $getitems_sbmt -> bindParam(":itemsno", $itemsno, PDO::PARAM_STR);    
     $getitems_sbmt -> execute();
     if($getitems_sbmt -> rowCount() > 0){
		 http_response_code(200);
          $response['error']= false;
	     $response['message']="Item Deleted";
     }
     else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Something Went Wrong!";
     }
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
     
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();

}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>