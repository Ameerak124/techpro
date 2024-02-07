<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$branch = $data->branch;
$status = $data->status;
$response = array();
$response1 = array();
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` ,`cost_center`,`role`,if(cost_center=:branch,'Yes','No') as visiblestatus FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> bindParam(":branch", $branch, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
 $result = $check->fetch(PDO::FETCH_ASSOC);
 if($result['role']=='Center Head'){
 if($branch=='All'){
if($status=='Pending'){

	
 $stmt1=$pdoread->prepare("SELECT@a:=@a+1 serial_number,patient_details.patient_name,mysupporters_mapping.sno as id, `unique_id`, `billno`, mysupporters_mapping.`umrno`, mysupporters_mapping.`invoice_no`, mysupporters_mapping.`trans_id`, date_format(`created_on`,'%d-%b-%Y %h:%i:%s') as created_on, `created_by`, if(ch_on ='0000-00-00 00:00','',date_format(`ch_on`,'%d-%b-%Y %h:%i:%s')) as chapprovaldate, `ch_by`, `ch_status` as approvalstatus, `status` as mappingstatus FROM (SELECT @a:=0) AS a,  `mysupporters_mapping` inner join patient_details on patient_details.transid=mysupporters_mapping.trans_id  WHERE  `ch_status`='Pending' AND `mysupporters_mapping`.`status`='Pending' and   date(mysupporters_mapping.`created_on`) BETWEEN :fromdate AND :todate ORDER BY `mysupporters_mapping`.`sno` DESC;");
 $stmt1 -> bindParam(":fromdate", $fdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":todate", $tdate, PDO::PARAM_STR);
$stmt1 -> execute();


 }else if ($status=='Approved'){
		 
	  $stmt1=$pdoread->prepare("SELECT@a:=@a+1 serial_number,patient_details.patient_name,mysupporters_mapping.sno as id, `unique_id`, `billno`, mysupporters_mapping.`umrno`, mysupporters_mapping.`invoice_no`, mysupporters_mapping.`trans_id`,date_format(`created_on`,'%d-%b-%Y %h:%i:%s') as created_on, `created_by`, if(ch_on ='0000-00-00 00:00','',date_format(`ch_on`,'%d-%b-%Y %h:%i:%s')) as chapprovaldate, `ch_by`, `ch_status` as approvalstatus, `status` as mappingstatus FROM (SELECT @a:=0) AS a,  `mysupporters_mapping` inner join patient_details on patient_details.transid=mysupporters_mapping.trans_id  WHERE  `ch_status`='Approved' AND `mysupporters_mapping`.`status`='Pending' and   date(mysupporters_mapping.`created_on`) BETWEEN :fromdate AND :todate ORDER BY `mysupporters_mapping`.`sno` DESC");
	  
$stmt1 -> bindParam(":fromdate", $fdate, PDO::PARAM_STR);
$stmt1 -> bindParam(":todate", $tdate, PDO::PARAM_STR);
$stmt1 -> execute();
		 		 
 }else if($status=='Rejected'){	
	  $stmt1=$pdoread->prepare("SELECT@a:=@a+1 serial_number,patient_details.patient_name,mysupporters_mapping.sno as id, `unique_id`, `billno`, mysupporters_mapping.`umrno`, mysupporters_mapping.`invoice_no`, mysupporters_mapping.`trans_id`, date_format(`created_on`,'%d-%b-%Y %h:%i:%s') as created_on, `created_by`, if(ch_on ='0000-00-00 00:00','',date_format(`ch_on`,'%d-%b-%Y %h:%i:%s')) as chapprovaldate, `ch_by`, `ch_status` as approvalstatus, `status` as mappingstatus FROM (SELECT @a:=0) AS a,  `mysupporters_mapping` inner join patient_details on patient_details.transid=mysupporters_mapping.trans_id  WHERE  `ch_status`='Rejected' AND `mysupporters_mapping`.`status`='Pending' and   date(mysupporters_mapping.`created_on`) BETWEEN :fromdate AND :todate ORDER BY `mysupporters_mapping`.`sno` DESC");
	  
$stmt1 -> bindParam(":fromdate", $fdate, PDO::PARAM_STR);
$stmt1 -> bindParam(":todate", $tdate, PDO::PARAM_STR);
$stmt1 -> execute();
 }
 }else{
	 if($status=='Pending'){
 
	 
 $stmt1=$pdoread->prepare("SELECT@a:=@a+1 serial_number,patient_details.patient_name,mysupporters_mapping.sno as id, `unique_id`, `billno`, mysupporters_mapping.`umrno`, mysupporters_mapping.`invoice_no`, mysupporters_mapping.`trans_id`, date_format(`created_on`,'%d-%b-%Y %h:%i:%s') as created_on, `created_by`, if(ch_on ='0000-00-00 00:00','',date_format(`ch_on`,'%d-%b-%Y %h:%i:%s')) as chapprovaldate, `ch_by`, `ch_status` as approvalstatus, `status` as mappingstatus FROM (SELECT @a:=0) AS a,  `mysupporters_mapping` inner join patient_details on patient_details.transid=mysupporters_mapping.trans_id  WHERE  `ch_status`='Pending' AND `mysupporters_mapping`.`status`='Pending' and   date(mysupporters_mapping.`created_on`) BETWEEN :fromdate AND :todate and mysupporters_mapping.branch=:costcenter ORDER BY `mysupporters_mapping`.`sno` DESC");
 
 $stmt1 -> bindParam(":fromdate", $fdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":todate", $tdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":costcenter", $branch, PDO::PARAM_STR);
$stmt1 -> execute();

 
 }else if ($status=='Approved'){
	  	 
 $stmt1=$pdoread->prepare("SELECT@a:=@a+1 serial_number,patient_details.patient_name,mysupporters_mapping.sno as id, `unique_id`, `billno`, mysupporters_mapping.`umrno`, mysupporters_mapping.`invoice_no`, mysupporters_mapping.`trans_id`,date_format(`created_on`,'%d-%b-%Y %h:%i:%s') as created_on, `created_by`, if(ch_on ='0000-00-00 00:00','',date_format(`ch_on`,'%d-%b-%Y %h:%i:%s')) as chapprovaldate, `ch_by`, `ch_status` as approvalstatus, `status` as mappingstatus FROM (SELECT @a:=0) AS a,  `mysupporters_mapping` inner join patient_details on patient_details.transid=mysupporters_mapping.trans_id  WHERE  `ch_status`='Approved' AND `mysupporters_mapping`.`status`='Pending' and   date(mysupporters_mapping.`created_on`) BETWEEN :fromdate AND :todate and mysupporters_mapping.branch=:costcenter ORDER BY `mysupporters_mapping`.`sno` DESC");
 $stmt1 -> bindParam(":fromdate", $fdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":todate", $tdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":costcenter", $branch, PDO::PARAM_STR);
$stmt1 -> execute();



 }else if($status=='Rejected'){
	 
	
		 
 $stmt1=$pdoread->prepare("SELECT@a:=@a+1 serial_number,patient_details.patient_name,mysupporters_mapping.sno as id, `unique_id`, `billno`, mysupporters_mapping.`umrno`, mysupporters_mapping.`invoice_no`, mysupporters_mapping.`trans_id`, date_format(`created_on`,'%d-%b-%Y %h:%i:%s') as created_on, `created_by`, if(ch_on ='0000-00-00 00:00','',date_format(`ch_on`,'%d-%b-%Y %h:%i:%s')) as chapprovaldate, `ch_by`, `ch_status` as approvalstatus, `status` as mappingstatus FROM (SELECT @a:=0) AS a,  `mysupporters_mapping` inner join patient_details on patient_details.transid=mysupporters_mapping.trans_id  WHERE  `ch_status`='Rejected' AND `mysupporters_mapping`.`status`='Pending' and   date(mysupporters_mapping.`created_on`) BETWEEN :fromdate AND :todate and mysupporters_mapping.branch=:costcenter ORDER BY `mysupporters_mapping`.`sno` DESC");
 $stmt1 -> bindParam(":fromdate", $fdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":todate", $tdate, PDO::PARAM_STR);
 $stmt1 -> bindParam(":costcenter", $branch, PDO::PARAM_STR);
$stmt1 -> execute();

	
 }
 
 }
 
 
 
if($stmt1 -> rowCount() > 0){

http_response_code(200);
$response['error'] = false;
$response['message']="Data found";	
$response['remarks']="Remarks";	
$response['billremarks']='';	
if($result['role']=='CFO'){
	$response['visiblestatus']="Yes";
}else{
$response['visiblestatus']=$result['visiblestatus'];
}	
while($list = $stmt1 -> fetch(PDO::FETCH_ASSOC)){
$temp=[
"id"=>$list['id'],
"billno"=>$list['billno'],
"patient_name"=>$list['patient_name'],
"udid"=>$list['umrno'],
"unique_id"=>$list['unique_id'],
"invoice_no"=>$list['invoice_no'],
"trans_id"=>$list['trans_id'],
"chapprovaldate"=>$list['chapprovaldate'],
"ch_by"=>$list['ch_by'],
"approvalstatus"=>$list['approvalstatus'],
"mapstatus"=>$list['mappingstatus'],
"createdon"=>$list['created_on'],
"createdby"=>$list['created_by'],
];
array_push($response1,$temp);
}
$response['billno']= 'Bill No';
$response['udid']= 'UDID';
$response['discounttype']= 'Discount Type';
$response['discountTitle']= 'Discount';
$response['netamount']= 'Net Amount';
$response['paymentmodetitle']= 'Payment Mode';
$response['raiseddate']= 'Raised Date';
$response['billvalue']= 'Bill Amount';
$response['billingdiscountlist']= $response1;

}else{
	 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
}
 }else{
	
    http_response_code(503);
	$response['error']= true;
	$response['message']="Unauthorized Access!";
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