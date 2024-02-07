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
    $pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    if(!empty($accesskey) && !empty($fromdate) && !empty($todate)){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role`  from `user_logins` WHERE accesskey =:accesskey AND `status` = 'Active'");
    $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
  $result=$sql->fetch(PDO::FETCH_ASSOC);  
  //  $insert_data=$con->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,`stage1_status`,`stage1_by` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status`='Raised' AND `stage1_status`='Pending' AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
  $insert_data=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`op_billing_generate`.`patient_name`, `refund_id`, `op_refund_generate`.`invoice_no`, `op_refund_generate`.`umr_no`, `op_refund_generate`.`billno`,  `op_refund_generate`.`original_value` as gross, `item_wise_discount` as discount,   `total` as 'bill_amt', `op_billing_generate`.`organization_code`,`op_billing_generate`.`organization_name`,DATE_FORMAT(`op_refund_generate`.`created_on`,'%d-%b-%Y') AS Bill_date, DATE_FORMAT(`op_refund_generate`.`created_on`,'%h:%i %p') AS Bill_time,`stage1_status`,`stage1_by` FROM (SELECT @a:=0) AS a,`op_refund_generate` left join `op_billing_generate` ON `op_billing_generate`.`invoice_no`=`op_refund_generate`.`invoice_no` WHERE  `bill_status`='Approved' AND `refund_status` IN ('Raised', 'Confirmed')  AND `op_refund_generate`.`cost_center`=:branch  AND  date(op_refund_generate.`created_on`) BETWEEN :fromdate AND :todate ");
   $insert_data->bindParam(':branch',$result['cost_center'],PDO::PARAM_STR);
   $insert_data->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
   $insert_data->bindParam(':todate',$todate,PDO::PARAM_STR);
   $insert_data->execute();
   if($insert_data->rowCount() > 0){
    $response['error']=false;
    $response['message']="Data Found";
    $sn=0;
      while($res=$insert_data->fetch(PDO::FETCH_ASSOC)){
        $response['list'][$sn]['sno']=$res['sno'];
        $response['list'][$sn]['patient_name']=$res['patient_name'];
        $response['list'][$sn]['refund_id']=$res['refund_id'];
        $response['list'][$sn]['invoice_no']=$res['invoice_no'];
        $response['list'][$sn]['umr_no']=$res['umr_no'];
        $response['list'][$sn]['billno']=$res['billno'];
        $response['list'][$sn]['gross']=$res['gross'];
        $response['list'][$sn]['discount']=$res['discount'];
        $response['list'][$sn]['bill_amt']=$res['bill_amt'];
        $response['list'][$sn]['organization_code']=$res['organization_code'];
        $response['list'][$sn]['organization_name']=$res['organization_name'];
        $response['list'][$sn]['cancellation_id']=$res['cancellation_id'];
        $response['list'][$sn]['Bill_date']=$res['Bill_date'];
        $response['list'][$sn]['Bill_time']=$res['Bill_time'];
        $response['list'][$sn]['stage1_status']=$res['stage1_status'];
        $response['list'][$sn]['stage1_by']=$res['stage1_by'];
        $sn++;
      }
   }else{
    $response['error']=true;
    $response['message']="No Data Found";
   }
 
 }else{
    $response['error']=true;
    $response['message']='Access Denied';
  }
  }else{
    $response['error']=true;
    $response['message']='sorry some details are missing';       
  }
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
	$errorlog = $pdoread -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
echo json_encode($response);
$pdoread = null;
?>