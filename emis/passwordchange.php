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
$Oldpwd = $data->oldpassword;
$newpwd = $data->newpassword;
 
$response = array();
if(!empty($accesskey) && !empty($Oldpwd) && !empty($newpwd)){
 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`cost_center` AS storeaccess,FROM_base64(`password`) AS checkpassword FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$results = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	// $results = $check->fetch();
 if ($Oldpwd==$results['checkpassword']) {


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
$response['message']='Old Password does not matched';

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