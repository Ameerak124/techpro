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
$empid = $data->empid;
$confirm_password = $data->confirmpassword;
$newpwd = $data->newpassword;
 
$response = array();
if(!empty($empid) && !empty($confirm_password) && !empty($newpwd)){
$rolecheck ="SELECT `userid` FROM `user_logins` WHERE `userid`=:empid AND `status`= 'Active'";
	 $stmt1 = $pdoread->prepare($rolecheck);
	 $stmt1->bindParam(":empid", $empid, PDO::PARAM_STR);
	 $stmt1->execute();
	 if($stmt1->rowCount()>0){
	 $results = $stmt1->fetch();
	 

 if ($confirm_password==$newpwd){

 http_response_code(200);
$resultt ="UPDATE `user_logins` SET `password`= To_Base64(:newpwd),`modifiedon`=CURRENT_TIMESTAMP,`modifiedby`=:userid WHERE `userid`= :userid";
$stmt3 = $pdo4->prepare($resultt);
	$stmt3->bindParam(":userid", $results['userid']);
	$stmt3->bindParam(":newpwd", $newpwd);

if($stmt3->execute()){


 http_response_code(200);
$response['error'] = false;
$response['message']='Your New Password Updated';
}else {
 http_response_code(503);
$response['error'] = true;
$response['message']='oops something went wrong.please Try Again';
            
}


}else {
 http_response_code(400);
$response['error'] = true;
$response['message']='New Password And confirm Password NotMatched';

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

   unset($pdoread);
   unset($pdo4);
	?>