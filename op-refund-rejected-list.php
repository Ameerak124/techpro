<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$fromdate = date('Y-m-d', strtotime($data->fromdate));
$todate = date('Y-m-d', strtotime($data->todate));
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
  if(!empty($accesskey) && !empty($fromdate) && !empty($todate)){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role`  from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
  $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
    if($result['role'] == 'AUDIT'){
 
  $result=$sql->fetch(PDO::FETCH_ASSOC);  
  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,`stage1_status`,`stage1_by` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `stage1_status`='Rejected' AND `stage2_status`='Pending' and `stage3_status`='Pending' and `stage4_status`='Pending'  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
   $insert_data->execute();
   
  }else if($result['role'] == 'Center Head'){
  $result=$sql->fetch(PDO::FETCH_ASSOC);  
  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,`stage1_status`,`stage1_by` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `stage1_status`='Rejected' AND `stage2_status`='Rejected' and `stage3_status`='Pending' and `stage4_status`='Pending'  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
   $insert_data->execute();
  }else if($result['role'] == 'Unit Head'){
  $result=$sql->fetch(PDO::FETCH_ASSOC);  
  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,`stage1_status`,`stage1_by` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `stage1_status`='Rejected' AND `stage2_status`='Rejected' and `stage3_status`='Rejected' and `stage4_status`='Pending'  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
   $insert_data->execute();
  }else if($result['role'] == 'ED'){
	   $result=$sql->fetch(PDO::FETCH_ASSOC);  
  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,`stage1_status`,`stage1_by` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `stage1_status`='Rejected' AND `stage2_status`='Rejected' and `stage3_status`='Rejected' and `stage4_status`='Rejected'  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
   $insert_data->execute(); 
  }
   if($insert_data->rowCount() > 0){
    http_response_code(200);
    $response['error']=false;
    $response['message']="Data Found";
    $sn=0;
      while($res=$insert_data->fetch(PDO::FETCH_ASSOC)){
        $response['refundlist'][$sn]['sno']=$res['sno'];
        $response['refundlist'][$sn]['patient_name']=$res['patient_name'];
        $response['refundlist'][$sn]['refund_id']=$res['refund_id'];
        $response['refundlist'][$sn]['invoice_no']=$res['invoice_no'];
        $response['refundlist'][$sn]['umr_no']=$res['umr_no'];
        $response['refundlist'][$sn]['billno']=$res['billno'];
        $response['refundlist'][$sn]['gross']=$res['gross'];
        $response['refundlist'][$sn]['discount']=$res['discount'];
        $response['refundlist'][$sn]['bill_amt']=$res['bill_amt'];
        $response['refundlist'][$sn]['organization_code']=$res['organization_code'];
        $response['refundlist'][$sn]['organization_name']=$res['organization_name'];
        $response['refundlist'][$sn]['cancellation_id']=$res['cancellation_id'];
        $response['refundlist'][$sn]['Bill_date']=$res['Bill_date'];
        $response['refundlist'][$sn]['Bill_time']=$res['Bill_time'];
        $response['refundlist'][$sn]['stage1_status']=$res['stage1_status'];
        $response['refundlist'][$sn]['stage1_by']=$res['stage1_by'];
        $sn++;
      }
      }else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
   }
   }else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
  }
  }else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! some details are missing';       
  }
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
	$errorlog = $pdo4 -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>