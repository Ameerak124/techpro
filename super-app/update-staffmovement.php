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
$sno = $data->sno;
$response = array();
try{

 if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$result2 = $pdo1 -> prepare("SELECT `sno`, `id`, `emp_id`, `emp_name`, `created_on`, `out_time`,in_time, `status` FROM `staff_movement` WHERE `sno`=:sno");
	$result2->bindParam(':sno', $sno, PDO::PARAM_STR);
	$result2-> execute();
	 $result2->execute();
    $data = $result2->fetch(PDO::FETCH_ASSOC);
	
    $result = $pdo1 -> prepare("UPDATE `staff_movement` SET `modified_by`=:userid,`modified_on`=CURRENT_TIMESTAMP,`in_time`=CURRENT_TIME,`status`='Entered' WHERE sno=:sno and status='Exit'");
	$result->bindParam(':sno', $sno, PDO::PARAM_STR);
	$result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();
   if($result->rowCount()>0){
	   
	   
	 $result1 = $pdo1 -> prepare("INSERT INTO `staff_movement_logs`(`id`, `emp_id`, `name`, `created_by`,`created_on`,`status`) VALUES (:id,:emp_id,:name,:userid,CURRENT_TIMESTAMP,'Entered')");
	$result1->bindParam(':id', $data['id'], PDO::PARAM_STR);
	$result1->bindParam(':emp_id', $data['emp_id'], PDO::PARAM_STR);
	$result1->bindParam(':name', $data['emp_name'], PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1-> execute();
	   
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data updated";
    }else{
       http_response_code(503);
       $response['error'] = true;
       $response['message']="Data not updated";
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
unset($pdoread);
unset($pdo1);
?>