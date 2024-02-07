<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$refund_id = trim($data->refund_id);
try {
 if(!empty($accesskey) && !empty($refund_id) ){
  $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role` from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
  $sql->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
  $sql->execute();
  if($sql->rowCount() > 0){
  $result=$sql->fetch(PDO::FETCH_ASSOC);  
  if($result['role'] == 'AUDIT'){      
            $update = $pdo4->prepare("UPDATE op_refund_generate SET `aadhar_file` = '#', `bank_file` = '#', `stage1_status`='Rejected', `stage1_by`=:userid, `stage1_on`=CURRENT_TIMESTAMP, `stage1_branch`=:cost_center WHERE `refund_id` = :refund_id AND `cost_center`=:cost_center AND `stage1_status`='Pending'");
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
  }else if($result['role'] == 'Center Head'){
	    $update = $pdo4->prepare("UPDATE op_refund_generate SET `aadhar_file` = '#', `bank_file` = '#', `stage2_status`='Rejected', `stage2_by`=:userid, `stage2_on`=CURRENT_TIMESTAMP, `stage2_branch`=:cost_center WHERE `refund_id` = :refund_id AND `cost_center`=:cost_center AND `stage2_status`='Rejected'");
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
  }else if($result['role'] == 'Unit Head'){
	    $update = $pdo4->prepare("UPDATE op_refund_generate SET `aadhar_file` = '#', `bank_file` = '#', `stage3_status`='Rejected', `stage3_by`=:userid, `stage3_on`=CURRENT_TIMESTAMP, `stage3_branch`=:cost_center WHERE `refund_id` = :refund_id AND `cost_center`=:cost_center AND `stage3_status`='Rejected'");
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();
  }else if($result['role'] == 'ED'){
  $update = $pdo4->prepare("UPDATE op_refund_generate SET `aadhar_file` = '#', `bank_file` = '#', `stage4_status`='Rejected', `stage4_by`=:userid, `stage4_on`=CURRENT_TIMESTAMP, `stage4_branch`=:cost_center WHERE `refund_id` = :refund_id AND `cost_center`=:cost_center AND `stage3_status`='Rejected'");
            $update->bindParam(':refund_id', $refund_id, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $update->execute();	  
            if ($update->rowCount() > 0) {
                http_response_code(200);
                $response['error']=false;
                $response['message']='Data Rejected successfully.';
            } else {
                http_response_code(503);
                $response['error']=true;
                $response['message']='Data Not Rejected';
            }
       
    } else {
        http_response_code(400);
        $response['error']=true;
        $response['role']='Something Went Wrong!';
    }
} else {
    http_response_code(400);
    $response['error']=true;
    $response['message']='Access Denied!';
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