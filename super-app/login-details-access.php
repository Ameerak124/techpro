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
$response = array();
$accesskey = trim($data->accesskey);
$type = trim($data->type);
try {
if(!empty($accesskey)){

$rolecheck ="SELECT From_Base64(password) AS password,userid FROM `super_logins` WHERE `mobile_accesskey`=:accesskey";
	 $stmt1 = $pdoread->prepare($rolecheck);
	 $stmt1->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	 $stmt1->execute();
	 if($stmt1->rowCount() == 1){
	 $results = $stmt1->fetch();


if(($type=='Employee Assist') || ($type=='Security Dashboard') || ($type=='Dynamic Form') || ($type=='Permissions') || ($type=='Get Data') || ($type=='Journal') || ($type=='PACS') || ($type=='Dashboard') || ($type=='International Referral')){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	
		
	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	if(($type=='International
	Referral')){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	
		
	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
}else if(($type=='Doc Assist')){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active' and role in ('Doctor','DOCTOR','Center Head','CENTER HEAD','Medical Head','MEDICAL HEAD','Director Operations','ED')");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
}else if($type=='I Assist'){
	$result2 = $pdo1 -> prepare("SELECT  `emp_id` as userid, `emp_name` as username, `designation` as designation, `department` as department, `branch` AS storeaccess ,`clinicaltype`,role,mpassword,password,accesstoken FROM `emp_logins` where accesstoken=:accesskey");
	$result2->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result2-> execute();
	if($result2->rowCount()>0){
	$fetchres1 = $result2->fetch(PDO::FETCH_ASSOC);
	
	
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}

}else if($type=='Procurement'){
	$result3 = $pdo2 -> prepare("SELECT `empid` as userid, `designation`, `name` as username, `branch` AS storeaccess, `emailid`, `department`,password,role,accesskey FROM `pologins` where accesskey=:accesskey ");
	$result3->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result3 -> execute();
	
	if($result3->rowCount()>0){
	$fetchres2 = $result3->fetch(PDO::FETCH_ASSOC);
	
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	
}else if($type=='Patient Referral'){
	
	$insert ="SELECT  `Emp_name`, `Designation`, `Emp_ID`, `Password`, `mpassword`, `Branch` AS storeaccess, `Job_Status`, `branch_access`,type,`branch_access`,accesskey  FROM `referral_logins` WHERE `Job_Status`='Active' and `accesskey`=:accesskey";
			$result4 = $con->prepare($insert);
            $result4->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
			$result4->execute();
			
			if($result4->rowCount()>0){
				
				
				$fetchres3 = $result4->fetch(PDO::FETCH_ASSOC);
				
				
				
		http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	
}else if($type=='MIS'){			
					
$insert1 ="SELECT  `Emp_name`, `Designation`, `Emp_ID`, `Password`, `mpassword`, `Branch` AS storeaccess, `Job_Status`, `branch_access`,type,`branch_access`,accesskey,Office_number FROM `logins` WHERE `Job_Status`='Active' and `accesskey`=:accesskey";
			$results = $con->prepare($insert1);
            $results->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
			$results->execute();
			
		if($results->rowCount()>0){
			$fetchress = $results->fetch(PDO::FETCH_ASSOC);					
					
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}

}else if( $type=='E-MIS' || $type=='Operations' ||$type=='My Article'
 ||$type=='Clinical News'){
		
					
	$result5 = $pdoread -> prepare("SELECT `userid`, `password`, `username`, `emailid`, `role`, `desgination`, `department`, `status`, `cost_center`,`branch`, `accesskey`, `mobile_accesskey` FROM `user_logins` WHERE `status`='Active' and `mobile_accesskey`=:accesskey");
	$result5 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result5  -> execute();
	if($result5 -> rowCount() > 0){
	 $fetchres4 = $result5->fetch(PDO::FETCH_ASSOC);
   /*  if($updateid -> rowCount() > 0){ */
   
  http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	}else if($type=='Doctor'){
		
					
	$result5 = $pdoread -> prepare("SELECT `userid`, `password`, `username`, `emailid`, `role`, `desgination`, `department`, `status`, `cost_center`,`branch`, `accesskey`, `mobile_accesskey` FROM `user_logins` WHERE `status`='Active' and `mobile_accesskey`=:accesskey AND role='Doctor'");
	$result5 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result5  -> execute();
	if($result5 -> rowCount() > 0){
	 $fetchres4 = $result5->fetch(PDO::FETCH_ASSOC);
   /*  if($updateid -> rowCount() > 0){ */
   
  http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	}else if($type=='HRMS'){
	
	$result6 = $pdo_hrms -> prepare("SELECT `empid` as userid, `employee_name` as username, concat( `first_name`, `middle_name`, `last_name`) as name, `branch`, `designation`,accesskey,roles as role FROM `employee_details` WHERE `status`='Active' and `accesskey`=:accesskey");
	$result6 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result6  -> execute();
	if($result6 -> rowCount() > 0){
				
				
				$fetchres5 = $result6->fetch(PDO::FETCH_ASSOC);
				
				
				
		http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	

	}else{
		
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	}else if($type=='Dashboard'){
		http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
		
		
	}else{
		http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
		
	}
	
	
}else{
     http_response_code(400);
    $response['error']= true;
    $response['message']= "Access denied!";	
	
}
}else{
	http_response_code(400);
    $response['error']= true;
    $response['message']= "Sorry, some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e;
}
echo json_encode($response);
$pdoread= null;
$pdo1 = null;
$pdo2 = null;
$pdo4 = null;
$con = null;
$pdo_hrms = null;
$himsdemo = null;
?>