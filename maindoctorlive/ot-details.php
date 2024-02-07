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
$accesskey=$data->accesskey;
$response=array();
try {
if( !empty($accesskey)) {

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` AS branch FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$output = $check->fetch(PDO::FETCH_ASSOC);
//accesskey verified//
if($check->rowcount()>0)
	{
	$list=$pdoread->prepare("SELECT `sno`,`otcode`,`otname`,`otnumber` FROM `ot_master` WHERE `branch`=:location AND `otstatus`='Active'");
    $list->bindParam(':location', $output['branch'], PDO::PARAM_STR);
   $list->execute();
      if($list->rowcount()>0) {
		  http_response_code(200);
	             $response['error']=false;
				 $response['message']='data found';
   $result=$list->fetchAll(PDO::FETCH_ASSOC);
	   
           $response['otdetailslist']=$result;
		        
   }else{
	       http_response_code(503);
	         $response['error']=true;
			 $response['message']='  No data found';
		}
}else {         
                  http_response_code(400);
                  $response['error']=true;
				 $response['message']='Access denied!';
		}
}else{
	        http_response_code(400);
              $response['error']=true;
				 $response['message']='Some Details are Missing';
		} 
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>