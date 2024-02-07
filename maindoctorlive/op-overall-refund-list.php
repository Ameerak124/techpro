<?php
header("Content-Type: application/json; charset=UTF-8");
include ('pdo-db.php');
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey=trim($data->accesskey);
$fromdate=trim($data->fromdate);
$todate=trim($data->todate);
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try { 
if(!empty($accesskey)&& !empty($fromdate)&& !empty($todate)) {
    //access verification start 
    $check = $pdoread -> prepare("SELECT `userid` ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check -> execute();
    $result=$check->fetch(PDO::FETCH_ASSOC);
    //accesskey verified//
if($check->rowcount()>0) {
$query=$pdoread->prepare(" SELECT `refund_id`, `umr_no`,`invoice_no`,`billno`, `line_of_items`, `raised_line_of_times`, `total`, `stage2_status`,`stage3_status`, DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y')AS refund_date,
(CASE WHEN `refund_status`='No Update' THEN '--' ELSE `refund_status` END) AS
refund_status ,`cancellation_status` FROM `op_refund_generate` WHERE  DATE(`op_refund_generate`.`created_on`) BETWEEN :fromdate and :todate ");
$query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$query->bindParam(':todate', $todate, PDO::PARAM_STR);
// $query->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount() > 0){
	http_response_code(200);
    $response['error']=false;
    $response['message']="Data Found";
    $sn=0;
    while($res=$query->fetch(PDO::FETCH_ASSOC)){
        $response['list'][$sn]['refund_id']=$res['refund_id'];
        $response['list'][$sn]['umr_no']=$res['umr_no'];
        $response['list'][$sn]['billno']=$res['billno'];
        $response['list'][$sn]['invoice_no']=$res['invoice_no'];
        $response['list'][$sn]['line_of_items']=$res['line_of_items'];
        $response['list'][$sn]['raised_line_of_times']=$res['raised_line_of_times'];
        $response['list'][$sn]['total']=$res['total'];
        $response['list'][$sn]['unit_status']=$res['stage2_status'];
        $response['list'][$sn]['cluster_status']=$res['stage3_status'];
        $response['list'][$sn]['refund_status']=$res['refund_status'];
        $response['list'][$sn]['cancellation_status']=$res['cancellation_status'];
        $response['list'][$sn]['refund_date']=$res['refund_date'];
        $sn++;
    }
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
}
}else {
	http_response_code(400);
	$response['error']=true;
	$response['message']='Access Denied Please re-login Again';
}  
    //Access Error toast
}else {
	http_response_code(400);
	$response['error']=true;
	$response['message']='Some Details are Missing';
}   
    //Mandatory fields Error toast      
} catch(PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message']= "Connection failed";
    $errorlog = $pdoread -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
    $errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
    $errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
    $errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
    $errorlog -> execute();
 }
    //Connection failed Error toast
echo json_encode($response);
$pdoread = null;
?>