<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$sno = $data->sno;
$remarks = $data->remarks;
$status = $data->status;
$ImageData = $data->image;
$response = array();
try{
	/* define('SERVER_API_KEY', 'AAAAf8YRe9U:APA91bE33bcZ5cURIonZvuxeKMX3UezEB2FCTKKXA3gZ518gql8ZJHRtu559Fp3lCtnD9kqhZhRnbV4FDUrrWdlKmUV745p8xzeEoI3J37o4mymc10oL7FS_LjlUfJvfYnksLMu7RuCZ');
$icon = 'https://65.1.244.68/rtc-hrms/appicons/tsrtclogo.jpg'; */
if(!empty($accesskey) && !empty($sno) && !empty($remarks) && !empty($status)){
	
	$accesscheck ="SELECT `userid`,`branch`,`cost_center`,role,username,concat(TRIM(username),' - ','(',userid,')') as assigningperson FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdo->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$empidd= $row['userid'];
			$empids=$empidd.'_'.$row['time'];
		$role= $row['role'];


/* if($ImageData==''){
	
	$ImagePath = '';
}else{
	
	$ImagePath = "grievance/$empids.png";	
}

 */


    $stmt11=$pdo->prepare("SELECT `ticket_id` FROM `employee_raise_ticket` WHERE `sno`=:sno");
	$stmt11->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt11->execute();
   $row22 = $stmt11->fetch(PDO::FETCH_ASSOC);
$stmt22= $pdo->prepare("SELECT `assigned_person`,created_by  FROM `employee_issue_logs` WHERE `issue_sno`= :sno and `status`like '%Pending%'");
       $stmt22->bindParam(":sno", $sno, PDO::PARAM_STR);
	   $stmt22->execute();
	   $rows = $stmt22->fetch(PDO::FETCH_ASSOC);
	   
	  
	   $type = $status .  ' by ' .$role;
	  
	   
		if($status=="Escalated"){
	$stmt1=$pdo->prepare("UPDATE `employee_raise_ticket` SET assigned_person=:assignperson WHERE `sno`=:sno");

	$stmt1->bindParam(":assignperson", $rows['assigned_person'], PDO::PARAM_STR);

	$stmt1->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt1->execute();
	
	
	
	$stmt21=$pdo->prepare("INSERT INTO `employee_issue_logs`(  `issue_sno`, `ticket_id`, `assigned_person`, `role`, `created_by`, `created_by_name`, `created_on`, `reason`, `status`)VALUES (:sno,:ticket_id,:assignperson,:role,:empidd,:name,CURRENT_TIMESTAMP,:remarks,:status)");
	$stmt21->bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt21->bindParam(":assignperson", $rows['assigned_person'], PDO::PARAM_STR);
	$stmt21->bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt21->bindParam(":ticket_id", $row22['ticket_id'], PDO::PARAM_STR);
	$stmt21 -> bindParam(":name", $row['username'], PDO::PARAM_STR);
	//$stmt21 -> bindParam(":ImagePath", $ImagePath, PDO::PARAM_STR);
	$stmt21->bindParam(":status", $type, PDO::PARAM_STR);
	$stmt21->bindParam(":empidd", $empidd, PDO::PARAM_STR);
	$stmt21->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt21->execute();
		}else{
			$stmt1=$pdo->prepare("UPDATE `employee_raise_ticket` SET assigned_person=:assignperson,status=:status,realstatus=:type WHERE `sno`=:sno");
	//$stmt1->bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt1->bindParam(":assignperson", $row['assigningperson'], PDO::PARAM_STR);
	//$stmt1 -> bindParam(":ImagePath", $ImagePath, PDO::PARAM_STR);
	$stmt1 -> bindParam(":status", $type, PDO::PARAM_STR);
	$stmt1->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt1->bindParam(":type", $type, PDO::PARAM_STR);
	$stmt1->execute();

	
		$stmt21=$pdo->prepare("INSERT INTO `employee_issue_logs`(  `issue_sno`, `ticket_id`, `assigned_person`, `role`, `created_by`, `created_by_name`, `created_on`, `reason`, `status`)VALUES (:sno,:ticket_id,:assignperson,:role,:empidd,:name,CURRENT_TIMESTAMP,:remarks,:status)");
	$stmt21->bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt21->bindParam(":assignperson", $row['assigningperson'], PDO::PARAM_STR);
	$stmt21->bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt21->bindParam(":ticket_id", $row22['ticket_id'], PDO::PARAM_STR);
	$stmt21 -> bindParam(":name", $row['username'], PDO::PARAM_STR);
	//$stmt21 -> bindParam(":ImagePath", $ImagePath, PDO::PARAM_STR);
	$stmt21->bindParam(":status", $type, PDO::PARAM_STR);
	$stmt21->bindParam(":empidd", $empidd, PDO::PARAM_STR);
	$stmt21->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt21->execute();
	
	
	
		}
	
	
	
	     if($stmt1 -> rowCount() > 0){
			 $body="Your issue will be ".$status;
		//	pushnotification($tokenid,$empname,$body,$icon);
		 http_response_code(200);
          $response['error']= false;
	      $response['message']="Employee  " . $status;
         /*  $decodedImage = base64_decode($ImageData);
          file_put_contents($ImagePath,$decodedImage); */
		 }
     else
     {
		 http_response_code(503);
        $response['error']= true;
	     $response['message']="Not approved";
     }
       
	}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied!";
}
	}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
     
     //"Connection failed: " . $e->getMessage();
}

function pushnotification($tokens,$title,$body,$icon){
    
    	$header = [
		'Authorization: Key=' . SERVER_API_KEY,
		'Content-Type: Application/json'
	];
	
$msg = [
		'title' => $title,
		'body' => $body,
		'image' => $icon,
	];
		$payload = [
		'to' 	=> $tokens,
		'notification' => $msg
	];
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => json_encode( $payload ),
	  CURLOPT_HTTPHEADER => $header
	));

	$responsess = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	//  echo "cURL Error #:" . $err;
	} else {
	 // echo $responsess;
	}
}


echo json_encode($response);
$pdo = null;
?>