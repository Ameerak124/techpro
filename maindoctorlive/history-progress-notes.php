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
$data=json_decode(file_get_contents("php://input"));
$accesskey =$data->accesskey;
$umrno=$data->umrno;
$category=$data->category;
$response =array();
try {
if(!empty($accesskey) && !empty($umrno)&& !empty($category)){
   
$check = $pdoread->prepare("SELECT `userid` ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$checkresult=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount() > 0){
$query=$pdoread->prepare("SELECT $category AS catageory,DATE_FORMAT(`createdon`,'%d-%b-%Y') AS createdon,
(SELECT CONCAT(`doctor_master`.`title`,'',`doctor_master`.`doctor_name`)AS docname FROM `doctor_master` WHERE `doctor_master`.`doctor_uid`=`doctor_progress_notes`.`doctor_uid`)AS doctor_name
FROM `doctor_progress_notes` WHERE   `umrno`=:umrno");
$query->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$query->execute();

if($query->rowCount()>0){
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data Found";
 while($result=$query->fetch(PDO::FETCH_ASSOC)){
		$response['historydetails'][]=$result;
	}

}else{
	http_response_code(503);
$response['error']=true;
$response['message']='Already deleted';
}
}else {
	http_response_code(400);
$response['error']=true;
$response['message']='Access denied!';
}
}else {
	http_response_code(400);
$response['error']=true;
$response['message']='Sorry! Some Details Are Missing';
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread=null;
?>