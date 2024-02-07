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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ip = trim($data->ip);
$wardname = trim($data->wardname);
$shifttype = trim($data->shifttype);
$bq1 = trim($data->bq1);
$tq1 = trim($data->tq1);
$bq2 = trim($data->bq2);
$tq2 = trim($data->tq2);
$bq3 = trim($data->bq3);
$tq3 = trim($data->tq3);
$bq4 = trim($data->bq4);
$tq4 = trim($data->tq4);
$bq5 = trim($data->bq5);
$tq5 = trim($data->tq5);
$bq6 = trim($data->bq6);
$tq6 = trim($data->tq6);
$bq7 = trim($data->bq7);
$tq7 = trim($data->tq7);
$bq8 = trim($data->bq8);
$tq8 = trim($data->tq8);
$bq9 = trim($data->bq9);
$tq9 = trim($data->tq9);
$bq10 = trim($data->bq10);
$tq10 = trim($data->tq10);
$q11 = trim($data->q11);
$q12 = trim($data->q12);
$q13 = trim($data->q13);
$q14 = trim($data->q14);
$remarks = trim($data->remarks);
$handoverby_n = trim($data->handoverby_n);
$takeoverby_n = trim($data->takeoverby_n);
$handoverby_d = trim($data->handoverby_d);
$takeoverby_d = trim($data->takeoverby_d);

try {
if(!empty($accesskey) && !empty($shifttype) && !empty($ip) && !empty($bq1)  && !empty($bq2)  && !empty($bq3) && !empty($bq4)  && !empty($bq5)  && !empty($bq6)  && !empty($bq7)  && !empty($bq8)  && !empty($bq9)  && !empty($bq10) && !empty($q11) && !empty($q12) && !empty($q13) && !empty($q14) && !empty($handoverby_n) && !empty($takeoverby_n)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
    $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
    $validate->bindParam(':ip', $ip, PDO::PARAM_STR);
    $validate -> execute();
    $validates = $validate->fetch(PDO::FETCH_ASSOC);
    if($validate -> rowCount() > 0){
        
            $insert = $pdo4->prepare("INSERT IGNORE INTO `shift_handover`(`sno`, `admissionno`,`wardname`, `shifttype`, `bq1`, `tq1`, `bq2`, `tq2`, `bq3`, `tq3`, `bq4`, `tq4`, `bq5`, `tq5`, `bq6`, `tq6`, `bq7`, `tq7`, `bq8`, `tq8`, `bq9`, `tq9`, `bq10`, `tq10`, `q11`, `q12`, `q13`, `q14`, `remarks`, `handoverby_n`, `takeoverby_n`, `handoverby_d`, `takeoverby_d`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `estatus`)  VALUES (NULL, :ip,:wardname,:shifttype, :bq1,  :tq1, :bq2, :tq2, :bq3, :tq3, :bq4, :tq4, :bq5, :tq5, :bq6, :tq6, :bq7, :tq7,:bq8, :tq8, :bq9, :tq9, :bq10, :tq10, :q11, :q12, :q13, :q14, :remarks, :handoverby_n, :takeoverby_n, :handoverby_d, :takeoverby_d, :userid,  CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP,  'Active')");
            $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
            $insert->bindParam(':wardname', $wardname, PDO::PARAM_STR);
            $insert->bindParam(':shifttype', $shifttype, PDO::PARAM_STR);
            $insert->bindParam(':bq1', $bq1, PDO::PARAM_STR);
            $insert->bindParam(':tq1', $tq1, PDO::PARAM_STR);
            $insert->bindParam(':bq2', $bq2, PDO::PARAM_STR);
            $insert->bindParam(':tq2', $tq2, PDO::PARAM_STR);
            $insert->bindParam(':bq3', $bq3, PDO::PARAM_STR); 
            $insert->bindParam(':tq3', $tq3, PDO::PARAM_STR);
            $insert->bindParam(':bq4', $bq4, PDO::PARAM_STR);
            $insert->bindParam(':tq4', $tq4, PDO::PARAM_STR);
            $insert->bindParam(':bq5', $bq5, PDO::PARAM_STR);
            $insert->bindParam(':tq5', $tq5, PDO::PARAM_STR);
            $insert->bindParam(':bq6', $bq6, PDO::PARAM_STR);
            $insert->bindParam(':tq6', $tq6, PDO::PARAM_STR);
            $insert->bindParam(':bq7', $bq7, PDO::PARAM_STR);
            $insert->bindParam(':tq7', $tq7, PDO::PARAM_STR);
            $insert->bindParam(':bq8', $bq8, PDO::PARAM_STR);
            $insert->bindParam(':tq8', $tq8, PDO::PARAM_STR);
            $insert->bindParam(':bq9', $bq9, PDO::PARAM_STR);
            $insert->bindParam(':tq9', $tq9, PDO::PARAM_STR);
            $insert->bindParam(':bq10', $bq10, PDO::PARAM_STR);
            $insert->bindParam(':tq10', $tq10, PDO::PARAM_STR);
            $insert->bindParam(':q11', $q11, PDO::PARAM_STR);
            $insert->bindParam(':q12', $q12, PDO::PARAM_STR);
            $insert->bindParam(':q13', $q13, PDO::PARAM_STR);
            $insert->bindParam(':q14', $q14, PDO::PARAM_STR);
            $insert->bindParam(':remarks', $remarks, PDO::PARAM_STR);
            $insert->bindParam(':handoverby_n', $handoverby_n, PDO::PARAM_STR);
            $insert->bindParam(':takeoverby_n', $takeoverby_n, PDO::PARAM_STR);
            $insert->bindParam(':handoverby_d', $handoverby_d, PDO::PARAM_STR);
            $insert->bindParam(':takeoverby_d', $takeoverby_d, PDO::PARAM_STR);
            $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $insert->execute();
            if($insert-> rowCount() > 0){
				http_response_code(200);
                $response['error'] = false;
              $response['message']= "Data saved";
             
              }else{
				  http_response_code(503);
                  $response['error'] = true;
                  $response['message']= "Sorry! Please try again";
              }
}else{
	http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
  }
//Check User Access End
}else{
	http_response_code(400);
    $response['error'] = true;
      $response['message']= "Access Denied";
  }
}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();;
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
