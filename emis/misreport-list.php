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
$type = $data->type;
$fdate=$data->fdate;
$tdate=$data->tdate;
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
	
	
    $org_list = $pdoread -> prepare("SELECT if(`patient_category`='GENERAL','CASH',`patient_category`) AS name,SUM(`total_bill`) AS total,(SELECT SUM(`total_bill`) AS count FROM `registration` WHERE Date(bill_closure_date) BETWEEN (:fdate - INTERVAL 1 MONTH) AND (:tdate - INTERVAL 1 MONTH) AND `branch`=:branchcode AND `patient_category`=t.patient_category AND admissionstatus='Discharged') AS lasttotal FROM `registration` t WHERE Date(t.bill_closure_date) BETWEEN :fdate AND :tdate AND t.`branch`=:branchcode AND admissionstatus='Discharged'  and t.patient_category!='NULL' GROUP by if(t.`patient_category`='GENERAL','CASH',t.`patient_category`) order by total DESC");
	
	$org_list->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
	$org_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$org_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$org_list -> execute();
	$finaltotal=0;
	$finallasttotal=0;
	if($org_list -> rowCount() > 0){

	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	while($org_lists = $org_list->fetch(PDO::FETCH_ASSOC)){
		if($type=="Thousand"){
		$count=round(($org_lists['total']/1000),2);
		$count1=round(($org_lists['lasttotal']/1000),2);
		}else if($type=="Lacs"){
		$count=round(($org_lists['total']/100000),2);
		$count1=round(($org_lists['lasttotal']/100000),2);
		}else if($type=="Million"){
		$count=round(($org_lists['total']/1000000),2);
		$count1=round(($org_lists['lasttotal']/1000000),2);
		}else if($type=="Crores"){
		$count=round(($org_lists['total']/10000000),2);	
		$count1=round(($org_lists['lasttotal']/10000000),2);	
		}
		if($count=='0' && $count1=='0'){
			$totalcolor="#a85032";
			$lastcolor="#a85032";
		}else if($count<$count1){
			$totalcolor="#a85032";
			$lastcolor="#29752c";
		}else{
			$lastcolor="#a85032";
			$totalcolor="#29752c";
		}
		
		$temp=[
		"name"=>$org_lists['name'],
		"total"=>strval($count),
		"lasttotal"=>strval($count1),
		"totalcolor"=>$totalcolor,
		"lastcolor"=>$lastcolor,
		];
		
		array_push($response1,$temp);
	$finaltotal=$finaltotal+$count;
	$finallasttotal=$finallasttotal+$count1;
	
	
	}
	if($finaltotal=='0' && $finallasttotal=='0'){
			$totalcolor1="#a85032";
			$lastcolor1="#a85032";
		}else if($finaltotal<$finallasttotal){
			$totalcolor1="#a85032";
			$lastcolor1="#29752c";
		}else{
			$totalcolor1="#a85032";
			$lastcolor1="#29752c";
		}
	
	/*  $temp1=[
		"name"=>"Total",
		"total"=>strval($finaltotal),
		"lasttotal"=>strval($finallasttotal),
		"totalcolor"=>$totalcolor1,
		"lastcolor"=>$lastcolor1,
		];
		
		array_push($response1,$temp1);
	
	 */
	
	$response['totlacount'] = strval($finaltotal);
	$response['lasttotalcount'] = strval($finallasttotal);
	$response['lastcolor'] =$lastcolor1;
	$response['totalcolor'] = $totalcolor1;
	$response['totalname'] ="Total";
	$response['misreportlist'] = $response1;
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