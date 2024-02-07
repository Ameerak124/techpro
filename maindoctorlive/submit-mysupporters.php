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
$name=trim($data->name);
$mobileno=trim($data->mobileno);
$location=trim($data->location);
$type=trim($data->type);
$designation=trim($data->designation);
try {

   if(!empty($accesskey)){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
			
	$uniqueid = $pdoread -> prepare("SELECT CONCAT('MS', LPAD(IFNULL(MAX(SUBSTRING(uniqueid, 3)) + 1, 1), 4, '0')) AS uniqueid FROM my_supporters LIMIT 1;");
$uniqueid -> execute();
$id = $uniqueid->fetch(PDO::FETCH_ASSOC);
$resultid=$id['uniqueid'];
			
    $sql = $pdoread -> prepare("SELECT `sno`, `uniqueid`, `name`, `mobileno`, `location`, `type`, `designation`, `created_on`, `created_by`, `modified_on`, `modified_by`, `status` FROM `my_supporters` WHERE `name`=:name and `mobileno`=:mobileno");
	$sql->bindParam(':name', $name, PDO::PARAM_STR);
$sql->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
$sql -> execute();
if($sql->rowCount() == 0){
$result1 = $sql->fetch(PDO::FETCH_ASSOC);



$insert=$pdo4->prepare("INSERT INTO `my_supporters`(`uniqueid`, `name`, `mobileno`, `location`, `type`, `designation`,`created_on`, `created_by`,`status`) VALUES (:uniqueid,:name,:mobileno,:location,:type,:designation,CURRENT_TIMESTAMP,:userid,'Active')");
$insert->bindParam(':uniqueid', $resultid, PDO::PARAM_STR);
$insert->bindParam(':name', $name, PDO::PARAM_STR);
$insert->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
$insert->bindParam(':location', $location, PDO::PARAM_STR);
$insert->bindParam(':type',$type, PDO::PARAM_STR);
$insert->bindParam(':designation', $designation, PDO::PARAM_STR);
$insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$insert->execute();
 
if($insert->rowCount() > 0){
	http_response_code(200);
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
        
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="Data Already Inserted";
}

}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}

} catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>