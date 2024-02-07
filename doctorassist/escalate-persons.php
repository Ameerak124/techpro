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
$accesskey = $data->accesskey;
$response = array();
$response1 = array();
//$responselg = array();
$type= $data->type;


try{
if(!empty($accesskey)){
	
	$accesscheck ="SELECT  `userid`, `role`, `desgination`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
		$row = $stmt->fetch();

	   
	if($type=='Escalate'){
		
		$stmt2 = $pdoread->prepare("SELECT `sno`, `PL`, `CL`, `NL`, `assign`, `escalate`, `resolve`, `reject`, `status`, `createdon`, `createdby` FROM `issue_level_designations` WHERE CL=:role and `status`='1'");
		$stmt2->bindParam(":role", $row['role'], PDO::PARAM_STR);
		 $stmt2->execute();
		$result = $stmt2->fetch(PDO::FETCH_ASSOC);
		
   
   if($stmt2->rowCount()>0){
if($row['role']=='Doctor'){
	  
	  $stmt1 = $pdoread->prepare("SELECT concat(TRIM(user_logins.username),' - ','(',user_logins.userid,')','-',trim(user_logins.role)) as assigningperson ,user_logins.role FROM user_logins   where  FIND_IN_SET(user_logins.role,:role)<>0  and cost_center=:branch and  user_logins.status='Active'");
       $stmt1->bindParam(":role", $result['NL'], PDO::PARAM_STR);
		$stmt1->bindParam(":branch", $row['cost_center'], PDO::PARAM_STR);
		$stmt1->execute();
	 
}else if($row['role']!='Doctor'){
	 $stmt1 = $pdoread->prepare("SELECT concat(TRIM(user_logins.username),' - ','(',user_logins.userid,')','-',trim(user_logins.role)) as assigningperson ,user_logins.role FROM user_logins   where  FIND_IN_SET(user_logins.role,:role)<>0  and  user_logins.status='Active'");
       $stmt1->bindParam(":role", $result['NL'], PDO::PARAM_STR);
		//$stmt1->bindParam(":branch", $row['cost_center'], PDO::PARAM_STR);
		$stmt1->execute();
	
}
	     if($stmt1->rowCount() > 0){
		 while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
			
		
  
		  

$temp=[
"assigningperson"=>$row1['assigningperson'],

];
array_push($response1,$temp);


				/* $response['assigningpersonslist'][] = [
					
					'assigningperson'=>$row1['assigningperson'],
					'assigningdesccode'=>$row1['role'],
				]; */
				
		 }  
     
		 }if(empty($response1)){
		 http_response_code(503);
	 $response['error'] = true;
	$response['message']="No data found!";
	
	 }else{
		 http_response_code(200);
	$response['error'] = false;
	$response['message']="Data found";
	
		$response['assigningpersonslist']= $response1;
	 }
	 }else{
		http_response_code(503);
	$response['error'] = true;
	$response['message']="This type of department is present";
		
	}
	}else if($type=='Assign'){
		$stmt2 = $pdoread->prepare("SELECT `sno`, `PL`, `CL`, `NL`, `assign`, `escalate`, `resolve`, `reject`, `status`, `createdon`, `createdby` FROM `issue_level_designations` WHERE `CL`=:role and `status`='1'");
		$stmt2->bindParam(":role",$row['role'], PDO::PARAM_STR);
		//$stmt2->bindParam(":department", $department, PDO::PARAM_STR);
		 $stmt2->execute();
		$result = $stmt2->fetch(PDO::FETCH_ASSOC);
		
		
		
		
	
	
	if($stmt2->rowCount()>0){
	
	
	
	
	  /* $stmt1 = $pdoread->prepare("SELECT concat(TRIM(user_logins.`username`),' - ','(',user_logins.`userid`,')','-',trim(user_logins.role)) as assigningperson ,user_logins.role FROM user_logins   where  FIND_IN_SET(user_logins.`role`,:role)<>0 and `branch`=:branch and  user_logins.status='Active';"); */
	  
	  
	 
	  $stmt1 = $pdoread->prepare("SELECT concat(TRIM(user_logins.`username`),' - ','(',user_logins.`userid`,')','-',trim(user_logins.role)) as assigningperson ,user_logins.role FROM user_logins   where  FIND_IN_SET(user_logins.`role`,:role)<>0  and  user_logins.status='Active';");
       $stmt1->bindParam(":role", $result['PL'], PDO::PARAM_STR);
		//$stmt1->bindParam(":branch", $row['branch'], PDO::PARAM_STR);
		$stmt1->execute();
	    
	   
	     if($stmt1->rowCount() > 0){
		 while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
			
		
  
	

$temp=[
"assigningperson"=>$row1['assigningperson'],

];
array_push($response1,$temp);

				
		 }  
     
		 }if(empty($response1)){
		 http_response_code(503);
	 $response['error'] = true;
	$response['message']="No data found!";
	
	 }else{
		 http_response_code(200);
	$response['error'] = false;
	$response['message']="Data found";
	
		$response['assigningpersonslist']= $response1;
	 }
	}else{
		http_response_code(503);
	$response['error'] = true;
	$response['message']="This type of department is present";
		
	}
	}
	}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
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
echo json_encode($response);
//$pdo4 = null;
$pdoread = null;
?>