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

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey As `accesskey`,`branch` AS storeaccess,((SELECT SUM(pharmacy) FROM `revenue_breakup` WHERE `date` BETWEEN :fdate AND :tdate AND `cost_center`=:branchcode)) AS pha,(SELECT SUM(reg) FROM `revenue_breakup` WHERE `date` BETWEEN :fdate AND :tdate AND `cost_center`=:branchcode) AS reg FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
$check->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$check->bindParam(':tdate', $tdate, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	
	
       // $org_list = $pdo -> prepare("SELECT `doctor_name`,IFNULL((SELECT SUM(`total_bill`)+SUM(pharmacy_amount)),0) AS count FROM `registration` INNER join doctor_master on `consultantcode`=doctor_master.doctor_uid WHERE doctor_master.`status`='Active' AND Date(`bill_closure_date`) between :fdate AND :tdate AND `admissionstatus`='Discharged' AND `cost_center`=:branchcode GROUP by `consultantcode` order by count desc");

//SELECT if(dm.doctor_code like '%FTC%',dm.doctor_code,dm.doctor_name) As doctorcode,if(dm.doctor_code like '%FTC%',dm.doctor_name,dm.doctor_code) AS doctor_name,if(dm.doctor_code like '%FTC%',dm.doctor_code,'Others')As datas,(SELECT IFNULL(SUM(`after_val`),0) FROM `op_billing_generate` WHERE Date(op_billing_generate.created_on) BETWEEN '2023-09-01' AND '2023-09-01' AND op_billing_generate.`status` = 'Confirmed' AND `op_billing_generate`.`cost_center` ='MCBEG' AND `referenceno`=dm.doctor_code),(SELECT IFNULL(SUM(`total_bill`),0) AS count FROM `registration` WHERE Date(bill_closure_date) BETWEEN '2023-09-01' AND '2023-09-01' AND `branch`='MCBEG' AND admissionstatus='Discharged' AND registration.consultantcode=dm.doctor_code),(if(dm.doctor_code NOT like '%FTC%',(SELECT SUM(pharmacy) FROM `revenue_breakup` WHERE `date` BETWEEN '2023-09-01' AND '2023-09-01' AND `cost_center`='MCBEG'),'0')),(if(dm.doctor_code NOT like '%FTC%',(SELECT SUM(reg) FROM `revenue_breakup` WHERE `date` BETWEEN '2023-09-01' AND '2023-09-01' AND `cost_center`='MCBEG'),'0')) AS count FROM `doctor_master` dm WHERE dm.status='Active' Group by datas;


	   

       $org_list = $pdoread -> prepare("SELECT if(dm.doctor_code like '%FTC%',dm.doctor_name,'Others') AS doctor_name,((SELECT IFNULL(SUM(`after_val`),0) FROM `op_billing_generate` WHERE Date(op_billing_generate.created_on) BETWEEN :fdate AND :tdate AND op_billing_generate.`status` = 'Confirmed' AND `op_billing_generate`.`cost_center` =:branchcode AND `referenceno`=dm.doctor_code)+(SELECT IFNULL(SUM(`total_bill`),0) AS count FROM `registration` WHERE Date(bill_closure_date) BETWEEN :fdate AND :tdate AND `branch`=:branchcode AND admissionstatus='Discharged' AND registration.consultantcode=dm.doctor_code)) AS count FROM `doctor_master` dm WHERE dm.status='Active'  HAVING count!='0' order by count desc");
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
if($org_lists['doctor_name']!="Others"){
	$temp=[
		"doctor_name"=>$org_lists['doctor_name'],
		"count"=>strval($counts),
		
		];
		
		array_push($response1,$temp);
	 $finaltotal=$finaltotal+$count;
	
	
}else{
	$otherstotal=$otherstotal+$org_lists['count'];
}
	}
	
	
	$subtotals=$otherstotal+$result['pha']+$result['reg'];
	
	   if($type=="Thousand"){
			$count_sub=($subtotals/1000);
			}else if($type=="Lacs"){
			$count_sub=($subtotals/100000);
			}else if($type=="Million"){
			$count_sub=($subtotals/1000000);
			}else if($type=="Crores"){
			$count_sub=($subtotals/10000000);	
			}
			$finalsubtotal=round($count_sub,2);
	
	
	
	 $temp1=[
		"doctor_name"=>"Others",
		"count"=>strval($finalsubtotal)
		];
		
		array_push($response1,$temp1);
		
		$finaltotals=round(($finaltotal+$finalsubtotal),3);
	$response['totalname'] = "Total";
	$response['totlacount'] =strval($finaltotals);
	$response['doctorwiselist'] = $response1;
	
	
	
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