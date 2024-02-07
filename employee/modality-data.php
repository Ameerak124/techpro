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
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$branch = $data->branch;
$response = array();
$response1 = array();
try
{
if(!empty($accesskey)){

 $check = $pdo -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$location = explode(",",$result['storeaccess']);
$i = 0;
if($check -> rowCount() > 0){
	
	
	if($branch=="All"){
    $modality_list = $pdo -> prepare("SELECT if(services_master.modality = '','OTHERS',services_master.modality) as short_name ,ifnull(Count(*),0) AS count FROM `op_biling_history` INNER join services_master on services_master.service_code=op_biling_history.servicecode inner join op_billing_generate on op_biling_history.billno=op_billing_generate.inv_no WHERE date(op_billing_generate.bill_date) between :fdate AND :tdate AND services_master.service_status='Active' AND `status`='Visible' group by modality");
	$modality_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$modality_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$modality_list -> execute();
	}else{
	$modality_list = $pdo -> prepare("SELECT if(services_master.modality = '','OTHERS',services_master.modality) as short_name ,ifnull(Count(*),0) AS count FROM `op_biling_history` INNER join services_master on services_master.service_code=op_biling_history.servicecode inner join op_billing_generate on op_biling_history.billno=op_billing_generate.inv_no WHERE date(op_billing_generate.bill_date) between :fdate AND :tdate AND `status`='Visible'  and op_biling_history.costcenter=:branch AND services_master.service_status='Active'  group by modality");
	$modality_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$modality_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$modality_list->bindParam(':branch', $branch, PDO::PARAM_STR);
	$modality_list -> execute();
	}
	
if($modality_list -> rowCount() > 0){
	$modality_data = $modality_list->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	$response['title']="MODALITY";
	
	
	/* $my_array = array("BMD","CARDIO","CT","MAARM","MRI","NUC.MED.","USG","X RAY","LAB");
	$my_array1 = array("512","231","80","0","0","0","0","0","0");
	
	
	for($x = 0; $x < sizeof($my_array); $x++){	
	$temp=[
	"name"=>$my_array[$x],
	"value"=>number_format($my_array1[$x]),
	];
	array_push($response1,$temp);
	} */
	 while($modality_data = $modality_list->fetch(PDO::FETCH_ASSOC)){	
	$temp=[
	"name"=>$modality_data['short_name'],
	"value"=>$modality_data['count'],
	];
	array_push($response1,$temp);
	}
     $response['modalitydata'] = $response1;
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No Data Found";
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
   unset($con);
 
?>