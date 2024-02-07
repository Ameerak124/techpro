<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$fromdate = trim($data->fromdate);
$todate = trim($data->todate);
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread->prepare("SELECT `userid`,`cost_center`,`role`,(CASE WHEN `role` IN ('Center Head','CFO') THEN 'Show' ELSE 'Hide' END) AS visibility FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
///logic code start
// if($result['role']=='CFO'){
$items_list=$pdoread->prepare("SELECT @a:=@a+1 AS sno,CONCAT(`umr_registration`.`patient_name`,'',`umr_registration`.`middle_name`,' ',`umr_registration`.`last_name`)AS patient_name, `umr_registration`.`mobile_no`AS contact,`op_refund_generate`.`umr_no` AS umr_no ,`billno`,`raised_line_of_times`,`total`,DATE_FORMAT(`created_on`,'%d-%b-%Y')AS created_date,DATE_FORMAT(`created_on`,'%H:%i %p')AS created_time, `op_refund_generate`.`refund_id` ,`op_refund_generate`.`cancellation_id`,`op_refund_generate`.`invoice_no`,
(CASE WHEN `op_refund_generate`.`stage3_status`='Pending' AND `op_refund_generate`.`stage4_status`='Pending' THEN 'viewbill' 
 WHEN `op_refund_generate`.`stage3_status`='Approved' AND `op_refund_generate`.`stage4_status`='Pending' THEN 'Payment'
 ELSE 'pdf' END) AS loop_status, (CASE WHEN `op_refund_generate`.`stage3_status`='Pending' AND `op_refund_generate`.`stage4_status`='Pending' THEN 'Approval Pending' 
 WHEN `op_refund_generate`.`stage3_status`='Approved' AND `op_refund_generate`.`stage4_status`='Pending' THEN 'Payment Pending'
 ELSE 'Confirmed' END) AS bill_status, `op_refund_generate`.`refund_status` FROM (SELECT @a:=0) AS a,`op_refund_generate` LEFT JOIN `umr_registration` ON `umr_registration`.`umrno`=`op_refund_generate`.`umr_no` WHERE `op_refund_generate`.`bill_status`='Approved' AND `op_refund_generate`.`refund_status`IN('Raised','Confirmed') AND `op_refund_generate`.`cost_center`=:branch AND `op_refund_generate`.`stage1_status`='Approved' AND `op_refund_generate`.`stage2_status`='Approved' AND `op_refund_generate`.`cancellation_status`='Approved' AND date(`op_refund_generate`.`created_on`)between :fromdate and :todate ");
$items_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$items_list->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$items_list->bindParam(':todate', $todate, PDO::PARAM_STR);
$items_list->execute();
$sn=0;
if($items_list->rowCount()>0){
    http_response_code(200);
    $response['error']=false;
    $response['message']="Data Found";
    $response['visibility']= $result['visibility'];
    while($res=$items_list->fetch(PDO::FETCH_ASSOC)){
        $response['list'][$sn]['sno']=$res['sno'];
        $response['list'][$sn]['patient_name']=$res['patient_name'];
        $response['list'][$sn]['contact']=$res['contact'];
        $response['list'][$sn]['umr_no']=$res['umr_no'];
        $response['list'][$sn]['billno']=$res['billno'];
        $response['list'][$sn]['raised_line_of_times']=$res['raised_line_of_times'];
        $response['list'][$sn]['total']=$res['total'];
        $response['list'][$sn]['created_date']=$res['created_date'];
        $response['list'][$sn]['created_time']=$res['created_time'];
        $response['list'][$sn]['refund_id']=$res['refund_id'];
        $response['list'][$sn]['cancellation_id']=$res['cancellation_id'];
        $response['list'][$sn]['bill_status']=$res['bill_status'];
        $response['list'][$sn]['loop_status']=$res['loop_status'];
        $response['list'][$sn]['refund_status']=$res['refund_status'];
        $response['list'][$sn]['invoice_no']=$res['invoice_no'];
        
        $sn++;
    }
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
    $response['visibility']= $result['visibility'];
}
// }else{
//    $response['error']=true;
//    $response['message']="Please Check Your Role";
// }
}else{
  http_response_code(400);
  $response['error']= true;
$response['message']= "Session expired. Please re-login again";
}
}else{
	http_response_code(400);
    $response['error']= true;
    $response['message']= "Sorry, some details are missing";
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