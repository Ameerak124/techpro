<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php"; // Include your database connection details here.
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$refund_id = trim($data->refund_id);
$remarks = trim($data->remarks);
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try{
 if(!empty($accesskey) && !empty($refund_id) ){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role` from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
  $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
  $result=$sql->fetch(PDO::FETCH_ASSOC);
  
        $query=$pdoread->prepare("SELECT `invoice_no`, `umr_no`, `billno`, `mobileno`, `total` FROM `op_refund_generate` WHERE `refund_id`=:refund_id");
            $query->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
			$query->execute();
            $result1=$query->fetch(PDO::FETCH_ASSOC);
  
  if($result['role'] == 'AUDIT'){      

            $update = $pdo4->prepare("UPDATE op_refund_generate SET `stage1_status`='Approved', `stage1_by`=:userid, `stage1_on`=CURRENT_TIMESTAMP,`stage1_branch`=:cost_center WHERE `refund_id` = :refund_id AND cost_center=:cost_center AND `stage1_status`='Pending'");
			
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
			if($update->rowCount() > 0){
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = 'Data Approved.';

			$query_logs=$pdo4->prepare("INSERT IGNORE INTO `op_refund_logs`(`refund_id`, `invoice_no`, `umr_no`, `billno`, `mobileno`, `total`, `created_by`, `created_on`, `status`, `designation`, `remarks`) VALUES (:refund_id, :invoice_no, :umr_no, :billno, :mobileno, :total, :created_by, CURRENT_TIMESTAMP, 'Approved', :designation, :remarks)"); 
			
        $query_logs->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
	    $query_logs->bindParam(':invoice_no', $result1['invoice_no'], PDO::PARAM_STR);
        $query_logs->bindParam(':umr_no', $result1['umr_no'], PDO::PARAM_STR);
	    $query_logs->bindParam(':billno', $result1['billno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':mobileno', $result1['mobileno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':total', $result1['total'], PDO::PARAM_STR);
	    $query_logs->bindParam(':created_by', $result['userid'], PDO::PARAM_STR);
	    $query_logs->bindParam(':designation', $result['role'], PDO::PARAM_STR);
		$query_logs->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $query_logs->execute();
			
			}else{
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = 'Data Not Approved';
            }	  
       
    } elseif($result['role'] == 'Center Head'){
		if($result1['total'] <='10000'){
		
		$update = $pdo4->prepare("UPDATE op_refund_generate SET `stage2_status`='Approved', `stage2_by`=:userid, `stage2_on`=CURRENT_TIMESTAMP, `stage2_branch`=:cost_center, `stage3_status`='Approved', `stage3_by`=:userid,`stage3_on`=CURRENT_TIMESTAMP, `stage3_branch`=:cost_center WHERE `refund_id` = :refund_id AND cost_center=:cost_center AND `stage2_status`='Pending' AND `stage3_status`='Pending' AND `stage1_status`='Approved'");
		
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
			if($update->rowCount() > 0){
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = 'Data Approved.';
			
			$query_logs=$pdo4->prepare("INSERT IGNORE INTO `op_refund_logs`(`refund_id`, `invoice_no`, `umr_no`, `billno`, `mobileno`, `total`, `created_by`, `created_on`, `status`, `designation`, `remarks`) VALUES (:refund_id, :invoice_no, :umr_no, :billno, :mobileno, :total, :created_by, CURRENT_TIMESTAMP, 'Approved', :designation, :remarks)"); 
			
        $query_logs->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
	    $query_logs->bindParam(':invoice_no', $result1['invoice_no'], PDO::PARAM_STR);
        $query_logs->bindParam(':umr_no', $result1['umr_no'], PDO::PARAM_STR);
	    $query_logs->bindParam(':billno', $result1['billno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':mobileno', $result1['mobileno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':total', $result1['total'], PDO::PARAM_STR);
	    $query_logs->bindParam(':created_by', $result['userid'], PDO::PARAM_STR);
	    $query_logs->bindParam(':designation', $result['role'], PDO::PARAM_STR);
		$query_logs->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $query_logs->execute();
			}else{
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = 'Data Not Approved';
            }		
				
	}else{
		
		$update = $pdo4->prepare("UPDATE op_refund_generate SET `stage2_status`='Approved', `stage2_by`=:userid, `stage2_on`=CURRENT_TIMESTAMP,`stage2_branch`=:cost_center WHERE `refund_id` = :refund_id AND cost_center=:cost_center AND `stage2_status`='Pending' AND `stage1_status`='Approved'");
		
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
			if($update->rowCount() > 0){
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = 'Data Approved.';
		
			 
			$query_logs=$pdo4->prepare("INSERT IGNORE INTO `op_refund_logs`(`refund_id`, `invoice_no`, `umr_no`, `billno`, `mobileno`, `total`, `created_by`, `created_on`, `status`, `designation`, `remarks`) VALUES (:refund_id, :invoice_no, :umr_no, :billno, :mobileno, :total, :created_by, CURRENT_TIMESTAMP, 'Approved', :designation, :remarks)"); 
			
        $query_logs->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
	    $query_logs->bindParam(':invoice_no', $result1['invoice_no'], PDO::PARAM_STR);
        $query_logs->bindParam(':umr_no', $result1['umr_no'], PDO::PARAM_STR);
	    $query_logs->bindParam(':billno', $result1['billno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':mobileno', $result1['mobileno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':total', $result1['total'], PDO::PARAM_STR);
	    $query_logs->bindParam(':created_by', $result['userid'], PDO::PARAM_STR);
	    $query_logs->bindParam(':designation', $result['role'], PDO::PARAM_STR);
		$query_logs->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $query_logs->execute();
			
			}else{
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = 'Data Not Approved';
            }
	    }	
			   
    }elseif($result['role'] == 'CFO'){
		
		$update = $pdo4->prepare("UPDATE op_refund_generate SET `stage4_status`='Approved', `stage4_by`=:userid, `stage4_on`=CURRENT_TIMESTAMP,`stage4_branch`=:cost_center WHERE `refund_id` = :refund_id AND cost_center=:cost_center AND `stage2_status`='Approved' AND `stage1_status`='Approved' AND `stage3_status`='Approved' AND `stage4_status`='Pending'");
		
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
			if($update->rowCount() > 0){
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = 'Data Approved.';
			
		    
			$query_logs=$pdo4->prepare("INSERT IGNORE INTO `op_refund_logs`(`refund_id`, `invoice_no`, `umr_no`, `billno`, `mobileno`, `total`, `created_by`, `created_on`, `status`, `designation`, `remarks`) VALUES (:refund_id, :invoice_no, :umr_no, :billno, :mobileno, :total, :created_by, CURRENT_TIMESTAMP, 'Approved', :designation, :remarks)"); 
			
        $query_logs->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
	    $query_logs->bindParam(':invoice_no', $result1['invoice_no'], PDO::PARAM_STR);
        $query_logs->bindParam(':umr_no', $result1['umr_no'], PDO::PARAM_STR);
	    $query_logs->bindParam(':billno', $result1['billno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':mobileno', $result1['mobileno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':total', $result1['total'], PDO::PARAM_STR);
	    $query_logs->bindParam(':created_by', $result['userid'], PDO::PARAM_STR);
	    $query_logs->bindParam(':designation', $result['role'], PDO::PARAM_STR);
		$query_logs->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $query_logs->execute();
			}else{
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = 'Data Not Approved';
            }					
} else {
	http_response_code(400);
    $response['error'] = true;
    $response['message'] = 'Something Went Wrong!';
}						
} else {
	http_response_code(400);
    $response['error'] = true;
    $response['message'] = 'Access Denied!';
}
    } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = 'Sorry, some details are missing.';
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>