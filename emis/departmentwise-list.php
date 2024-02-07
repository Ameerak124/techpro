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
	
	
    //$org_list = $pdo -> prepare("SELECT department AS `department_name`,IFNULL(SUM(`total_bill`),0) AS count FROM `registration` WHERE  Date(`bill_closure_date`) between :fdate AND :tdate AND `admissionstatus`='Discharged' AND `cost_center`=:branchcode GROUP by `department` order by count DESC");
   $org_list = $pdoread -> prepare("SELECT department AS department_name,(SUM(`after_val`)+IFNULL((SELECT SUM(`total_bill`) AS count FROM `registration`  WHERE Date(bill_closure_date) BETWEEN :fdate AND :tdate AND `branch`=:branchcode AND admissionstatus='Discharged' AND  registration.consultantcode=referenceno),0)+(if(referenceno NOT like '%FTC%',(SELECT SUM(reg) FROM `revenue_breakup` WHERE `date` BETWEEN :fdate AND :tdate AND `cost_center`=:branchcode),'0'))+(if(referenceno NOT like '%FTC%',(SELECT SUM(pharmacy) FROM `revenue_breakup` WHERE `date` BETWEEN :fdate AND :tdate AND `cost_center`=:branchcode),'0'))) AS count FROM `op_billing_generate` INNER join doctor_master on `referenceno`=doctor_master.doctor_code  WHERE Date(op_billing_generate.created_on) BETWEEN :fdate AND :tdate AND op_billing_generate.`status` = 'Confirmed' AND  `op_billing_generate`.`cost_center` =:branchcode AND doctor_master.`status`='Active' Group by department order by count desc");
	
	$org_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$org_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$org_list->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
	$org_list -> execute();
	$finaltotal=0;
	if($org_list -> rowCount() > 0){

	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	while($org_lists = $org_list->fetch(PDO::FETCH_ASSOC)){
		if($type=="Thousand"){
		$count=($org_lists['count']/1000);
		}else if($type=="Lacs"){
		$count=($org_lists['count']/100000);
		}else if($type=="Million"){
		$count=($org_lists['count']/1000000);
		}else if($type=="Crores"){
		$count=($org_lists['count']/10000000);	
		}
		$counts=round($count,2);
	$temp=[
		"department_name"=>$org_lists['department_name'],
		"count"=>strval($counts),
		
		];
		
		array_push($response1,$temp);
	 $finaltotal=$finaltotal+$count;
	
	
	
	}
	/*  $temp1=[
		"department_name"=>"Total",
		"count"=>strval($finaltotal)
		];
		
		array_push($response1,$temp1);  */
		$finaltotals=round($finaltotal,3);
	 $response['totalname'] = "Total";
	$response['totlacount'] = strval($finaltotals);
	 
	$response['departmentwiselist'] = $response1;
	
	
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