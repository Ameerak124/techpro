<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db-new.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
/* $id = $data->id; */
$emp_id = $data->emp_id;
$emp_name = $data->emp_name;
$desgination = $data->desgination;
$branch = $data->branch;
$reason = $data->reason;
$reason_remarks = $data->reason_remarks;
$response = array();
try{

 if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	

	$fetch = $pdo1 -> prepare("SELECT  `emp_id`, `emp_name`, `created_on`, `out_time`,in_time, `status` FROM `staff_movement` WHERE emp_id=:emp_id and status='Exit' and date(created_on)=CURRENT_DATE");
	$fetch->bindParam(':emp_id', $emp_id, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount() == 0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);	
	
 $result2 = $pdo1 -> prepare("SELECT Concat('STM',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`id`),'STM23090000'),Concat('STM',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS id FROM `staff_movement` where id like concat('%STM',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
					$result2->execute();
                 $data=$result2->fetch(PDO::FETCH_ASSOC);
					$idd=$data['id'];
 
    $result = $pdo1 -> prepare("INSERT INTO `staff_movement`(`id`,`emp_id`, `emp_name`, `desgination`, `branch`, `reason`, `reason_remarks`, `created_by`, `created_on`,`out_time`,`status`) VALUES
	(:idd,:emp_id,:emp_name,:desgination,:branch,:reason,:reason_remarks,:userid,CURRENT_TIMESTAMP,CURRENT_TIME,'Exit')");
	$result->bindParam(':idd', $idd, PDO::PARAM_STR);
	$result->bindParam(':emp_id', $emp_id, PDO::PARAM_STR);
	$result->bindParam(':emp_name', $emp_name, PDO::PARAM_STR);
	$result->bindParam(':desgination', $desgination, PDO::PARAM_STR);
	$result->bindParam(':branch', $branch, PDO::PARAM_STR);
	$result->bindParam(':reason', $reason, PDO::PARAM_STR);
	$result->bindParam(':reason_remarks', $reason_remarks, PDO::PARAM_STR);
	$result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();
	
 $result1 = $pdo1 -> prepare("INSERT INTO `staff_movement_logs`(`id`, `emp_id`, `name`, `created_by`,`created_on`,`status`) VALUES (:idd,:emp_id,:name,:userid,CURRENT_TIMESTAMP,'Exit')");
	$result1->bindParam(':idd', $idd, PDO::PARAM_STR);
	$result1->bindParam(':emp_id', $emp_id, PDO::PARAM_STR);
	$result1->bindParam(':name', $emp_name, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1-> execute();
	
	
   if($result->rowCount()>0){
	   
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data inserted";

    }else{
       http_response_code(503);
       $response['error'] = true;
       $response['message']="Data not inserted";
     }
	 	   }else{
	 http_response_code(503);
    $response['error'] = true; 
    $response['message']= "Data already inserted";
	   }
    	}else{
			http_response_code(400);
		    $response['error'] = true;
			$response['message']="Access denied!";
		}  
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
unset($pdo);
unset($pdo1);
?>