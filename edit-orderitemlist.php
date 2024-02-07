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
$itemsno = $data->itemsno;
$modifiedqty=$data->modifiedqty;
$response = array();
try {	
if(!empty($accesskey) &&!empty($itemsno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     $stmt=$pdo4->prepare("Update `pharmcy_indent` set `apr_qty`=:modifiedqty where `sno`=:itemsno");
     $stmt -> bindParam(":modifiedqty", $modifiedqty, PDO::PARAM_STR);
	 $stmt -> bindParam(":itemsno", $itemsno, PDO::PARAM_STR);
     $stmt -> execute();
     if($stmt -> rowCount() > 0){

     $getitem_query = "SELECT `sno`,`order_no`, `drug_code`, `drug_name`, `quantity`, `apr_qty` FROM `pharmcy_indent` WHERE `sno` = :itemsno";
     $getitem_sbmt = $pdoread -> prepare($getitem_query);
     $getitem_sbmt -> bindParam(":itemsno", $itemsno, PDO::PARAM_STR);
     $getitem_sbmt -> execute();
     if($getitem_sbmt -> rowCount() > 0){
         $list = $getitem_sbmt -> fetch(PDO::FETCH_ASSOC);
		 http_response_code(200);
          $response['error']= false;
	      $response['message']="Data Found";
          $response['editorderitemlist'][]= $list;
     }
     else
     {
		 http_response_code(503);
        $response['error']= true;
	     $response['message']="Sorry Something Went Wrong";
     }
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}else{
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
$pdoread = null;
$pdo4 = null;
?>