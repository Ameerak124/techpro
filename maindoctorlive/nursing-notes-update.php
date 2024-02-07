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
$admissionno = trim($data->admissionno);
$umrno = trim($data->umrno);
$sno = trim($data->sno);
$shift_type = trim($data->shift_type);
$notes = trim($data->notes);
$ward = trim($data->ward);
try {
if(!empty($accesskey) && !empty($admissionno) && !empty($umrno) && !empty($shift_type) && !empty($notes) && !empty($sno)){
//Check User Access Start
$check = $pdoread -> prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   
    if($shift_type == 'Morning To Afternoon'){
        $shift_time='FROM 8AM TO 2PM';
    } else if($shift_type == 'Afternoon To Night'){
        $shift_time='FROM 2PM TO 8PM';
    } else if($shift_type == 'Night To Morning'){
        $shift_time='FROM 8PM TO 8AM';
    } else if($shift_type == 'Morning To Night'){
        $shift_time='FROM 8AM TO 8PM';
    }
    else{
        $shift_time=''; 
    }
          
    $update = $pdo4->prepare("UPDATE `nursing-notes-tbl` SET `shift_type`= :shift_type, `shift_time`= :shift_time,`notes`=:notes, `ward` =:ward, `modifiedby`=:userid, `modifiedon`=CURRENT_TIMESTAMP WHERE `admissionno` = :admissionno AND `umr_no`=:umrno AND `sno` =:sno AND `estatus`='Active' ");
    $update->bindParam(':shift_type', $shift_type, PDO::PARAM_STR);
    $update->bindParam(':shift_time', $shift_time, PDO::PARAM_STR);
    $update->bindParam(':notes', $notes, PDO::PARAM_STR);         
    $update->bindParam(':ward', $ward, PDO::PARAM_STR);  
    $update->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
    $update->bindParam(':umrno', $umrno, PDO::PARAM_STR);                                                          
    $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
    $update->bindParam(':sno', $sno, PDO::PARAM_STR);
    $update->execute();
        if($update-> rowCount() > 0){
			http_response_code(200);
            $response['error'] = false;
            $response['message']= "Updated Successfully";
                    
            }else{
				http_response_code(503);
                $response['error'] = true;
                 $response['message']= "Not Updated";
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
               $response['message']= "Connection failed".$e->getMessage();
           }
           echo json_encode($response);
           $pdo4 = null;
           $pdoread = null;
 ?>
          