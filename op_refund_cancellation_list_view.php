<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$refund_id = trim($data->refund_id);
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
 if(!empty($accesskey) && !empty($refund_id) ){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role` from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
  $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
  $result=$sql->fetch(PDO::FETCH_ASSOC);  
$items_list=$pdoread->prepare("SELECT `op_billing_generate`.`patient_name`, `op_billing_generate`.`mobile`AS contact,`op_refund_generate`.`umr_no` AS umr_no ,`billno`,`raised_line_of_times`,`total`,DATE_FORMAT(op_billing_generate.`modified_on`,'%d-%b-%Y')AS created_date,DATE_FORMAT(op_billing_generate.`modified_on`,'%H:%i %p')AS created_time,`op_refund_generate`.`refund_id`,`op_refund_generate`.`remarks`,`op_refund_generate`.`bulk_discount` AS bulk_discount,if(aadhar_file='','',CONCAT('mobile-api/',`op_refund_generate`.`aadhar_file`)) as 'aadhar_file', if(bank_file='','',CONCAT('mobile-api/',`op_refund_generate`.`bank_file`)) as 'bank_file' FROM `op_refund_generate`
LEFT JOIN `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no`
WHERE `op_refund_generate`.`bill_status`='Approved' AND `op_refund_generate`.`refund_status`='Raised' AND `op_refund_generate`.`cost_center`=:branch AND `op_refund_generate`.`refund_id`=:refund_id ");
$items_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$items_list->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
$items_list->execute();  
$items_lists = $items_list->fetch(PDO::FETCH_ASSOC);
if($items_list -> rowCount() > 0){
http_response_code(200);
$response['error']=false;
$response['message']="Data Found";
$response['role']=$result['role'];
$response['patient_name']=$items_lists['patient_name'];
$response['contact']=$items_lists['contact'];
$response['umr_no']=$items_lists['umr_no'];
$response['billno']=$items_lists['billno'];
$response['raised_line_of_times']=$items_lists['raised_line_of_times'];
$response['total']=$items_lists['total'];
$response['created_date']=$items_lists['created_date'];
$response['refund_id']=$items_lists['refund_id'];
$response['created_time']=$items_lists['created_time'];
$response['remarks']=$items_lists['remarks'];
$response['bulk_discount']=$items_lists['bulk_discount'];
$response['aadhar_file']=$items_lists['aadhar_file'];
$response['bank_file']=$items_lists['bank_file'];
   
    $total_items_value=$pdoread->prepare(" SELECT ROUND(SUM(`total`),2) AS item_total FROM `op_refund_history` WHERE `status`='Checked' AND `refund_id`=:rid AND `cost_center`=:branch ");
    $total_items_value->bindParam(':rid', $refund_id, PDO::PARAM_STR);
    $total_items_value->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
    $total_items_value->execute();
    $final_total=$total_items_value->fetch(PDO::FETCH_ASSOC);
    $response['item_total']=$final_total['item_total'];
        
        $get_list=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`requisition_no`, `category`, `servicecode`, `service_name`, `quantity`,  `total` FROM (SELECT @a:=0) AS a, `op_refund_history` WHERE `status`='Checked' AND `cost_center`=:branch AND `refund_id`=:rid");
        $get_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
        $get_list->bindParam(':rid', $refund_id, PDO::PARAM_STR);
        $get_list->execute();
        $s=0;
        while($final_list=$get_list->fetch(PDO::FETCH_ASSOC)){
            $response['refundviewlist'][$s]['sno']=$final_list['sno'];
            $response['refundviewlist'][$s]['requisition_no']=$final_list['requisition_no'];
            $response['refundviewlist'][$s]['category']=$final_list['category'];
            $response['refundviewlist'][$s]['servicecode']=$final_list['servicecode'];
            $response['refundviewlist'][$s]['service_name']=$final_list['service_name'];
            $response['refundviewlist'][$s]['quantity']=$final_list['quantity'];
            $response['refundviewlist'][$s]['total']=$final_list['total'];
            $s++;

        }
}else{
    http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
}
//logic code end
}else{
  http_response_code(400);
  $response['error']= true;
  $response['message']= "Access denied!";
}
}else{
    http_response_code(400);
    $response['error']= true;
    $response['message']= "Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	$errorlog = $pdoread -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $accountname, PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
echo json_encode($response);
$pdoread = null;
?>