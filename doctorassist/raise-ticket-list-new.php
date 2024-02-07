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
$accesskey=trim($data->accesskey);
/* $status=trim($data->status); */
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 

$response = array();
try{

 if(!empty($accesskey)){
$validate = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();

if($validate -> rowCount() > 0){
	$row = $validate->fetch();
	  
 
    $check2 = $pdoread->prepare("SELECT doctor_raise_ticket.`sno`, doctor_raise_ticket.`type`, doctor_raise_ticket.`ticket_id`, doctor_raise_ticket.`category`, doctor_raise_ticket.`issues`,if(doctor_raise_ticket.`issues`='Any other Suggestions',doctor_raise_ticket.`issue_details`,'') as issue_remarks, doctor_raise_ticket.`issue_details`,doctor_raise_ticket.`remarks`,doctor_raise_ticket.`photo`, case when doctor_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') else concat(:baseurl,'appicons/solve.png') end  as icon , CASE WHEN doctor_raise_ticket.`status`='Pending' THEN '#F1732E' ELSE '#419505' END AS color ,issue_logs.`created_by`, date_format(doctor_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , doctor_raise_ticket.`modified_by`, doctor_raise_ticket.`modified_on`, if(doctor_raise_ticket.`photo`='Please select an image file to upload.' OR doctor_raise_ticket.`photo`='Yes' or doctor_raise_ticket.`photo`='Invalid image file'  or doctor_raise_ticket.`photo`='','',concat(:baseurl,doctor_raise_ticket.`photo`)) as image ,doctor_raise_ticket.assigned_person,(SELECT `status` FROM `issue_logs` WHERE `issue_sno`=doctor_raise_ticket.`sno` order by `sno` desc LIMIT 1) AS status,branch,branch_master.display_name as branchname FROM `doctor_raise_ticket` inner join issue_logs ON issue_logs.issue_sno=doctor_raise_ticket.sno inner join branch_master on doctor_raise_ticket.branch=branch_master.cost_center WHERE  doctor_raise_ticket.user_id=:userid AND DATE(doctor_raise_ticket.created_on) between :fdate and :tdate group by issue_logs.issue_sno order by issue_logs.`sno` DESC");
	// $check2->bindParam(':category', $category, PDO::PARAM_STR);
	$check2->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$check2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$check2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$check2->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
/* 	$check2->bindValue(':status', "%{$status}%", PDO::PARAM_STR); */
	
	$check2 -> execute();
    if($check2->rowCount() > 0){
		$revenue_list = $pdoread -> prepare("SELECT (SELECT COUNT(*) FROM `doctor_raise_ticket` where user_id=:user_id AND status!='' and date(created_on) between :fdate  and :tdate) as totalcount,
(select COUNT(*) FROM doctor_raise_ticket where status NOT LIKE '%Resolve%' or status NOT LIKE '%Reject%' and user_id=:user_id and date(created_on) between :fdate  and :tdate) as pending,(select COUNT(*) FROM doctor_raise_ticket where status  LIKE '%Resolve%' and status  LIKE '%Reject%' and  user_id=:user_id and date(created_on) between :fdate  and :tdate) as solved");
		$revenue_list->bindParam(':user_id', $row['userid'], PDO::PARAM_STR);
		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	    $revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	     //$revenue_list->bindParam(':branch', $result1['cost_center'],PDO::PARAM_STR);
   	     $revenue_list->execute();
	
	     $revenue_data = $revenue_list->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";	
	$response['raisedcount']= $revenue_data['totalcount'];
	$response['openedcount']= $revenue_data['pending'];
	$response['solvedcount']= $revenue_data['solved'];
			
        while($check2list = $check2->fetch(PDO::FETCH_ASSOC)){
		
		$response['raiseissuelist'][] = [
					'sno'=>$check2list['sno'],
					'issuename'=>$check2list['issues'],
					'issue_remarks'=>$check2list['issue_remarks'],
					'ticket_id'=>$check2list['ticket_id'],
					'Status'=>$check2list['status'],
					'icon'=>$check2list['icon'],
					'statuscolour'=>$check2list['color'],
					'raisedon'=>$check2list['created_on'],
					'department'=>$check2list['category'],
					'type'=>$check2list['type'],
					'imageicon'=>$check2list['image'],
					'imagecolour'=>'#00ACC1',
					'imagetext'=>'Show Attachment',
					'trackicon'=>$check2list['image'],
					'trackcolour'=>'#419505',
					'tracktext'=>'Track',
					'branch'=>$check2list['branch'],
					'branchname'=>$check2list['branchname'],
					'remarks'=>$check2list['remarks'],
				];
	}
}else{
	http_response_code(503);
      $response['error'] = true;
	 $response['message']="No data found";
}
    	}else{
			http_response_code(400);
		    $response['error'] = true;
			$response['message']="Access denied!";
		}  
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdoread = null;
?>