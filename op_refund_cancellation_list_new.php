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
$accesskey = trim($data->accesskey);
$fromdate = date('Y-m-d', strtotime($data->fromdate));
$todate = date('Y-m-d', strtotime($data->todate));
try {
  if(!empty($accesskey) && !empty($fromdate) && !empty($todate)){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role`  from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
  $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
	    $result=$sql->fetch(PDO::FETCH_ASSOC); 

 if($result['role'] == 'AUDIT'){
 	  
   $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,`total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date,`op_refund_generate`.`remarks` AS remarks, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,if(`stage1_status`='','Pending',stage1_status) as `stage1_status`,IF(stage1_status='','Yes','No') as Approve_button,IF(stage1_status='','Yes','No') as Reject_button,`stage1_by`,IF(stage1_status='Approved' and stage2_status='Approved' and  stage3_status='Approved',concat('https://techpro.medicoveronline.com/refund-bill-pdf.php?a=',:accesskey,'&no=',refund_id),'') AS report FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised', 'Confirmed')  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
    $insert_data->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
   $insert_data->execute();
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
        $response['refundlist'][$sn]['button1']=$res['Approve_button'];
        $response['refundlist'][$sn]['button2']=$res['Reject_button'];
        $response['refundlist'][$sn]['report']=$res['report'];
        $response['refundlist'][$sn]['remarks']=$res['remarks'];
        $sn++;
      }
      }else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
   }
 
   
}else if($result['role'] == 'Center Head' || $result['role'] == 'Unit Head'){
	  
	  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date,`op_refund_generate`.`remarks` AS remarks, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,if(`stage2_status`='','Pending',stage2_status) as stage1_status,IF(stage1_status='Approved' and stage2_status='','Yes','No') as 'Approve_button',IF(stage1_status='Pending','No','No') as 'Reject_button',`stage2_by` as stage1_by,IF(stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved',concat('https://techpro.medicoveronline.com/refund-bill-pdf.php?a=',:accesskey,'&no=',refund_id),'') AS report FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised', 'Confirmed')  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
    $insert_data->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
   $insert_data->execute();
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
        $response['refundlist'][$sn]['button1']=$res['Approve_button'];
        $response['refundlist'][$sn]['button2']=$res['Reject_button'];
        $response['refundlist'][$sn]['report']=$res['report'];
		  $response['refundlist'][$sn]['remarks']=$res['remarks'];
     
        $sn++;
      }
      }else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
   }  

 }else if($result['role'] == 'CFO'){

	  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date,`op_refund_generate`.`remarks` AS remarks, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,if(`stage3_status`='','Pending',stage3_status) as stage1_status,IF(stage1_status='Approved' and stage2_status='Approved' and  stage3_status='','Yes','No') as 'Approve_button',IF(stage1_status='Pending','No','No') as 'Reject_button',`stage3_by` as stage1_by,IF(stage1_status='Approved' and stage2_status='Approved' and stage3_status='Approved',concat('https://techpro.medicoveronline.com/refund-bill-pdf.php?a=',:accesskey,'&no=',refund_id),'') AS report FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised', 'Confirmed')  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate AND  `total` > 10000");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
    $insert_data->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
   $insert_data->execute();
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
        $response['refundlist'][$sn]['button1']=$res['Approve_button'];
        $response['refundlist'][$sn]['button2']=$res['Reject_button'];
        $response['refundlist'][$sn]['report']=$res['report'];
		  $response['refundlist'][$sn]['remarks']=$res['remarks'];
     
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";

}
echo json_encode($response);
$pdoread = null;
?>