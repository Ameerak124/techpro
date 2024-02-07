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
$assigning_person = $data->assigning_person;
$remarks = $data->remarks;
$status=$data->status;
$response = array();
try{

if(!empty($accesskey) && !empty($sno)  && !empty($assigning_person)){
	
	$accesscheck ="SELECT `userid`,`branch`,`cost_center`,role,username,concat(TRIM(username),' - ','(',userid,')') as assigningperson FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$empidd= $row['userid'];
		$designation= $row['role'];



$person= explode(')-',$assigning_person);

$persondata=$person[0].')';
$design= $person[1];
  $stmt11=$pdoread->prepare("SELECT `ticket_id` FROM `doctor_raise_ticket` WHERE `sno`=:sno");
	$stmt11->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt11->execute();
   $rows = $stmt11->fetch(PDO::FETCH_ASSOC);
   
   	$statusact='Pending by '.$design;
	$stmt1=$pdo4->prepare("UPDATE `doctor_raise_ticket` SET assigned_person=:assigning_person,`remarks`=:remarks,realstatus=:statusact WHERE `sno`=:sno");
	$stmt1->bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt1 -> bindParam(":assigning_person", $persondata, PDO::PARAM_STR);
	$stmt1->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt1->bindParam(":statusact", $statusact, PDO::PARAM_STR);
	$stmt1->execute();
	$type = $status . ' to ' .  $design .  ' by ' .$designation;
	
	
	$stmt2=$pdo4->prepare("INSERT INTO `issue_logs`( `issue_sno`, `assigned_person`,role, `created_by`, `created_on`, `reason`, `status`,created_by_name,ticket_id) VALUES(:sno,:assigning_person,:designation,:empidd,CURRENT_TIMESTAMP,:remarks,:status,:name,:ticket_id)");
	$stmt2->bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt2 -> bindParam(":assigning_person", $persondata, PDO::PARAM_STR);
	$stmt2 -> bindParam(":designation", $designation, PDO::PARAM_STR);
	$stmt2 -> bindParam(":ticket_id", $rows['ticket_id'], PDO::PARAM_STR);
	$stmt2 -> bindParam(":name", $row['username'], PDO::PARAM_STR);
	$stmt2->bindParam(":empidd", $empidd, PDO::PARAM_STR);
	$stmt2->bindParam(":sno", $sno, PDO::PARAM_STR);
	$stmt2->bindParam(":status", $type, PDO::PARAM_STR);
	$stmt2->execute();
	
	
	     if($stmt1 -> rowCount() > 0){
			 $body="Your issue will be ".$status;
			pushnotification($tokenid,$empname,$body,$icon);
		 http_response_code(200);
          $response['error']= false;
	      $response['message']="Doctor " . $status;
          
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
$pdo4 = null;
$pdoread = null;
?>