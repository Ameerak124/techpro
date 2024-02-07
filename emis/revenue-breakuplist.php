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
$branchname = $data->branchname;
$type = $data->type;
$fdate=$data->fdate;
$tdate=$data->tdate;
$response = array();
$response1 = array();
try
{
if(!empty($accesskey) && !empty($branchcode) && !empty($type) && !empty($fdate)&& !empty($tdate)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess,Date_Format(:tdate,'%d-%b-%Y') As tdate FROM `user_logins` WHERE (`mobile_accesskey`= :accesskey AND `status`= 'Active') OR (`accesskey`= :accesskey AND `status`= 'Active') LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> bindParam(":tdate", $tdate, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	
	
    $org_list = $pdoread -> prepare("SELECT  Date_format(`date`,'%d-%b-%Y') as date, `cost_center`, `revenue`, `op`, `pharmacy`, `reg`, round((`revenue`+`op`+`pharmacy`+`reg`),2) AS total FROM `revenue_breakup` WHERE `cost_center`=:branchcode AND date BETWEEN :fdate AND :tdate");
	
	$org_list->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
	$org_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$org_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$org_list -> execute();
	$finaltotal=0;
	$regtotal=0;
	$pharmacytotal=0;
	$optotal=0;
	$revenuetotal=0;
	
	if($org_list -> rowCount() > 0){

	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	while($org_lists = $org_list->fetch(PDO::FETCH_ASSOC)){
		if($type=="Thousand"){
		$revenue=round(($org_lists['revenue']/1000),2);
		$op=round(($org_lists['op']/1000),2);
		$pharmacy=round(($org_lists['pharmacy']/1000),2);
		$reg=round(($org_lists['reg']/1000),2);
		}else if($type=="Lacs"){
		$revenue=round(($org_lists['revenue']/100000),2);
		$op=round(($org_lists['op']/100000),2);
		$pharmacy=round(($org_lists['pharmacy']/100000),2);
		$reg=round(($org_lists['reg']/100000),2);
		}else if($type=="Million"){
		$revenue=round(($org_lists['revenue']/1000000),2);
		$op=round(($org_lists['op']/1000000),2);
		$pharmacy=round(($org_lists['pharmacy']/1000000),2);
		$reg=round(($org_lists['reg']/1000000),2);
		}else if($type=="Crores"){
		$revenue=round(($org_lists['revenue']/10000000),2);	
		$op=round(($org_lists['op']/10000000),2);	
		$pharmacy=round(($org_lists['pharmacy']/10000000),2);	
		$reg=round(($org_lists['reg']/10000000),2);	
		}
		/* if($count=='0' && $count1=='0'){
			$totalcolor="#a85032";
			$lastcolor="#a85032";
		}else if($count<$count1){
			$totalcolor="#a85032";
			$lastcolor="#29752c";
		}else{
			$lastcolor="#a85032";
			$totalcolor="#29752c";
		} */
		$totals=$op+$reg+$pharmacy+$revenue;
		$temp=[
		"date"=>$org_lists['date'],
		"iprevenue"=>strval($revenue),
		"oprevenue"=>strval($op),
		"oppharmacy"=>strval($pharmacy),
		"regrevenue"=>strval($reg),
		"daytotal"=>strval($totals),
		];
		
		array_push($response1,$temp);
	$revenuetotal=$revenuetotal+$revenue;
	$optotal=$optotal+$op;
	$pharmacytotal=$pharmacytotal+$pharmacy;
	$regtotal=$regtotal+$reg;
	$finaltotal=$finaltotal+$totals;
	
	
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
	
	$response['maintitle'] = "Revenue Report till ".$result['tdate']. " ( ".$branchname." )";
	$response['datetitle'] = "Date";
	$response['revenuetitle'] = "IP Revenue";
	$response['optitle'] = "OP Revenue";
	$response['pharmacytitle'] = "OP Pharmacy Revenue";
	$response['regtitle'] = "Registration Revenue";
	$response['daytitle'] = "Day Total";
	$response['revenuetotal'] = strval($revenuetotal);
	$response['optotal'] = strval($optotal);
	$response['pharmacytotal'] = strval($pharmacytotal);
	$response['regtotal'] =  strval($regtotal);
	$response['finaltotal'] =  strval($finaltotal);
	$response['totalname'] ="Total";
	$response['revenuebreakuplist'] = $response1;
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