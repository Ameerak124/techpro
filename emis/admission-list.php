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
$branchcode = $data->branchcode;
$fdate=$data->fdate;
$tdate=$data->tdate;
$type=$data->type;
$response = array();
$response1 = array();
try
{
if(!empty($accesskey) && !empty($branchcode) && !empty($type) && !empty($fdate)&& !empty($tdate)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	
	
    $org_list = $pdoread -> prepare("SELECT if(registration.organization_name='No Update' || registration.organization_name='','Cash',registration.organization_name) AS name,COUNT(*) AS count FROM `registration` WHERE `admissionstatus`!='Cancelled' AND `cost_center`=:branchcode AND Date(`createdon`) BETWEEN :fdate AND :tdate  GROUP by if(registration.organization_name='No Update' || registration.organization_name='','Cash',registration.organization_name) order by count desc");
	$org_list->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
	$org_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$org_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$org_list -> execute();
	$finaltotal=0;
	if($org_list -> rowCount() > 0){
	
	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	while($org_lists = $org_list->fetch(PDO::FETCH_ASSOC)){
		
	$temp=[
		"name"=>$org_lists['name'],
		"count"=>$org_lists['count'],
		
		];
		
		array_push($response1,$temp);
	 $finaltotal=$finaltotal+$org_lists['count'];
	
	
	
	}
	 /* $temp1=[
		"name"=>"Total",
		"count"=>strval($finaltotal)
		];
		
		array_push($response1,$temp1); */
			$response['totalname'] = "Total";
	$response['totlacount'] = strval($finaltotal);
	$response['admissionlist'] = $response1;
	
	
	
	}
	else{
		
			http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
	}
		
	
 }else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
} 

 }else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
 }catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>