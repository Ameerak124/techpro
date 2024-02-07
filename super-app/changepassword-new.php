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
$Oldpwd = $data->oldpassword;
$newpwd = $data->newpassword;
$response = array();
if(!empty($accesskey) && !empty($Oldpwd) && !empty($newpwd)){
$rolecheck ="SELECT From_Base64(password) AS password,userid FROM `super_logins` WHERE `mobile_accesskey`=:accesskey";
	 $stmt1 = $pdoread->prepare($rolecheck);
	 $stmt1->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	 $stmt1->execute();
	 if($stmt1->rowCount() == 1){
	 $results = $stmt1->fetch();
try{


 if ($Oldpwd==$results['password']) {

if(strlen($newpwd) <8) {
 http_response_code(400);
$response['error'] = true;
$response['message']='atleast 8 characters length';

}else{
	try{
	$result = "UPDATE `super_logins` SET `password`=To_Base64(:newpassword) WHERE `userid`= :empid";
	$sql = $pdo4->prepare($result);
 $sql->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql->execute();
	
//medicover
$sql1 = $pdo4 -> prepare("UPDATE `user_logins` SET `password`=To_Base64(:newpassword) WHERE `userid`=:empid");
 $sql1->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql1->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql1->execute();

/* 
//i-assist
$sql2 = $pdo1 -> prepare("UPDATE `emp_logins` SET `mpassword`=:newpassword WHERE `emp_id`=:empid");
 $sql2->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql2->bindParam(":newpassword", $newpwd);
$newpwd=password_hash($newpassword, PASSWORD_BCRYPT);
$sql2->execute();

//po
$sql3 = $pdo2 -> prepare("UPDATE `pologins` SET `password`=To_Base64(:newpassword) WHERE `empid`=:empid");
 $sql3->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql3->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql3->execute();


//refferal
$sql4 = $con -> prepare("UPDATE `referral_logins` SET `mpassword`=:newpassword WHERE `Emp_ID`=:empid");
 $sql4->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql4->bindParam(":newpassword", $newpwd);
$newpwd=password_hash($newpassword, PASSWORD_BCRYPT);
$sql4->execute();

//mis
$sql5 = $con -> prepare("UPDATE `logins` SET `mpassword`=:newpassword WHERE `Emp_ID`=:empid");
 $sql5->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql5->bindParam(":newpassword", $newpwd);
$newpwd=password_hash($newpassword, PASSWORD_BCRYPT);
$sql5->execute();

//emis
$sql6 = $pdo4 -> prepare("UPDATE `user_logins` SET `password`=To_Base64(:newpassword) WHERE `userid`=:empid");
 $sql6->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
$sql6->bindParam(":newpassword", $newpwd, PDO::PARAM_STR);
$sql6->execute();

//hrms
$sql7 = $pdo_hrms -> prepare("UPDATE `employee_details` SET `mobile_password`=:newpassword WHERE `empid`=:empid");
 $sql7->bindParam(":empid", $results['userid'], PDO::PARAM_STR);
	$sql7->bindParam(":newpassword", $newpwd);
   $newpwd=password_hash($newpassword, PASSWORD_BCRYPT);
$sql7->execute(); */

if($sql -> rowCount() > 0){
/* 	$fetch = $pdo -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `super_logins` WHERE `userid` = :userid AND `status`= 'Active' LIMIT 1");
	$fetch->bindParam(':userid',$results['userid'], PDO::PARAM_STR);
	$fetch -> execute();
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	$stmt=$pdo->prepare("SELECT `display_name` FROM `branch_master` where  `cost_center`=:costcenter");
	$stmt->bindParam(':costcenter', $fetchres['cost_center'], PDO::PARAM_STR);
	$stmt -> execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC); */
	 http_response_code(200);
$response['error'] = false;
$response['message']='Your New Password Updated';
/*     $response['name']= $fetchres['username'];
    $response['userid']= $fetchres['userid'];
    $response['branch']= $fetchres['storeaccess'];
    $response['costcentercode']= $fetchres['cost_center'];
    $response['costcenter']= $row['display_name'];
    $response['accesskey']= $fetchres['mobile_accesskey'];
    $response['role']= $fetchres['role']; */
		

				           	
}else {
 http_response_code(503);
$response['error'] = true;
$response['message']='oops something went wrong.please Try Again';
            
}


} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed" .$e->getmessage();
}
}
}else {
 http_response_code(400);
$response['error'] = true;
$response['message']='Old Password does not matched';

}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed" .$e->getmessage();
}

}else{
	  http_response_code(400);
	  $response['error'] = true;
      $response['message']="Access denied!";
}
}else{
	
	  http_response_code(400);
	  $response['error'] = true;
      $response['message'] = "Sorry! Some details are missing";
	
}


 
echo json_encode($response);
	
/* unset($pdo); */
$pdoread = null;
$pdo4 = null;


	?>