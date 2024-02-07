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
$userid = $data->userid;
$newpwd = $data->newpassword;
$dob = date('Y-m-d', strtotime($data->dob));
$mobile = $data->mobile;
$response = array();


if(!empty($userid) && !empty($newpwd) && !empty($dob) && !empty($mobile)){
	

if(strlen($newpwd) <8) {
 http_response_code(400);
$response['error'] = true;
$response['message']='atleast 8 characters length';

}else{
try{
	
	$resultt="SELECT date(`date_of_birth`) as `date_of_birth`, `off_mobile`  FROM  `employee_details` WHERE `empid`=:userid AND `date_of_birth`=:dob AND `off_mobile`=:mobile";
     $stmt3 = $pdo_hrms->prepare($resultt);
	 $stmt3->bindParam(":userid",$userid, PDO::PARAM_STR);
	 $stmt3->bindParam(":dob",$dob, PDO::PARAM_STR);
	 $stmt3->bindParam(":mobile",$mobile, PDO::PARAM_STR);
     $stmt3->execute();
if($stmt3->rowCount() == 1){
	
	
$result = "UPDATE `super_logins` SET `password`=To_Base64(:newpassword) WHERE `userid`= :userid";
	$sql = $pdo4->prepare($result);
 $sql->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql->execute();

//medicover
$result1 = "UPDATE `user_logins` SET `password`=To_Base64(:newpassword),hash_password2=:newpassword1 WHERE `userid`= :userid";
$sql1 = $pdo4->prepare($result1);
 $sql1->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql1->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql1->bindParam(":newpassword1", $newpwd1, PDO::PARAM_STR);
$newpwd1=password_hash($newpwd, PASSWORD_BCRYPT);
$sql1->execute();	

//i-assist	
$result2 = "UPDATE `emp_logins` SET `mpassword`=:newpassword WHERE `emp_id`=:userid";
$sql2 = $pdo1->prepare($result2);
 $sql2->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql2->bindParam(":newpassword", $newpwd1, PDO::PARAM_STR);
$newpwd1=password_hash($newpwd, PASSWORD_BCRYPT);
$sql2->execute();

//po
	$result3 = "UPDATE `pologins` SET `password`=To_Base64(:newpassword) WHERE `empid`=:userid";
$sql3 = $pdo2->prepare($result3);
 $sql3->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql3->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql3->execute();

//refferal
$result4 = "UPDATE `referral_logins` SET `mpassword`=:newpassword WHERE `Emp_ID`=:userid";
$sql4 = $con->prepare($result4);
 $sql4->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql4->bindParam(":newpassword", $newpwd1, PDO::PARAM_STR);
$newpwd1=password_hash($newpwd, PASSWORD_BCRYPT);
$sql4->execute();

//mis
$result5 = "UPDATE `logins` SET `mpassword`=:newpassword WHERE `Emp_ID`=:userid";
$sql5 = $con->prepare($result5);
 $sql5->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql5->bindParam(":newpassword", $newpwd1, PDO::PARAM_STR);
$newpwd1=password_hash($newpwd, PASSWORD_BCRYPT);
$sql5->execute();


//hrms
$result6 = "UPDATE `employee_details` SET `mobile_password`=:newpassword WHERE `empid`=:userid";
$sql6 = $pdo_hrms->prepare($result6);
 $sql6->bindParam(":userid", $userid, PDO::PARAM_STR);
$sql6->bindParam(":newpassword", $newpwd1, PDO::PARAM_STR);
$newpwd1=password_hash($newpwd, PASSWORD_BCRYPT);
$sql6->execute();

if($sql -> rowCount() > 0){


 http_response_code(200);
$response['error'] = false;
$response['message']='Your New Password Updated';
				
    
    
            	
}else {
 http_response_code(503);
$response['error'] = true;
$response['message']='oops something went wrong.please Try Again';
            
}
}else{
	  http_response_code(503);
	  $response['error'] = true;
      $response['message']="Your details not matched";
         
    
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed" .$e->getmessage();
}
}
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] = "Sorry! Some details are missing";
}
echo json_encode($response);

unset($pdo_hrms);
unset($con);
unset($pdo2);
unset($pdo1);
unset($pdoread);
unset($pdo4);
?>