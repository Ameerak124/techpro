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
//data credentials
 $data = json_decode(file_get_contents("php://input"));
$itemname = $data->itemname;
$itemcode = $data->itemcode;
$quantity = $data->quantity;
$drug_price = $data->drug_price;
$umrno = $data->umrno;
$preorder = $data->preorder;
$admissionno = $data->admissionno;
$accesskey = $data->accesskey;
$batchno = $data->batchno;
$sno = $data->sno;
$hsn = $data->hsn;
$uom = $data->uom;
$response=array();
try {
if(!empty($accesskey) && !empty($itemname) && !empty($itemcode) &&  !empty($quantity)&& !empty($drug_price) && !empty($umrno) && !empty($admissionno) && !empty($batchno) && !empty($sno)  && !empty($uom)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
	if(empty($preorder)){
     $adddrug_query = "INSERT INTO `pharmcy_indent`(`hsn`,`umr_no`, `ip_no`, `order_no`, `drug_code`, `drug_name`,`quantity`, `drug_price`, `created_by`, `created_on`) SELECT :hsn,:umrno,:ipno,(COALESCE(( Concat('SYUOD',LPAD((SUBSTRING_INDEX(order_no,'SYUOD',-1)+1),'7','0')) ),'SYUOD0000001')) AS order_no,:drug_code,:drug_name,:quantity,:drug_price,:userid, CURRENT_TIMESTAMP FROM `pharmcy_indent` order by `sno` desc limit 1";
     $adddrug_sbmt = $pdo4 -> prepare($adddrug_query);
     $adddrug_sbmt -> bindParam(":drug_code", $itemcode, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":drug_name", $itemname, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":drug_price", $drug_price, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":userid", $result['userid'], PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":umrno", $umrno, PDO::PARAM_STR, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":ipno", $admissionno, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":hsn", $hsn, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":quantity", $quantity, PDO::PARAM_STR);
     $adddrug_sbmt -> execute();
	 
  }else{
	 $adddrug_query = "INSERT INTO `pharmcy_indent`(`hsn`,`umr_no`, `ip_no`, `order_no`, `drug_code`, `drug_name`,`quantity`, `drug_price`, `created_by`, `created_on`) VALUES (:hsn,:umrno,:ipno,:order_no, :drug_code, :drug_name,:quantity, :drug_price, :userid, CURRENT_TIMESTAMP WHERE ip_no =:ipno )";
     $adddrug_sbmt = $pdo4 -> prepare($adddrug_query);
     $adddrug_sbmt -> bindParam(":drug_code", $itemcode, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":drug_name", $itemname, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":drug_price", $drug_price, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":userid", $result['userid'], PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":umrno", $umrno, PDO::PARAM_STR, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":ipno", $admissionno, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":order_no", $preorder, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":hsn", $hsn, PDO::PARAM_STR);
     $adddrug_sbmt -> bindParam(":quantity", $quantity, PDO::PARAM_STR);
     $adddrug_sbmt -> execute(); 
  }
 if($adddrug_sbmt -> rowCount() > 0){
	$sno_issued=$con->lastInsertId();
	$sqlnum="SELECT `order_no` FROM `pharmcy_indent` WHERE `sno`=:sno";
    $stmt_ist = $con->prepare($sqlnum);
    $stmt_ist->bindParam(":sno", $sno_issued, PDO::PARAM_STR);
    $stmt_ist->execute();
	 }
    if($stmt_ist->rowCount()>0){
     $stmtsrow=$stmt_ist->fetch(PDO::FETCH_ASSOC);	 
		 http_response_code(200);
          $response['error']= false;
	     $response['message']="Item Added Successfully";
		 $response['preorder'] = $stmtsrow['order_no'];
     }
     else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Sorry Something Went Wrong";
     }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
    // $response['accesskey'] = $accesskey;
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