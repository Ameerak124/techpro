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
$branchcode= $data->branchcode;
$type = $data->type;
$fdate=$data->fdate;
$tdate=$data->tdate;
$response = array();
$response1 = array();
try
{
if(!empty($accesskey) && !empty($branchcode) && !empty($type) && !empty($fdate)&& !empty($tdate)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE (`mobile_accesskey`=:accesskey AND `status`= 'Active') OR (`accesskey`= :accesskey AND `status`= 'Active') LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);


if($check -> rowCount() > 0){
	
	 $check21 = $pdoread -> prepare("SELECT `date` FROM `revenue_breakup` WHERE `date`=CURRENT_DATE");
     $check21 -> execute();
	if($check21 ->rowCount()>0){
		$check31 = $pdoread -> prepare("SELECT :branchcode1 AS beg, ((SELECT COALESCE(SUM(`total_bill`),0) AS count FROM `registration` WHERE Date(bill_closure_date)=CURRENT_DATE AND `branch`=:branchcode1 AND admissionstatus='Discharged')+(SELECT IFNULL(SUM(CASE WHEN credit_debit_note.bill_type = 'DEBIT' THEN credit_debit_note.total_bill WHEN credit_debit_note.bill_type = 'CREDIT' THEN credit_debit_note.total_bill*-1 ELSE 0 END),0) AS netamount  FROM credit_debit_note INNER JOIN registration ON registration.admissionno = credit_debit_note.ipnumber WHERE DATE(credit_debit_note.bh_approved_on)=CURRENT_DATE AND credit_debit_note.bh_approval_status = 'Approved' AND credit_debit_note.status = 'Raised' AND registration.admissionstatus = 'Discharged' AND registration.cost_center =:branchcode1)) AS revenue,(SELECT COALESCE(SUM(`after_val`),0) FROM `op_billing_generate` WHERE status='Confirmed' AND cost_center=:branchcode1 AND Date(op_billing_generate.created_on)=CURRENT_DATE) AS op,(SELECT COALESCE(round(SUM(total),2),0) FROM `op_pharmacy_inv_generate` INNER JOIN `op_pharmacy_inv_item` ON `op_pharmacy_inv_item`.`so_number` = `op_pharmacy_inv_generate`.`inv_no` WHERE op_pharmacy_inv_generate.`status` = 'Completed' AND `item_status` != 'Delete' AND op_pharmacy_inv_generate.`cost_center` =:branchcode1 AND Date(op_pharmacy_inv_item.created_on)=CURRENT_DATE) AS pharmacy,(SELECT COALESCE(SUM(`payment_history`.`total`),0) AS netvalue FROM `payment_history` LEFT JOIN `umr_registration` ON `umr_registration`.`umrno` = `payment_history`.`admissionon` WHERE DATE(`payment_history`.`createdon`)=CURRENT_DATE AND `payment_history`.`bill_type` = 'registration' AND `payment_history`.`status` = 'Visible' AND `payment_history`.`credit_debit` = 'CREDIT' AND `umr_registration`.`branch` =:branchcode1 AND `umr_registration`.`status` = 'Visible') AS reg;");
		$check31->bindParam(':branchcode1', $branchcode, PDO::PARAM_STR);
		$check31 -> execute();
		 $org_listss = $check31->fetch(PDO::FETCH_ASSOC);
		
		 $check213 = $pdo4 -> prepare("UPDATE `revenue_breakup` SET `revenue`=(:revenue), `op`=(:op), `pharmacy`=(:pharmacy), `reg`=(:reg),`total`=(:total),`last_updatedon`=CURRENT_TIMESTAMP WHERE `date`=CURRENT_DATE AND `cost_center`=:branchcodes");
		 $check213->bindParam(':revenue', $org_listss['revenue'], PDO::PARAM_STR);
		 $check213->bindParam(':op', $org_listss['op'], PDO::PARAM_STR);
		 $check213->bindParam(':pharmacy', $org_listss['pharmacy'], PDO::PARAM_STR);
		 $check213->bindParam(':reg', $org_listss['reg'], PDO::PARAM_STR);
		 $check213->bindParam(':total', $total, PDO::PARAM_STR);
		$check213->bindParam(':branchcodes', $branchcode, PDO::PARAM_STR);
		 $total=round(($org_listss['revenue']+$org_listss['op']+$org_listss['pharmacy']+$org_listss['reg']),2);
         $check213 -> execute();
		
	}
	
		
	
    $org_list = $pdoread -> prepare("SELECT Round(SUM(`total`),2) AS daytotal,Round(SUM(`revenue`),2) AS revenuetotal,Round(SUM(`op`),2) AS optotal,Round(SUM(`pharmacy`),2) AS pharmacytotal,Round(SUM(`reg`),2) AS regtotal,(SELECT Round(SUM(`revenue`),2) AS count FROM `revenue_breakup` WHERE MONTH(`date`)=MONTH(:fdate) AND `cost_center`=:branchcode) AS revenuemtd,(SELECT Round(SUM(`op`),2) AS count FROM `revenue_breakup` WHERE MONTH(`date`)=MONTH(:fdate) AND `cost_center`=:branchcode) AS opmtd ,(SELECT Round(SUM(`pharmacy`),2) AS count FROM `revenue_breakup` WHERE MONTH(`date`)=MONTH(:fdate) AND `cost_center`=:branchcode) AS pharmacymtd ,(SELECT Round(SUM(`reg`),2) AS count FROM `revenue_breakup` WHERE MONTH(`date`)=MONTH(:fdate) AND `cost_center`=:branchcode) AS regmtd,(SELECT Round(SUM(`total`),2) AS count FROM `revenue_breakup` WHERE MONTH(`date`)=MONTH(:fdate) AND `cost_center`=:branchcode) AS totalmtd   FROM `revenue_breakup`  WHERE Date(date) BETWEEN :fdate AND :tdate AND `cost_center`=:branchcode");
	
	$org_list->bindParam(':branchcode', $branchcode, PDO::PARAM_STR);
	$org_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$org_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$org_list -> execute();
	$finaltotal=0;
	$finallasttotal=0;
	if($org_list -> rowCount() > 0){
		$org_lists = $org_list->fetch(PDO::FETCH_ASSOC);
	$my_arrayname = array("IP Revenue","OP Revenue","OP Pharmacy Revenue","Registration Revenue");	
	$my_arrayvalue = array($org_lists['revenuetotal'],$org_lists['optotal'],$org_lists['pharmacytotal'],$org_lists['regtotal']);	
	$my_arraymtd = array($org_lists['revenuemtd'],$org_lists['opmtd'],$org_lists['pharmacymtd'],$org_lists['regmtd']);	
		
		

	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	 for($x = 0; $x < sizeof($my_arrayname); $x++){	
	
		if($type=="Thousand"){
		$count=round(($my_arrayvalue[$x]/1000),2);
		$count1=round(($my_arraymtd[$x]/1000),2);
		}else if($type=="Lacs"){
		$count=round(($my_arrayvalue[$x]/100000),2);
		$count1=round(($my_arraymtd[$x]/100000),2);
		}else if($type=="Million"){
		$count=round(($my_arrayvalue[$x]/1000000),2);
		$count1=round(($my_arraymtd[$x]/1000000),2);
		}else if($type=="Crores"){
		$count=round(($my_arrayvalue[$x]/10000000),2);	
		$count1=round(($my_arraymtd[$x]/10000000),2);	
		}
		
		
		$temp=[
		"name"=>$my_arrayname[$x],
		"total"=>strval($count),
		"mtdtotal"=>strval($count1),
		
		];
		
		array_push($response1,$temp);
	$finaltotal=$finaltotal+$count;
	$finallasttotal=$finallasttotal+$count1;
	
	
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
	
	$response['totlacount'] = round($finaltotal,2);
	$response['mtdcount'] = round($finallasttotal,2);
	$response['totalname'] ="Total";
	$response['rbreaklist'] = $response1;
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
   unset($pdo4);
?>