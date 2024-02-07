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
$accesskey = trim($data->accesskey);
$fromdate = date_format(date_create(trim($data->fromdate)),"Y-m-d");
$todate = date_format(date_create(trim($data->todate)),"Y-m-d");
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT E.created_by,`user_logins`.`username`,ROUND(SUM(E.grossamt),2) AS grossamt,ROUND(SUM(E.ConcAmt),2) AS ConcAmt,ROUND(SUM(E.netvalue),2) AS netvalue,ROUND(SUM(E.receiptamt),2) AS receiptamt,ROUND(SUM(E.balanceamt),2) AS balanceamt,SUM(E.paymenttype) AS paymenttype,ROUND(SUM(E.cash),2) AS cash,ROUND(SUM(E.cheque),2) AS cheque,ROUND(SUM(E.card),2) AS card,ROUND(SUM(E.upi),2) AS upi,ROUND(SUM(E.dueamt),2) AS dueamt,E.cost_center FROM ((SELECT 'OP BILLING' AS bill_type,`created_by`,DATE_FORMAT(`op_billing_generate`.`created_on`,'%d-%b-%Y %H:%m:%s') AS INVDATE,`op_billing_generate`.`inv_no` AS sonumber,`op_billing_generate`.`invoice_no`,`original_value` AS grossamt,(`itemwise_disc`+`discount_val`) AS ConcAmt,`after_val` AS netvalue,`receivedamt` AS receiptamt,`returnamt` AS balanceamt,`paymenttype`,(CASE WHEN TRIM(`payment_history`.`paymentmode`) = 'CASH' AND `op_billing_generate`.`paymenttype` = 'Paid' THEN ROUND(SUM(`payment_history`.`total`),2) ELSE '0.00' END) AS cash,(CASE WHEN `payment_history`.`paymentmode` = 'CHEQUE' AND `op_billing_generate`.`paymenttype` = 'Paid' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS cheque,(CASE WHEN `payment_history`.`paymentmode` = 'CARD' AND `op_billing_generate`.`paymenttype` = 'Paid' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS card,(CASE WHEN `payment_history`.`paymentmode` IN ('UPI/NETBANKING','UPI') AND `op_billing_generate`.`paymenttype` = 'Paid' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS upi,(CASE WHEN `op_billing_generate`.`paymenttype` = 'Credit' THEN ROUND(SUM(`after_val`),2) ELSE '0.00' END) AS dueamt,`op_billing_generate`.`cost_center` FROM `op_billing_generate` LEFT JOIN `payment_history` ON `op_billing_generate`.`inv_no` = `payment_history`.`billno` WHERE `op_billing_generate`.`cost_center` = :cost_center AND `op_billing_generate`.`status` ='Confirmed' AND DATE(`op_billing_generate`.`created_on`) BETWEEN :fromdate AND :todate GROUP BY `op_billing_generate`.`inv_no`) UNION ALL (SELECT `payment_history`.`bill_type`,`createdby`,DATE_FORMAT(`createdon`,'%d-%b-%Y %H:%m:%s') AS INVDATE,`billno` AS sonumber,`admissionon` AS invoice_no,`amount` AS grossamt,`discount_value` AS ConcAmt,`total` AS netvalue,`total` AS receiptamt,0 AS balanceamt,(CASE WHEN `paymentmode` = 'ORGANIZATION DUE' THEN 'Credit' ELSE 'Paid' END) AS paymenttype,(CASE WHEN TRIM(`paymentmode`) = 'CASH' THEN ROUND(SUM(`payment_history`.`total`),2) ELSE '0.00' END) AS cash,(CASE WHEN `paymentmode` = 'CHEQUE' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS cheque,(CASE WHEN `paymentmode` = 'CARD' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS card,(CASE WHEN `paymentmode` IN ('UPI/NETBANKING','UPI') THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS upi,(CASE WHEN `paymentmode` = 'ORGANIZATION DUE' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS dueamt,`payment_history`.`cost_center` AS cost_center FROM `payment_history` WHERE DATE(`createdon`) BETWEEN :fromdate AND :todate AND `bill_type` = 'IP ADMISSION' AND `status` = 'Visible' AND `credit_debit` = 'CREDIT' AND `cost_center` = :cost_center GROUP BY `receiptno`) UNION ALL (SELECT `payment_history`.`bill_type`,`payment_history`.`createdby`,DATE_FORMAT(`payment_history`.`createdon`,'%d-%b-%Y %H:%m:%s') AS INVDATE,`payment_history`.`billno` AS sonumber,`payment_history`.`admissionon` AS invoice_no,`payment_history`.`amount` AS grossamt,`payment_history`.`discount_value` AS ConcAmt,`payment_history`.`total` AS netvalue,`payment_history`.`total` AS receiptamt,0 AS balanceamt,(CASE WHEN `payment_history`.`paymentmode` = 'ORGANIZATION DUE' THEN 'Credit' ELSE 'Paid' END) AS paymenttype,(CASE WHEN TRIM(`payment_history`.`paymentmode`) = 'CASH' THEN ROUND(SUM(`payment_history`.`total`),2) ELSE '0.00' END) AS cash,(CASE WHEN `payment_history`.`paymentmode` = 'CHEQUE' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS cheque,(CASE WHEN `payment_history`.`paymentmode` = 'CARD' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS card,(CASE WHEN `payment_history`.`paymentmode` IN ('UPI/NETBANKING','UPI') THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS upi,(CASE WHEN `payment_history`.`paymentmode` = 'ORGANIZATION DUE' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS dueamt,`op_pharmacy_inv_generate`.`cost_center` AS cost_center FROM `payment_history` LEFT JOIN `op_pharmacy_inv_generate` ON `op_pharmacy_inv_generate`.`inv_no` = `payment_history`.`billno` WHERE DATE(`payment_history`.`createdon`) BETWEEN :fromdate AND :todate AND `payment_history`.`bill_type` = 'OP-PHARMACY' AND `payment_history`.`status` = 'Visible' AND `payment_history`.`credit_debit` = 'CREDIT' AND `op_pharmacy_inv_generate`.`cost_center` = :cost_center AND `op_pharmacy_inv_generate`.`status` = 'Completed' GROUP BY `op_pharmacy_inv_generate`.`inv_no`) UNION ALL (SELECT `payment_history`.`bill_type`,`payment_history`.`createdby`,DATE_FORMAT(`payment_history`.`createdon`,'%d-%b-%Y %H:%m:%s') AS INVDATE,`payment_history`.`billno` AS sonumber,`payment_history`.`admissionon` AS invoice_no,`payment_history`.`amount` AS grossamt,`payment_history`.`discount_value` AS ConcAmt,`payment_history`.`total` AS netvalue,`payment_history`.`total` AS receiptamt,0 AS balanceamt,(CASE WHEN `payment_history`.`paymentmode` = 'ORGANIZATION DUE' THEN 'Credit' ELSE 'Paid' END) AS paymenttype,(CASE WHEN TRIM(`payment_history`.`paymentmode`) = 'CASH' THEN ROUND(SUM(`payment_history`.`total`),2) ELSE '0.00' END) AS cash,(CASE WHEN `payment_history`.`paymentmode` = 'CHEQUE' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS cheque,(CASE WHEN `payment_history`.`paymentmode` = 'CARD' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS card,(CASE WHEN `payment_history`.`paymentmode` IN ('UPI/NETBANKING','UPI') THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS upi,(CASE WHEN `payment_history`.`paymentmode` = 'ORGANIZATION DUE' THEN ROUND(SUM(`payment_history`.`amount`),2) ELSE '0.00' END) AS dueamt,`umr_registration`.`branch` AS cost_center FROM `payment_history` LEFT JOIN `umr_registration` ON `umr_registration`.`umrno` = `payment_history`.`admissionon` WHERE DATE(`payment_history`.`createdon`) BETWEEN :fromdate AND :todate AND `payment_history`.`bill_type` = 'registration' AND `payment_history`.`status` = 'Visible' AND `payment_history`.`credit_debit` = 'CREDIT' AND `umr_registration`.`branch` = :cost_center AND `umr_registration`.`status` = 'Visible' GROUP BY `umr_registration`.`umrno`)) AS E LEFT JOIN `user_logins` ON `user_logins`.`userid` = E.created_by GROUP BY E.created_by ASC");

$reglist->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
	$reglist->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
	$reglist->bindParam(':todate', $todate, PDO::PARAM_STR);

$reglist -> execute();
if($reglist -> rowCount() > 0){
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	$response['userdetails']= "User Details";
	$response['gross']= "Gross";
	$response['disc']= "Disc";
	$response['net']= "Net";
	$response['cash']= "Cash";
	$response['card']= "Card";
	$response['upi']= "UPI";
	$response['cheque']= "Cheque";
	$response['due']= "Due";
	$response['total']= "Total";
	while($listwise = $reglist->fetch(PDO::FETCH_ASSOC)){
			$response['userwisecashcollectionlist'][] = $listwise;
		}
}else{
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>