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
$accesskey =  trim($data->accesskey);
$sno=trim($data->sno);
$qty = trim($data->qty);
$department =trim($data->department);
$consumptionid=trim($data->consumptionid);
$response = array();
try{
if(!empty($accesskey)&&!empty($sno)&&!empty($qty)&&!empty($department)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check-> fetch(PDO::FETCH_ASSOC);
$stmt = $pdoread->prepare("SELECT `qty` as qty1  FROM `department_inventory` WHERE `sno` = :sno AND qty>0");
 $stmt->bindParam(':sno',$sno, PDO::PARAM_STR);
       $stmt-> execute();
if($stmt-> rowCount() > 0){
	$chk = $stmt-> fetch(PDO::FETCH_ASSOC);
	if($chk['qty1'] > $qty)
   {
  $val= $qty;
   }
   else{
   $val=$chk['qty1'];
	 }
	
	if(empty($consumptionid)){
 $stmt2 = $pdo4->prepare("INSERT INTO `generate_consumption`(`date_of_consumption`, `consumption_id`, `branch`, `branch_code`, `stock_point_code`, `department`, `department_code`, `status`, `created_by`, `created_on`) SELECT CURRENT_DATE,(COALESCE((SELECT Concat('MCCON',LPAD((SUBSTRING_INDEX(`consumption_id`,'MCCON',-1)+1),'6','0'))  FROM `generate_consumption` order by `sno` desc limit 1),'MCCON000001')) AS consumption_id ,`branch`,`branch_code`, `stockpoint_code`, `department`, `department_code`, 'Pending',  :userid, CURRENT_TIMESTAMP FROM `department_inventory` WHERE sno =:sno");
	 $stmt2->bindParam(':sno',$sno, PDO::PARAM_STR);
	 $stmt2->bindParam(':userid',$emp['userid'], PDO::PARAM_STR);
       $stmt2-> execute();
if($stmt2-> rowCount() > 0){
	$sno_issued=$con->lastInsertId();
	$sqlnum="SELECT `consumption_id` FROM `generate_consumption` WHERE `sno`=:sno";
    $stmt_ist = $pdoread->prepare($sqlnum);
    $stmt_ist->bindParam(":sno", $sno_issued, PDO::PARAM_STR);
    $stmt_ist->execute();
    if($stmt_ist->rowCount()>0){
     $stmtsrow=$stmt_ist->fetch(PDO::FETCH_ASSOC);
	 
	  $item_insert=$pdo4->prepare("INSERT INTO `consumption_item`(`date_of_consumption`, `consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`, `item_name`, `item_code`, `mrp`, `purchase_price`, `batch_no`, `exp_date`, `item_category`, `item_group`, `item_subgroup`, `consumed_qty`, `consumed_value`, `created_by`, `created_on`, `status`,`dept_inventory_sno`) SELECT CURRENT_DATE, :consumption_id,`branch`, `branch_code`,`stock_point`, `stockpoint_code`,`department`, `department_code`, `itemname`, `itemcode`,`mrp`,`purchase_rate`, `batch_no`, `expiry_date`,`item_category`, `item_group`, `item_subgroup`,:qty,(`purchase_rate`*:qty),:userid,CURRENT_TIMESTAMP,'Pending',`sno` FROM `department_inventory` WHERE `sno`=:sno");
	   $item_insert->bindParam(':qty', $val, PDO::PARAM_STR);
	   $item_insert->bindParam(':userid', $emp['userid'], PDO::PARAM_STR);
	   $item_insert->bindParam(':consumption_id', $stmtsrow['consumption_id'], PDO::PARAM_STR);
	   $item_insert->bindParam(':sno', $sno, PDO::PARAM_STR);
       $item_insert->execute();
	  if($item_insert->rowCount()>0){
		  $cons_item=$con->lastInsertId();
		  $log_insert=$pdo4->prepare("INSERT INTO `department_consumption_logs`(`con_item_sno`,`dept_inventory_sno`,`consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`,`itemcode`, `itemname`, `qty`, `batch_no`, `expiry_date`, `status`, `createdby`,`created_on`) SELECT sno,dept_inventory_sno,`consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`,  `item_code`,`item_name`,`consumed_qty`, `batch_no`, `exp_date`,'added' ,`created_by`, `created_on` FROM `consumption_item` WHERE `sno`=:sno");
          $log_insert->bindParam(':sno', $cons_item, PDO::PARAM_STR);
          $log_insert->execute();
		  if($log_insert->rowCount()>0){
	$updt1 = $pdo4->prepare("UPDATE `department_inventory` SET `qty`=(`qty`-:val) WHERE `sno`=:sno");
	$updt1->bindParam(':sno',$sno, PDO::PARAM_STR);
	$updt1->bindParam(':val',$val, PDO::PARAM_STR);
	$updt1-> execute();
	if($updt1-> rowCount() > 0){
	
	$list_items=$pdoread->prepare("SELECT `sno`,`date_of_consumption`, `consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`, `item_name`, `item_code`, `mrp`, `purchase_price`, `batch_no`, `exp_date`, `item_category`, `item_group`, `item_subgroup`, `consumed_qty`, `consumed_value`, `created_by`, `created_on`, `modified_by`, `modified_on`, `status`, `approved_by`, `approved_on`, `consumption_type`, `consumption_category`, `patient_umr`, `patient_ip`, `patient_name` FROM `consumption_item` WHERE  `consumption_id`=:consumption_id AND `status`='Pending'");
	$list_items->bindParam(':consumption_id',$stmtsrow['consumption_id'], PDO::PARAM_STR);
	$list_items->execute();
	if($list_items->rowCount()>0){
		$itemlist=$list_items->fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
	  $response['error']= false;
	  $response['message']= "Data inserted Stock ".$val." Available";
	  $response['consumption_id'] = $stmtsrow['consumption_id'];
	  $response['consumptionitemlist']=$itemlist;
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="NO data inserted"; 
}
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Consumption Item Not Created PLease try once";
}
}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Consumption Not Created PLease try once";
}
}
}else{
	  $item_insert=$pdo4->prepare("INSERT INTO `consumption_item`(`date_of_consumption`, `consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`, `item_name`, `item_code`, `mrp`, `purchase_price`, `batch_no`, `exp_date`, `item_category`, `item_group`, `item_subgroup`, `consumed_qty`, `consumed_value`, `created_by`, `created_on`, `status`,`dept_inventory_sno`) SELECT CURRENT_DATE, :consumption_id,`branch`, `branch_code`,`stock_point`, `stockpoint_code`,`department`, `department_code`, `itemname`, `itemcode`,`mrp`,`purchase_rate`, `batch_no`, `expiry_date`,`item_category`, `item_group`, `item_subgroup`,:qty,(`purchase_rate`*:qty),:userid,CURRENT_TIMESTAMP,'Pending',`sno` FROM `department_inventory` WHERE `sno`=:sno");
	   $item_insert->bindParam(':qty', $val, PDO::PARAM_STR);
	   $item_insert->bindParam(':userid', $emp['userid'], PDO::PARAM_STR);
	   $item_insert->bindParam(':consumption_id', $consumptionid, PDO::PARAM_STR);
	   $item_insert->bindParam(':sno', $sno, PDO::PARAM_STR);
       $item_insert->execute();
	  if($item_insert->rowCount()>0){
		  $cons_item=$con->lastInsertId();
		  $log_insert=$pdo4->prepare("INSERT INTO `department_consumption_logs`(`con_item_sno`,`dept_inventory_sno`,`consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`,`itemcode`, `itemname`, `qty`, `batch_no`, `expiry_date`, `status`, `createdby`,`created_on`) SELECT sno,dept_inventory_sno,`consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`,  `item_code`,`item_name`,`consumed_qty`, `batch_no`, `exp_date`,'added' ,`created_by`, `created_on` FROM `consumption_item` WHERE `sno`=:sno");
          $log_insert->bindParam(':sno', $cons_item, PDO::PARAM_STR);
          $log_insert->execute();
		  if($log_insert->rowCount()>0){
	$updt1 = $pdo4->prepare("UPDATE `department_inventory` SET `qty`=(`qty`-:val) WHERE `sno`=:sno");
	$updt1->bindParam(':sno',$sno, PDO::PARAM_STR);
	$updt1->bindParam(':val',$val, PDO::PARAM_STR);
	$updt1-> execute();
	if($updt1-> rowCount() > 0){
	
	$list_items=$pdoread->prepare("SELECT `sno`,`date_of_consumption`, `consumption_id`, `branch`, `branch_code`, `stock_point`, `stock_point_code`, `department`, `department_code`, `item_name`, `item_code`, `mrp`, `purchase_price`, `batch_no`, `exp_date`, `item_category`, `item_group`, `item_subgroup`, `consumed_qty`, `consumed_value`, `created_by`, `created_on`, `modified_by`, `modified_on`, `status`, `approved_by`, `approved_on`, `consumption_type`, `consumption_category`, `patient_umr`, `patient_ip`, `patient_name` FROM `consumption_item` WHERE  `consumption_id`=:consumption_id AND `status`='Pending'");
	$list_items->bindParam(':consumption_id',$consumptionid, PDO::PARAM_STR);
	$list_items->execute();
	if($list_items->rowCount()>0){
		$itemlist=$list_items->fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
	  $response['error']= false;
	  $response['message']= "Data inserted Stock ".$val." Available";
	  $response['consumption_id'] = $consumptionid;
	   //$itemlist=$list_items->fetch(PDO::FETCH_ASSOC);
	    $response['consumptionitemlist']=$itemlist;	
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="NO data inserted"; 
}
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Consumption Item Not Created PLease try once";
}
}	
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Stock Not Available";
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
}catch(Exception $e) {

	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
