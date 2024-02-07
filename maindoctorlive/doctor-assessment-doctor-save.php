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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ip = trim($data->ip);
$picked = explode(',', $data->picked);
try {
//Check empty Parameters Start
if(!empty($accesskey) && !empty($ip) && !empty($picked)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   $list = $pdoread->prepare("SELECT ROW_NUMBER() OVER (ORDER BY `registration`.`consultantname`) AS sno,`registration`.`consultantname` AS dname ,`registration`.`department` AS department
   FROM `registration` WHERE `registration`.`status`='Visible' AND `registration`.`admissionno`=:ip");
        $list->bindParam(':ip', $ip, PDO::PARAM_STR);
        $list->execute();
        if($list -> rowCount() > 0){
          http_response_code(200);
          $response['error'] = false;
          $response['message']= "Data found";
        $i = 1;
        while ($row = $list->fetch(PDO::FETCH_ASSOC)) {
          if (in_array($i, $picked)) {
              $formatted_row = implode( array(
                  $row['dname']."\r\n",
                  $row['department']."\r\n",
                  "\n",
                  "\n"
              ));
              $formatted_rows[] = $formatted_row;
            }
          $i++;
      }
      
      // Join the formatted rows with a separator
      http_response_code(200);
              $response['error'] = false;
  $response['message']= "Data picked";
$response['Doctor_Details']= $formatted_rows; 

}else{
  http_response_code(503);
  $response['error'] = true;
  $response['message']= "No data found";
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
$pdoread = null;
?>
