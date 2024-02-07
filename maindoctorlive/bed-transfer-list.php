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
try {

if(!empty($ip)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   
    $dt = $pdoread->prepare("SELECT `createdon` FROM `bed_transfer` where `admissionno` = :ip ORDER BY `createdon` ASC");
    $dt->bindParam(':ip', $ip, PDO::PARAM_STR);
    $dt->execute();
   
    if($dt -> rowCount() > 0){
      while($dts = $dt->fetch(PDO::FETCH_ASSOC)){
  //  $list = $pdo->prepare("SELECT @a:=@a+1 AS sno, (DATE_FORMAT(`createdon`,'%d-%b-%Y %H:%i:%s')) as date_time, `service_name` as shiftedfrom, (select IFNULL(`service_name`,'')  from `bed_transfer` where `admissionno` = :ip and `createdon` = (select IFNULL(min(`createdon`),'') from `bed_transfer` where `createdon` >=:credate and `admissionno` = :ip)) as shiftedto, :username as advisedby, `remarks` from (SELECT @a:=0) AS a, `bed_transfer` where `admissionno` = :ip and `createdon` = :credate ");
   $list = $pdoread->prepare("SELECT @a:=@a+1 AS sno, E.datetimes AS DATE_TIME,E.shiftedfrom,(CASE WHEN E.bed_status = 'ON_BED' THEN E.bed_status ELSE (SELECT `service_name` FROM `bed_transfer` WHERE `sno` = E.`reference` LIMIT 1) END) AS shiftedto,E.advisedby,E.remarks FROM (SELECT DATE_FORMAT(`createdon`,'%d-%b-%Y %h:%i %p') AS datetimes,`service_name` AS shiftedfrom,`reference`,`bed_transfer`.`transferedby` AS advisedby,`bed_transfer`.`remarks`,`bed_transfer`.`bed_status` FROM (SELECT @a:=0) AS a,`bed_transfer` WHERE `admissionno` =:ip ORDER BY date(createdon) ,time(createdon)) AS E");
        $list->bindParam(':ip', $ip, PDO::PARAM_STR);
        // $list->bindParam(':credate', $dts['createdon'], PDO::PARAM_STR);
        // $list->bindParam(':username', $result['username'], PDO::PARAM_STR);
        $list->execute();
       http_response_code(200);
            $response['error'] = false;
          $response['message']= "Data found";
          while($results = $list->fetch(PDO::FETCH_ASSOC)){ 
            $response['bedtransferlist'][] = $results;
          }        
        }
        }else{
			http_response_code(503);
            $response['error'] = true;
              $response['message']= "NO data found";
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
	$response['message']= "Connection failed".$e;
}
echo json_encode($response);
$pdoread = null;
?>
