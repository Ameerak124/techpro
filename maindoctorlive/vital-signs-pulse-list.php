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
$accesskey = $data->accesskey;
$response = array();

try {

if(!empty($accesskey)){
	
//Check User Access Start
$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	
   $list = $pdoread->prepare("SELECT  `age_details`, 
 MAX(CASE WHEN `category` = 'Pulse Men'  And `age_from` =18 And `age_to` = 25 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
         WHEN `category` = 'Pulse Men'  And `age_from` =18 And `age_to` = 25 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`)
 ELSE NULL END) AS `18-25`,  
 MAX(CASE WHEN `category` = 'Pulse Men'  And `age_from` =26 And `age_to` = 35 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Men'  And `age_from` =26 And `age_to` = 35 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `26-35`, 
 MAX(CASE WHEN `category` = 'Pulse Men'  And `age_from` =36 And `age_to` = 45 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Men'  And `age_from` =36 And `age_to` = 45 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `36-45`, 
 MAX(CASE WHEN `category` = 'Pulse Men'  And `age_from` =46 And `age_to` = 55 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Men'  And `age_from` =46 And `age_to` = 55 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `46-55`, 
 MAX(CASE WHEN `category` = 'Pulse Men'  And `age_from` =56 And `age_to` = 65 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Men'  And `age_from` =56 And `age_to` = 65 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `56-65`, 
 MAX(CASE WHEN `category` = 'Pulse Men'  And `age_from` =66 And `age_to` = 100 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Men'  And `age_from` =66 And `age_to` = 100 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `>65`
FROM 
 `normal_vital_signs_range`
WHERE 
 `category` = 'Pulse Men' AND `age_details` IN ('ATHLETE', 'EXCELLENT', 'GOOD', 'ABOVE AVERAGE', 'AVERAGE', 'BELOW AVERAGE', 'POOR')
GROUP BY 
 `age_details` order by `sno`");
 $list->execute();
 if($list-> rowCount() > 0){
     $response['error'] = false;
   $response['message']= "Data found";
   while(  $result = $list->fetch(PDO::FETCH_ASSOC)){
     $response['pulselist']['MEN'][] = $result;
   }
   

   $list1 = $pdoread->prepare("SELECT  `age_details`, 
 MAX(CASE WHEN `category` = 'Pulse Women'  And `age_from` =18 And `age_to` = 25 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
         WHEN `category` = 'Pulse Women'  And `age_from` =18 And `age_to` = 25 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`)
 ELSE NULL END) AS `18-25`,  
 MAX(CASE WHEN `category` = 'Pulse Women'  And `age_from` =26 And `age_to` = 35 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Women'  And `age_from` =26 And `age_to` = 35 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `26-35`, 
 MAX(CASE WHEN `category` = 'Pulse Women'  And `age_from` =36 And `age_to` = 45 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Women'  And `age_from` =36 And `age_to` = 45 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `36-45`, 
 MAX(CASE WHEN `category` = 'Pulse Women'  And `age_from` =46 And `age_to` = 55 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Women'  And `age_from` =46 And `age_to` = 55 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `46-55`, 
 MAX(CASE WHEN `category` = 'Pulse Women'  And `age_from` =56 And `age_to` = 65 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Women'  And `age_from` =56 And `age_to` = 65 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `56-65`, 
 MAX(CASE WHEN `category` = 'Pulse Women'  And `age_from` =66 And `age_to` = 100 AND (`start_point` != `end_point`) THEN CONCAT(`start_point`, '-', `end_point`) 
     WHEN `category` = 'Pulse Women'  And `age_from` =66 And `age_to` = 100 AND (`start_point` = `end_point`) THEN CONCAT('>', `end_point`) ELSE NULL END) AS `>65`
FROM 
 `normal_vital_signs_range`
WHERE 
 `category` = 'Pulse Women' AND `age_details` IN ('ATHLETE', 'EXCELLENT', 'GOOD', 'ABOVE AVERAGE', 'AVERAGE', 'BELOW AVERAGE', 'POOR')
GROUP BY 
 `age_details` order by `sno` ");
       
        $list1->execute();
        if($list1-> rowCount() > 0){
            $response['error'] = false;
          $response['message']= "Data found";
          while(  $results = $list1->fetch(PDO::FETCH_ASSOC)){
            $response['pulselist']['WOMEN'][] = $results;
          }
          }else{
              $response['error'] = true;
              $response['message']= "No data found";
          }
        }else{
            $response['error'] = true;
            $response['message']= "No data found";
        }
//Check User Access End
}else{
    $response['error'] = true; 
      $response['message']= "Access Denied";
  }
}else{
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
