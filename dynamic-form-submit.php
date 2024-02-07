<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
if(!empty($data)){ 
}else{
$dataa = json_encode($_POST);
$data = json_decode($dataa);
}
$userid = "06920";
$formid = $data->formid;
$answers = $data->answers;
$image = $data->image;
$signaturedate = $data->signaturedate;
if(!empty($formid) && !empty($answers)){
//Check access 
$check = $pdoread -> prepare("SELECT `username`, `emailid`, `mobile`,userid FROM `user_logins` WHERE `userid` = :userid AND `status` = 'Active'");
$check->bindParam(':userid', $userid, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$userid = $result['userid'];
$username = $result['username'];
$emailid = $result['emailid'];
$mobile = $result['mobile'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $query = "SELECT IFNULL(MAX(`transid`),'DYF23020000') AS transid FROM `dynamic_form_submit` LIMIT 1";   
     $sbmt = $pdoread -> prepare($query);   
     $sbmt -> execute();
     if($sbmt -> rowCount() > 0){
          $data = $sbmt -> fetch(PDO::FETCH_ASSOC);
          $transid  = $data['transid'];
          ++$transid;
          if($transid != ''){
			
			$ImagePath = "signatureimage/".$transid."_1.png";	
               $submitquery = "INSERT INTO `dynamic_form_submit`(`customer`, `mobile`, `emailid`, `form_id`, `form_name`, `form_description`, `department`, `frequency`, `usage_type`, `createdon`, `createdby`, `status`, `transid`,signature_image,signature_datetime) SELECT :username,:mobile,:emailid,:formid,`formname`, `formdescription`, `department`,  `frequency`,`usage_type`,CURRENT_TIMESTAMP,:userid,'Pending',:transid,:ImagePath,:signaturedate FROM `main_dynamic_form` WHERE `form_id`=:formid"; 
               $dynamic_sbmt = $pdo4 -> prepare($submitquery);
               $dynamic_sbmt -> bindParam(":transid",$transid,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":mobile",$mobile,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":emailid",$emailid,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":formid",$formid,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":username",$username,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":userid",$userid,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":userid",$userid,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":ImagePath", $ImagePath,PDO::PARAM_STR); 
               $dynamic_sbmt -> bindParam(":signaturedate", $signaturedate,PDO::PARAM_STR); 
               $dynamic_sbmt -> execute();
               if($dynamic_sbmt -> rowCount() > 0){
				   
				   $answersdata=explode("|*",(str_replace('"','',$answers)));
				   foreach ($answersdata as $answersdata11) {
				  $answersdata1=explode("|-",$answersdata11);
				 
			    $submitquery1 = "INSERT INTO `dynamic_form_submit_answer`(`transid`, `formid`, `questionid`, `question_name`, `question_type`, `answers`, `createdon`, `createdby`, `status`) VALUES (:transid,:formid,:questionid,:question_name,:question_type,:answers,CURRENT_TIMESTAMP,:userid,'Pending')"; 
               $dynamic_sbmt1 = $pdo4 -> prepare($submitquery1);
               $dynamic_sbmt1 -> bindParam(":transid",$transid,PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":formid",$answersdata1[0],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":userid",$userid,PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":questionid",$answersdata1[1],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":question_name",$answersdata1[2],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":question_type",$answersdata1[3],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> bindParam(":answers",$answersdata1[4],PDO::PARAM_STR); 
               $dynamic_sbmt1 -> execute();
				   }
				   $decodedImage = base64_decode($image);
                       file_put_contents($ImagePath,$decodedImage);
				http_response_code(200);
                    $response['error']= false;
                    $response['message']="Submitted Successfully";
                    $response['transid']=$transid;
                  
               }
               else
               {
			     http_response_code(503);
                    $response['error']= true;
                    $response['message']="Something went wrong";
               }
          }
          else{
			  http_response_code(503);
               $response['error'] = true;
               $response['message'] = "Something went wrong";
          }
     }
     else{http_response_code(503);
          $response['error'] = true;
          $response['message'] = "Something went wrong";
     } 
}
else
{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing"; 
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>