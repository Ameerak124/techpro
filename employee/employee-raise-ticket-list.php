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
$status=trim($data->status);
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 

$response = array();
try{

 if(!empty($accesskey)){
$validate = $pdo -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();

if($validate -> rowCount() > 0){
	$row = $validate->fetch();
	  
 
    $check2 = $pdo->prepare("SELECT employee_raise_ticket.`sno`, employee_raise_ticket.`type`, employee_raise_ticket.`ticket_id`, employee_raise_ticket.`category`, employee_raise_ticket.`issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, employee_raise_ticket.`issue_details`,employee_raise_ticket.`remarks`,employee_raise_ticket.`photo`, case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') WHEN employee_raise_ticket.`status` like '%Rejected%' THEN concat(:baseurl,'appicons/rejected.png')  else concat(:baseurl,'appicons/accepted.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' WHEN employee_raise_ticket.`status` like '%Rejected%' THEN '#fc033d' ELSE '#419505' END AS color ,employee_issue_logs.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(employee_raise_ticket.`photo`='Please select an image file to upload.' OR employee_raise_ticket.`photo`='Yes' or employee_raise_ticket.`photo`='Invalid image file'  or employee_raise_ticket.`photo`='','',concat(:baseurl,employee_raise_ticket.`photo`)) as image ,employee_raise_ticket.assigned_person,(SELECT `status` FROM `employee_issue_logs` WHERE `issue_sno`=employee_raise_ticket.`sno`  and  status like :status order by `sno` desc LIMIT 1) AS status,branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join employee_issue_logs ON employee_issue_logs.issue_sno=employee_raise_ticket.sno inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE  employee_raise_ticket.created_by like :userid and employee_raise_ticket.status like :status  AND DATE(employee_raise_ticket.created_on) between :fdate and :tdate group by employee_issue_logs.issue_sno order by employee_issue_logs.`sno` DESC");
	// $check2->bindParam(':category', $category, PDO::PARAM_STR);
	$check2->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$check2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$check2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$check2->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$check2->bindValue(':status', "%{$status}%", PDO::PARAM_STR);
	
	$check2 -> execute();
    if($check2->rowCount() > 0){
		$employee_list = $pdo -> prepare("SELECT (SELECT COUNT(*) FROM `employee_raise_ticket` where user_id=:user_id AND status!='' and date(created_on) between :fdate  and :tdate) as totalcount,
		(select COUNT(*) FROM employee_raise_ticket where status NOT LIKE '%Resolve%' or status NOT LIKE '%Reject%' and user_id=:user_id and date(created_on) between :fdate  and :tdate) as pending,(select COUNT(*) FROM employee_raise_ticket where status  LIKE '%Resolve%' and status  LIKE '%Reject%' and  user_id=:user_id and date(created_on) between :fdate  and :tdate) as solved");
		$employee_list->bindParam(':user_id', $row['userid'], PDO::PARAM_STR);
		$employee_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	    $employee_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	     //$revenue_list->bindParam(':branch', $result1['cost_center'],PDO::PARAM_STR);
   	     $employee_list->execute();
	
	     $employee_data = $employee_list->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";	
	$response['raisedcount']= $employee_data['totalcount'];
	$response['openedcount']= $employee_data['pending'];
	$response['solvedcount']= $employee_data['solved'];
			
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
$pdo = null;
?>