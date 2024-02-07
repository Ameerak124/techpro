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
include "radiology-data-save.php";
$data =json_decode(file_get_contents("php://input"));
$accesskey=$data->accesskey;
$billno=$data->billno;
$approvalstatus=$data->approvalstatus;
$comments=$data->comments;
$response =array();
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
if(!empty($accesskey)&& !empty($approvalstatus)&& !empty($billno)) {

$check =$pdoread->prepare("SELECT `userid`, `cost_center`,`role` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active' AND `role` IN ('Center Head','EA')");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0){
$validate = $pdoread->prepare("SELECT `inv_no`,`approved_by`,(CASE WHEN `approved_status` = 'No Update' THEN 'Pending' ELSE `approved_status` END) AS approved_status,DATE_FORMAT(`approvedon`,'%d-%b-%Y') AS chapprovedon FROM `op_billing_generate` WHERE (`itemwise_disc`+`discount_val`) > 0 AND `cost_center` = :cost_center AND  `status` = 'Confirmed' AND `inv_no` = :billno");
$validate->bindParam(':billno', $billno, PDO::PARAM_STR);
$validate->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$validate->execute();
if($validate->rowCount() > 0){
$valid=$validate->fetch(PDO::FETCH_ASSOC);
if($valid['approved_status'] == 'Pending' && $result['role'] == 'Center Head'){

if($approvalstatus=="Approved"){
	$ch = $pdo4->prepare("UPDATE `op_billing_generate` SET `approved_by` = :userid,`approved_status` = :approvalstatus,`approvedon` = CURRENT_TIMESTAMP WHERE `inv_no` = :billno AND `cost_center` = :cost_center AND `approved_status` = 'No Update'");
$ch->bindParam(':approvalstatus', $approvalstatus1, PDO::PARAM_STR);
$ch->bindParam(':billno', $billno, PDO::PARAM_STR);
$ch->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ch->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$approvalstatus1='Approved';
}else{
	$ch = $pdo4->prepare("UPDATE `op_billing_generate` SET `approved_by` = :userid,`approved_status` = :approvalstatus,`approvedon` = CURRENT_TIMESTAMP,billing_remarks=:comments WHERE `inv_no` = :billno AND `cost_center` = :cost_center AND `approved_status` = 'No Update'");
$ch->bindParam(':comments', $comments, PDO::PARAM_STR);
$ch->bindParam(':approvalstatus', $approvalstatus1, PDO::PARAM_STR);
$ch->bindParam(':billno', $billno, PDO::PARAM_STR);
$ch->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ch->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$approvalstatus1='Pending For Clarification';
}
$ch->execute();
if($ch->rowCount() > 0){
        $response['error']=false;
        $response['message']='Discount '.$approvalstatus;
}else{
        $response['error']=true;
        $response['message']='Sorry! changes are not allowed';
}
}else if($valid['approved_status'] == 'Approved' && $result['role'] == 'EA'){
	if($approvalstatus=="Approved"){
        $ch = $pdo4->prepare("UPDATE `op_billing_generate` SET `ea_approval_by` = :userid,`ea_approval_status` = :approvalstatus,`ea_approval_on` = CURRENT_TIMESTAMP WHERE `inv_no` = :billno AND `cost_center` = :cost_center AND `approved_status` = 'Approved' AND `ea_approval_status` IN ('','No Update')");
        $ch->bindParam(':approvalstatus', $approvalstatus, PDO::PARAM_STR);
        $ch->bindParam(':billno', $billno, PDO::PARAM_STR);
        $ch->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
        $ch->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
        $ch->execute();
	}else{
		$ch = $pdo4->prepare("UPDATE `op_billing_generate` SET `ea_approval_by` = :userid,`ea_approval_status` = :approvalstatus,`ea_approval_on` = CURRENT_TIMESTAMP,billing_remarks=:comments  WHERE `inv_no` = :billno AND `cost_center` = :cost_center AND `approved_status` = 'Approved' AND `ea_approval_status` IN ('','No Update')");
        $ch->bindParam(':comments', $comments, PDO::PARAM_STR);
        $ch->bindParam(':approvalstatus', $approvalstatus1, PDO::PARAM_STR);
        $ch->bindParam(':billno', $billno, PDO::PARAM_STR);
        $ch->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
        $ch->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
		$approvalstatus1='Pending For Clarification';
        $ch->execute();
	}
        if($ch->rowCount() > 0){
			    http_response_code(200);
                $response['error']=false;
                $response['message']='Discount '.$approvalstatus;
        }else{
			     http_response_code(503);
                $response['error']=true;
                $response['message']='Sorry! changes are not allowed';
        }

}else{
	  http_response_code(503);
        $response['error']=true;
        $response['message']='Discount is already '.$valid['approved_status'].' on '.$valid['chapprovedon'];
}
}else{
	    http_response_code(400);
        $response['error']=true;
        $response['message']='Please choose proper bill number';
}

}else {
	    http_response_code(400);
        $response['error']=true;
        $response['message']='Access Denied!!';
}
} else {
	  http_response_code(400);
        $response['error']=true;
        $response['message']='Sorry! Some details are missing';
}
} catch(PDOException $e) {
       http_response_code(503);
       $response['error'] = true;
       $response['message']= "Connection failed";
       $errorlog = $pdo -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
   $errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
   $errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
   $errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
   $errorlog -> execute();
}
   echo json_encode($response);
   $pdo4 = null;
   $pdoread = null;
   ?>