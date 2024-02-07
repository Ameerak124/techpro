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
$cat=trim($data->cat);
$searchterm=$data->searchterm;
try {
	if(!empty($accesskey) ){

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
if($cat=='ANAESTHESIOLOGY') {
$country = $pdoread -> prepare(" SELECT CONCAT(`title`,' ',`doctor_name`) AS displayname, `doctor_name` AS searchname,`department` AS department, `doctor_uid` AS doctorcode ,`designation` FROM `doctor_master` WHERE( `doctor_uid` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936' AND `department`='ANAESTHESIOLOGY') OR( `doctor_name` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936'AND `department`='ANAESTHESIOLOGY')");
	$country->bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
	$country->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
    $country -> execute();

// }elseif($cat==''){
// 	$country = $pdo -> prepare(" SELECT CONCAT(`title`,' ',`doctor_name`) AS displayname, `doctor_name` AS searchname,`department` AS department, `doctor_uid` AS doctorcode FROM `doctor_master` WHERE( `doctor_uid` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936') OR( `doctor_name` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936')");
// 	$country->bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
// 	$country->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
//     $country -> execute();
	
// }elseif($cat==''){
// 	$country = $pdo -> prepare(" SELECT CONCAT(`title`,' ',`doctor_name`) AS displayname, `doctor_name` AS searchname,`department` AS department, `doctor_uid` AS doctorcode FROM `doctor_master` WHERE( `doctor_uid` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936') OR( `doctor_name` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936')");
// 	$country->bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
// 	$country->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
//     $country -> execute();

}else{
	$country = $pdoread -> prepare(" SELECT CONCAT(`title`,' ',`doctor_name`) AS displayname, `doctor_name` AS searchname,`department` AS department, `doctor_uid` AS doctorcode,designation FROM `doctor_master` WHERE( `doctor_uid` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936') OR( `doctor_name` LIKE :searchterm AND  `status`='Active' AND `location`=:branch AND `doctor_uid` != 'EMR0936')");
	$country->bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
	$country->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
    $country -> execute();

}
if($country -> rowCount() > 0){
	$response['error']= false;
	$response['message']= "Data found";
// 	$response['list'][0]['displayname']= "DR. ER PHYSICIAN";
// 	$response['list'][0]['searchname']= "DR. ER PHYSICIAN";
// $response['list'][0]['department']="EMERGENCY MEDICINE";
// $response['list'][0]['doctorcode']= "EMR0936";
	
	$s=1;
	while($result = $country->fetch(PDO::FETCH_ASSOC)){
$response['list'][$s]['displayname']=$result['displayname'];
$response['list'][$s]['searchname']=$result['searchname'];
$response['list'][$s]['department']=$result['department'];
$response['list'][$s]['doctorcode']=$result['doctorcode'];
$response['list'][$s]['designation']=$result['designation'];
$s++;

}
}else{
	$response['error']= true;
	$response['message']="No data found";
}
}else{
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>