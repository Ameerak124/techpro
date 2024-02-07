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
try{
if(!empty($accesskey)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check->rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
$my_array =["2ND FLOOR","2ND FLOOR DELUXE ROOM","2ND FLOOR SUITE ROOM","3RD FLOOR SINGLE ROOM","3RD FLOOR TWIN SHARING","4TH FLOOR SINGLE ROOM","4TH FLOOR TWIN SHARING","CATH ICU","CT ICU","DIALYSIS","EMERGENCY WARD","MICU","PRE OP","SICU"];
http_response_code(200);
 $response['error'] = false; 
 $response['message']= "Data Found";
 for($x = 0; $x < sizeof($my_array); $x++){
       $response['patientmovementdroplist'][$x]['wardname']=$my_array[$x];	
  }	
      }else{
            http_response_code(400);
            $response['error'] = true;
            $response['message']="Access Denied";
       }  
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
}
}
catch(PDOException $e) {
     http_response_code(503);
     $response['error'] = true;
     $response['message']= "Connection failed".$e->getMessage();
 }
echo json_encode($response);
$pdoread = null;
?>