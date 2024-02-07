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
$accesskey= $data-> accesskey;
$amount= $data-> amount;
$expcat_ref= $data-> expcategory;
$exptype_ref= $data-> exptype;
$branch= $data-> branch;
$expense_details= $data-> expense_details;
$pay_mode= $data-> pay_mode;
$status = 'Pending';
$response = array();
try{
if(!empty($accesskey) && !empty($amount)&& !empty($branch)&& !empty($exptype_ref)&& !empty($expcat_ref) && !empty($expense_details) ){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
     $empdata = $check -> fetch(PDO::FETCH_ASSOC);
     $empname = $empdata['userid'];
     $vid_sbmt = $pdoread -> prepare("SELECT IFNULL(MAX(`voucher_id`),'MCCV0000') AS `voucherid` FROM `ms_billing`.`cashvoucher` LIMIT 1");
     $vid_sbmt -> execute();
    if($vid_sbmt -> rowCount() > 0){
         $data = $vid_sbmt -> fetch(PDO::FETCH_ASSOC);
         $voucherid = $data['voucherid'];
         $voucherid = ++$voucherid;
          /* submit cash voucher */ 
      $co_query = "INSERT INTO ms_billing.cashvoucher
      (voucher_id, amount, branch, expense_type_ref, expense_cat_ref, created_on, created_by, status, expense_details,pay_mode)
      VALUES(:voucherid, :amount,:branch, :expensetyperef, :expensecatref, CURRENT_TIMESTAMP, :empname, :status , :expense_details, :pay_mode)";
      $co_sbmt = $pdo4 -> prepare($co_query);
      $co_sbmt -> bindParam(":voucherid", $voucherid, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":empname", $empname, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":expensecatref", $expcat_ref, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":expensetyperef", $exptype_ref, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":amount", $amount, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":branch", $branch, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":status", $status, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":expense_details", $expense_details, PDO::PARAM_STR);
      $co_sbmt -> bindParam(":pay_mode", $pay_mode, PDO::PARAM_STR);
      $co_sbmt -> execute();
          if($co_sbmt -> rowCount() > 0){
               http_response_code(200);
               $response['error']= false;
               $response['message']="Voucher generated with ID-" .$voucherid;
          }
          else{
               http_response_code(503);
               $response['error']= true;
               $response['message']="Something went wrong!";
          }
      /* submit cash voucher */ 
    }
    else{
     http_response_code(503);
     $response['error']= true;
     $response['message']="Something went wrong!";
    }
}
else{
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
unset($pdo4);
unset($pdoread);
?>

	 