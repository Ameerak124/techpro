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
$response = array();
$response1 = array();
try
{
if(!empty($accesskey) && !empty($branchcode) && !empty($fdate)&& !empty($tdate)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	
	
    $org_list = $pdoread -> prepare("SELECT subcategory,Count(*) AS count FROM `op_biling_history` WHERE `category`='Consultation' AND date(createdon) between :fdate AND :tdate AND status='Visible' AND costcenter=:branchcode Group by `subcategory` order by count desc;
");
	
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
		"name"=>$org_lists['subcategory'],
		"count"=>$org_lists['count'],
		
		];
		
		array_push($response1,$temp);
	 $finaltotal=$finaltotal+$org_lists['count'];
	
	
	
	}
	 /* $temp1=[
		"name"=>"Total",
		"count"=>
		];
		
		array_push($response1,$temp1); */
			$response['totalname'] = "Total";
	$response['totlacount'] = strval($finaltotal);
	$response['consultantlist'] = $response1;

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