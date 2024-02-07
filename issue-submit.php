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
$indent_no= $data->indent_no;
$response = array();
try{
	if(!empty($accesskey) &&!empty($indent_no) ){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	$stmtt=$pdo4->prepare("INSERT IGNORE INTO `issue_stock`(`indent_no`, `issue_no`, `issued_by`, `issued_on`, `status`, `department`, `department_code`, `branch`, `branch_code`, `stock_point`, `store`) SELECT `indent_number`,COALESCE((SELECT  Concat('MCISS',LPAD((SUBSTRING_INDEX(`issue_no`,'MCISS',-1)+1),'6','0')) AS issueno FROM `issued_items` order by `sno` desc limit 1),'MCISS000001') AS issueno,:userid,CURRENT_TIMESTAMP,'Issued',`department`, `department_code`, `branch`, `branch_code`,`stock_point`, `store` FROM `department_indent` WHERE `indent_number`=:indent_no");
	$stmtt->bindParam(':userid',$emp['userid'], PDO::PARAM_STR);
	$stmtt->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
	$stmtt->execute(); 
if($stmtt -> rowCount() > 0){
	$sno_issued=$pdoread->lastInsertId();
	$sqlnum="SELECT `issue_no` FROM `issue_stock` WHERE `sno`=:sno";
    $stmt_ist = $pdoread->prepare($sqlnum);
    $stmt_ist->bindParam(":sno", $sno_issued, PDO::PARAM_STR);
    $stmt_ist->execute();
    if($stmt_ist->rowCount()>0){
     $stmtsrow=$stmt_ist->fetch(PDO::FETCH_ASSOC);
    $stmt1=$pdo4->prepare("INSERT  INTO `issued_items`(`queue_sno`,`cen_sno` ,`issue_no`, `indent_no`, `branch`, `item_category`, `item_group`, `item_subgroup`, `itemcode`, `itemname`,priority,`status`,`created_on`,`createdby`,`modified_by`,`modified_on`,`status2`, `ref_itemno`,`mrp`,`batch_no`, `issued_qty`, `indent_qty`, `expiry_date`,`hsn`, `purchase_rate`, `uom`, `sale_rate`) SELECT `sno`, `cen_sno` ,:issueno, `indent_no`, `branch`, `item_category`, `item_group`, `item_subgroup`,`itemcode`, `itemname`, `priority`, `status`, CURRENT_TIMESTAMP, :userid, `modified_by`, `modified_on`, `status2`, `ref_itemno`,`mrp`, `batch_no`, `issued_qty`,`qty`,`exp_date` ,`hsn`, `purchase_rate`,`uom`,`sale_rate` FROM `indent_queue` where `issued_status`='Updated' and indent_no=:indent_no");
	$stmt1->bindParam(':userid',$emp['userid'], PDO::PARAM_STR);
	$stmt1->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
	$stmt1->bindParam(':issueno',$stmtsrow['issue_no'], PDO::PARAM_STR);

	
$stmt1-> execute();
/* } */
	if($stmt1 -> rowCount() > 0){
	
	 $sqlnum1="UPDATE `indent_queue` SET `issued_status`='Received',`issue_no`=:issue_no WHERE `issued_status`='Updated' AND `indent_no`=:indent_no";
    $stmt_up = $pdo4->prepare($sqlnum1);
    $stmt_up->bindParam(":indent_no", $indent_no, PDO::PARAM_STR);
    $stmt_up->bindParam(":issue_no", $stmtsrow['issue_no'], PDO::PARAM_STR);
    $stmt_up->execute();
	http_response_code(200);
          $response['error']= false;
	     $response['message']="Data inserted"; 
	     $response['issueno']=$stmtsrow['issue_no']; 
	
	}else{
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Issue items not created";
	}
}
	
}else{
	     http_response_code(503);
          $response['error']= true;
	     $response['message']="Issue not generated";
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
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdo4);
   unset($pdoread);
?>
