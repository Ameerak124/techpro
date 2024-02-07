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
$form_id= $data->form_id;
$transid= $data->transid;
$accesskey= $data->accesskey;
$response = array();
try{	
if(!empty($form_id) && !empty($accesskey)){
$check1 = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check1->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check1 -> execute();
if($check1 -> rowCount() > 0){
	$result = $check1->fetch(PDO::FETCH_ASSOC);	
$stmt3=$pdoread->prepare("SELECT signature_datetime, if(signature_image='','',Concat(:baseurl,signature_image)) AS signature_image FROM `dynamic_form_submit` WHERE   `form_id`=:form_id AND transid=:transid");
$stmt3->bindParam(':form_id', $form_id, PDO::PARAM_STR);
$stmt3->bindParam(':transid', $transid, PDO::PARAM_STR);
$stmt3->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$stmt3-> execute();
  
 $row = $stmt3 -> fetch(PDO::FETCH_ASSOC);
$stmt2=$pdoread->prepare("SELECT `sno`, `transid`, `formid`, `questionid`, `question_name`, `question_type`, `answers`, Date_format(`createdon`,'%d-%b-%Y %H:%i %p') As date, `createdby`, `status` FROM `dynamic_form_submit_answer` where  `formid`=:form_id AND transid=:transid");
$stmt2->bindParam(':form_id', $form_id, PDO::PARAM_STR);
$stmt2->bindParam(':transid', $transid, PDO::PARAM_STR);
$stmt2-> execute();

if($stmt2 -> rowCount() > 0){

		 http_response_code(200);
           $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
		 $response['color']="#1E88E5";
		 $response['signature_datetime']=$row['signature_datetime'];
		 $response['signature_image']=$row['signature_image'];
	      $response['dynamicformanswerlist']= $data;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
	}else{
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Access denied";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>
