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
$receiptno = trim($data->receiptno);
$remarks = trim($data->remarks);

try {
    if (!empty($accesskey) && !empty($receiptno) && !empty($remarks)) {
        $sql = $pdoread->prepare("SELECT `userid`,`cost_center`,`role` from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
        $sql->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $sql->execute();
        if ($sql->rowCount() > 0) {
        $result = $sql->fetch(PDO::FETCH_ASSOC);
			
	    $query=$pdoread->prepare("select `sno`,`bill_type`, `receiptno`, `admissionon`, `billno`, `total`,amount FRO    `payment_history` WHERE receiptno=:receiptno");
        $query->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
	    $query->execute();
        $result1=$query->fetch(PDO::FETCH_ASSOC);	
	if ($result['role'] == 'AUDIT') {

        $update = $pdo4->prepare("UPDATE `payment_history` SET `stage1_status`='Rejected', `stage1_by`=:userid`stage1_on`=CURRENT_TIMESTAMP, `stage1_branch`=:cost_center,remarks=:remarks WHERE `receiptno` = :receiptnAND `cost_center`=:cost_center AND `stage1_status`=''");
        $update->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $update->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
        $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
        $update->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
        $update->execute();
        if ($update->rowCount() > 0) {
                  
		$query_logs=$pdo4->prepare("INSERT INTO `payment_history_logs`(`bill_type`, `receiptno`, `admissionon`, `billno`, `total`, `createdby`, `createdon`,`remarks`, `status`, `designation`) VALUES (:bill_type,:receiptno,:admissionon,:billno,:total,:createdby,CURRENT_TIMESTAMP,:remarks,'Rejected',:designation)");
	    $query_logs->bindParam(':bill_type', $result1['bill_type'], PDO::PARAM_STR);
	    $query_logs->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
        $query_logs->bindParam(':admissionon', $result1['admissionon'], PDO::PARAM_STR);
	    $query_logs->bindParam(':billno', $result1['billno'], PDO::PARAM_STR);
	    $query_logs->bindParam(':total', $result1['total'], PDO::PARAM_STR);
	    $query_logs->bindParam(':remarks', $remarks, PDO::PARAM_STR);
	    $query_logs->bindParam(':createdby', $result['userid'], PDO::PARAM_STR);
	    $query_logs->bindParam(':designation', $result['role'], PDO::PARAM_STR);
        $query_logs->execute();		
					
		http_response_code(200);
        $response['error'] = false;
        $response['message'] = 'Data Rejected successfully';
        } else {
		http_response_code(503);
        $response['error'] = true;
        $response['message'] = 'Data Not Rejected';
        }
        } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = 'Role Must Be Auditor';
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
        $response['message'] = "Connection failed: " . $e->getMessage();
        }
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
