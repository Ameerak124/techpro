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
$response = array();
$response1 = array();
$accesskey = trim($data->accesskey);
$status = trim($data->status);
$accesskey_web = "MjIyMjIyMDI0LTAxLTA0IDE1OjM2OjIy";
try {
  if(!empty($accesskey) ){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role`,accesskey  from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
  $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
	    $result=$sql->fetch(PDO::FETCH_ASSOC);  
 if($result['role'] == 'CFO'){
if($status == 'Pending'){
	
	$insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date,`op_refund_generate`.`remarks` AS remarks, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,if(`stage4_status`='','Pending',stage4_status) as stage1_status,IF(stage1_status='Approved' and stage2_status='Approved' and  stage4_status='','Yes','No') as 'Approve_button',IF(stage1_status='Pending','No','No') as 'Reject_button',`stage3_by` as stage1_by,IF(stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved',concat('https://techpro.medicoveronline.com/refund-bill-pdf.php?a=',:accesskey,'&no=',refund_id),'') AS report ,`op_refund_generate`.`cost_center` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised') and  stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved' and stage4_status='Pending'  AND  `total` > 10000");
  //`payment_history`.`cost_center`=:branch  AND 
   //$insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':accesskey',$accesskey_web,PDO::PARAM_STR);
   $insert_data->execute();
}else if($status == 'Approved'){
		$insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date,`op_refund_generate`.`remarks` AS remarks, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,if(`stage4_status`='','Pending',stage4_status) as stage1_status,IF(stage1_status='Approved' and stage2_status='Approved' and  stage4_status='','Yes','No') as 'Approve_button',IF(stage1_status='Pending','No','No') as 'Reject_button',`stage3_by` as stage1_by,IF(stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved',concat('https://techpro.medicoveronline.com/refund-bill-pdf.php?a=',:accesskey,'&no=',refund_id),'') AS report,`op_refund_generate`.`cost_center` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised','Confirmed') and  stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved' and stage4_status='Approved'  AND  `total` > 10000");
	$insert_data->bindParam(':accesskey',$accesskey_web,PDO::PARAM_STR);
   $insert_data->execute();
}else if($status == 'Rejected'){
 $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date,`op_refund_generate`.`remarks` AS remarks, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,if(`stage4_status`='','Pending',stage4_status) as stage1_status,IF(stage1_status='Approved' and stage2_status='Approved' and  stage4_status='','Yes','No') as 'Approve_button',IF(stage1_status='Pending','No','No') as 'Reject_button',`stage3_by` as stage1_by,IF(stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved',concat('https://techpro.medicoveronline.com/refund-bill-pdf.php?a=',:accesskey,'&no=',refund_id),'') AS report,`op_refund_generate`.`cost_center` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised','Cancelled') and  stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved' and stage4_status='Rejected'  AND  `total` > 10000");
   $insert_data->bindParam(':accesskey',$accesskey_web,PDO::PARAM_STR);
   $insert_data->execute();
}
   if($insert_data->rowCount() > 0){
    http_response_code(200);
    $response['error']=false;
    $response['message']="Data Found";
    $response['sponserdetails']="Sponsor Details";
    $response['billdetails']="Bill Details";
    $response['discount']="Discount #";
    $response['remarks']="Remarks";
    $response['approve']="Approve";
    $response['reject']="Reject";
    $response['approvedby']="Approved By";
    $response['gross']="Gross #";
    $response['pdf']="PDF";
    $sn=0;
      while($res=$insert_data->fetch(PDO::FETCH_ASSOC)){
		  
		  		   $stmt2=$pdoread->prepare("SELECT `username`  FROM `user_logins` WHERE `userid` = :stage1_by");
		  $stmt2->bindParam(':stage1_by',$res['stage1_by'],PDO::PARAM_STR);
          $stmt2->execute();
		  $res1 = $stmt2->fetch(PDO::FETCH_ASSOC);
		  
		    $stmt3=$pdoread->prepare("SELECT `display_name`  FROM `branch_master` WHERE `cost_center` = :branch");
		  $stmt3->bindParam(':branch',$res['cost_center'],PDO::PARAM_STR);
          $stmt3->execute();
		  $res11 = $stmt3->fetch(PDO::FETCH_ASSOC);
		  
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
		$response['refundlist'][$sn]['stage1_by']=$res1['username'];
        $response['refundlist'][$sn]['button1']=$res['Approve_button'];
        $response['refundlist'][$sn]['button2']=$res['Reject_button'];
        $response['refundlist'][$sn]['report']=$res['report'];
		  $response['refundlist'][$sn]['remarks']=$res['remarks'];
		  $response['refundlist'][$sn]['branch']=$res11['display_name'];
     
        $sn++;
      }
   
      }else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
   } 
   }else{
  http_response_code(503);
    $response['error']=true;
    $response['message']="Unauthorized Access";
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
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection Failed";

}
echo json_encode($response);
$pdoread = null;
?>