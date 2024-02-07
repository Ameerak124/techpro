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
$pharmsno =$data->pharmsno;
$accesskey = $data->accesskey;
$admissionno = $data->admissionno;
$umrno = $data->umrno;
$quantity = $data->quantity;
$order_no =$data->orderno;
$itemcode =$data->itemcode;
$itemname =$data->itemname;
$hsn =$data->hsn;
$price =$data->price;
//$order_no =$data->orderno;
$zero = '0';
$response = array();
try{
if(!empty($accesskey) &&!empty($pharmsno)&&!empty($admissionno)&&!empty($umrno)&&!empty($quantity)&&!empty($order_no)&&!empty($itemcode)&&!empty($itemname)&&!empty($price)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$empdata = $check -> fetch(PDO::FETCH_ASSOC);
$empname = $empdata['userid'];
/* approve voucher start */
$raise_indent = "INSERT INTO `pharmcy_returns`(`order_no`, `indentitem_ref`, `itemname`, `itemcode`, `hsn`, `qty`, `price`, `umrno`, `ipno`,`created_on`, `created_by`, `is_raised`) VALUES (:orderno, :pharmsno, :itemname,:itemcode, :hsn,:quantity,:price,:umrno,:admissionno,CURRENT_TIMESTAMP, :empname, :zero)";
$sbmt = $pdo4 -> prepare($raise_indent);
$sbmt -> bindParam(":orderno", $order_no, PDO::PARAM_STR);
$sbmt -> bindParam(":pharmsno", $pharmsno, PDO::PARAM_STR);
$sbmt -> bindParam(":itemname", $itemname, PDO::PARAM_STR);
$sbmt -> bindParam(":itemcode", $itemcode, PDO::PARAM_STR);
$sbmt -> bindParam(":hsn", $hsn, PDO::PARAM_STR);
$sbmt -> bindParam(":quantity", $quantity, PDO::PARAM_STR);
$sbmt -> bindParam(":price", $price, PDO::PARAM_STR);
$sbmt -> bindParam(":umrno", $umrno, PDO::PARAM_STR);
$sbmt -> bindParam(":admissionno", $admissionno, PDO::PARAM_STR);
$sbmt -> bindParam(":empname", $empname, PDO::PARAM_STR);
$sbmt -> bindParam(":zero", $zero, PDO::PARAM_STR);
$sbmt -> execute();
	if($sbmt -> rowCount() > 0){
		 http_response_code(200);
		 $response['error']= false;
		 $response['message']="Item Submitted!";
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Not Able to Submit!";
     }
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}
else{
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
   unset($pdo4);
   unset($pdoread);
?>
