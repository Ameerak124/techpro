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
$accesskey = trim($data->accesskey);
$response = array();
$ip = trim($data->ip);
//$createdfrom = trim($data->createdfrom);
try {
if(!empty($ip)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   $list = $pdoread->prepare("SELECT @a:=@a+1 serial_number,sno, REVERSE(SUBSTRING_INDEX(REVERSE(`sugg_by`), '_', 1)) as sugg_by,`doctor_name`, `ward`, `estatus`, `createdby`, (DATE_FORMAT(`createdon`,'%d-%b-%Y %H:%i:%s')) as createdon, `modifiedby` as Approvedby, (DATE_FORMAT(`modifiedon`,'%d-%b-%Y %H:%i:%s')) as Approved_dt, `appointment_priority`,`patient_clinical_problems`,`reason_for_refferal`,`reffered_for` FROM (SELECT @a:= 0) a, `nursing_doctor_visit` where `ip` = :ip And `consult` = 'Cross Consultation Request'  AND estatus='Approved' order by nursing_doctor_visit.createdon DESC");
 
        $list->bindParam(':ip', $ip, PDO::PARAM_STR);
        //$list->bindParam(':createdfrom', $createdfrom, PDO::PARAM_STR);
        $list->execute();
        if($list-> rowCount() > 0){
			 http_response_code(200);
            $response['error'] = false;
          $response['message']= "Data found";
          while(  $results = $list->fetch(PDO::FETCH_ASSOC)){
            $response['Crossconsultationrequestlist'][] = $results;
          }
          }else{
			   http_response_code(503);
              $response['error'] = true;
              $response['message']= "No data found";
          }

}else{
	 http_response_code(400);
    $response['error'] = true;
      $response['message']= "Access denied";
  }
}else{
	 http_response_code(400);
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();;
}
echo json_encode($response);
$pdoread = null;
?>
