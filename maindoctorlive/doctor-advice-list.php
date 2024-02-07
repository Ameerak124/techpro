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
$response = array();
$accesskey = trim($data->accesskey);
$reqno=trim($data->reqno);
try{

if(!empty($accesskey)&& !empty($reqno)){
//Check access 
$check =$pdoread->prepare("SELECT `userid`AS empid FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check -> execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0) {
//Access verified//
$query=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`doctor_advice`,date_format(`doctor_advice_created_date`,'%d-%b-%Y') AS doctor_advice_created_date ,`doctor_advice_remarks` FROM (SELECT @a:=0) AS a,`op_biling_history` WHERE `op_biling_history`.`status`='Visible' AND `op_biling_history`.`requisition_no`=:reqno ");
  $query->bindParam(':reqno', $reqno, PDO::PARAM_STR);
$query->execute();
$queryres=$query->fetch(PDO::FETCH_ASSOC);
if($query->rowCount()>0) {
 $sn=0;
 http_response_code(200);
    $response['error']=false;
    $response['message']='Data found';
    $response['advicelist'][]=$queryres;
       /*  $response['sno']=$queryres['sno'];
        $response['doctor_advice']=$queryres['doctor_advice'];
        $response['doctor_advice_remarks']=$queryres['doctor_advice_remarks'];
        $response['doctor_advice_created_date']=$queryres['doctor_advice_created_date']; */
        }else{	
     http_response_code(503);		
    $response['error']=true;
    $response['message']='No data found';
        }
   }else {
     http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
   }
   }else{
     http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
   }
}catch(PDOException $e) {
    http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>