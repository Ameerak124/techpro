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
$accesskey=trim($data->accesskey);
try{
     if(!empty($accesskey)){
          $accesscheck ="SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
               $stmt = $pdoread->prepare($accesscheck);
               $stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
               $stmt->execute();
               if($stmt->rowCount() > 0){
               $row = $stmt->fetch(PDO::FETCH_ASSOC);
               $my_array = array("Wards/Rooms","ER","ICU","OT","Labor Room");
              http_response_code(200);
              $response['error'] = false; 
              $response['message']= "Data Found";
              for($x = 0; $x < sizeof($my_array); $x++){
                    $response['opdadmitlist'][$x]['Wardname']=$my_array[$x];	
               }	
                   }else{
                         http_response_code(400);
                        $response['error'] = true;
                         $response['message']="Access denied!";
                    }  
          }else{
          http_response_code(400);
          $response['error'] = true;
          $response['message'] ="Sorry! Some details are missing";
          }
          } 
          catch(PDOException $e)
          {
              die("ERROR: Could not connect. " . $e->getMessage());
          }
          echo json_encode($response);
          $pdoread = null;
          ?>