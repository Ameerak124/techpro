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
$status = $data->status;
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$response = array();
$responsebutton = array();
try{
if(!empty($accesskey)){
	$accesscheck ="SELECT `userid`,`branch`,`cost_center`,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdo->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
	$row = $stmt->fetch();
	$language =TRIM($row['language']);
		
     /*  $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `issue_level_designations` WHERE FIND_IN_SET(:role,`CL`)<>0 LIMIT 1";
	$stmt12 = $pdo->prepare($displaybutton);
	$stmt12->bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt12->execute();
	$rows = $stmt12->fetch(); */

	if($status=="Pending"){
	$displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `issue_level_designations` WHERE PL IN ('Sub unit head','Sub Corporate head','sub Director Operations','sub ED')  AND FIND_IN_SET(:role,`PL`)<>0 LIMIT 1";
     $stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
     $rows = $stmt12->fetch();	
		          //$button1=$rows['assign_button'];
		          $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2=$rows['textescalate'];
				$buttontext3=$rows['textreject'];
				$buttontext4=$rows['textresolve'];
				$buttontext5=$rows['textprocessing'];
	}else{
		         //$button1=$rows['assign_button'];
	               $button1="No";
				$button2='No';
				$button3='No';
				$button4='No';
				$button5='No';
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2='No';
				$buttonbg3='No';
				$buttonbg4='No';
				$buttonbg5='No';
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2='No';
				$buttontext3='No';
				$buttontext4='No';
				$buttontext5='No';
	}
	$stmt1 = $pdo->prepare("SELECT employee_raise_ticket.`sno`,employee_raise_ticket.`type`, employee_raise_ticket.`ticket_id`, employee_raise_ticket.`category`, employee_raise_ticket.`issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, employee_raise_ticket.`issue_details`, employee_raise_ticket.`remarks`, employee_raise_ticket.`photo`, case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') else concat(:baseurl,'appicons/solve.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' ELSE '#419505' END AS color ,employee_raise_ticket.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(employee_raise_ticket.`photo`='Please select an image file to upload.' OR employee_raise_ticket.`photo`='Yes' or employee_raise_ticket.`photo`='Invalid image file'  or employee_raise_ticket.`photo`='','',concat(:baseurl,employee_raise_ticket.`photo`)) as image ,employee_raise_ticket.status ,employee_raise_ticket.assigned_person,employee_raise_ticket.branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE  employee_raise_ticket.assigned_person  like :empid and employee_raise_ticket.`status` like :status AND date(employee_raise_ticket.created_on) between :fdate and :tdate group by employee_raise_ticket.`sno`  order by employee_raise_ticket.`sno` DESC");
	$stmt1->bindValue(":empid", "%{$row['userid']}%", PDO::PARAM_STR);
	$stmt1->bindValue(":status", "%{$status}%", PDO::PARAM_STR);
	$stmt1->bindValue(":role", "%{$row['role']}%", PDO::PARAM_STR);
	$stmt1->bindparam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt1->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$stmt1->bindParam(':tdate', $tdate, PDO::PARAM_STR);
     $stmt1->execute();
 	}elseif($status=="Escalated" || $status=="Assigned"){
			
	 $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `issue_level_designations` WHERE PL IN ('Sub unit head','Sub Corporate head','sub Director Operations','sub ED') AND FIND_IN_SET(:role,`PL`)<>0 LIMIT 1";
     $stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
     $rows = $stmt12->fetch();	
 		          //$button1=$rows['assign_button'];
		          $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2=$rows['textescalate'];
				$buttontext3=$rows['textreject'];
				$buttontext4=$rows['textresolve'];
				$buttontext5=$rows['textprocessing'];
	}else{
		          //$button1=$rows['assign_button'];
	               $button1="No";
				$button2='No';
				$button3='No';
				$button4='No';
				$button5='No';
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2='No';
				$buttonbg3='No';
				$buttonbg4='No';
				$buttonbg5='No';
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2='No';
				$buttontext3='No';
				$buttontext4='No';
				$buttontext5='No';
	}
	$stmt1 = $pdo->prepare("SELECT employee_raise_ticket.`sno`, employee_raise_ticket.`type`, employee_raise_ticket.`ticket_id`, employee_raise_ticket.`category`, employee_raise_ticket.`issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, employee_raise_ticket.`issue_details`,employee_raise_ticket.`remarks`,employee_raise_ticket.`photo`, case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') else concat(:baseurl,'appicons/solve.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' ELSE '#419505' END AS color ,employee_issue_logs.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(employee_raise_ticket.`photo`='Please select an image file to upload.' OR employee_raise_ticket.`photo`='Yes' or employee_raise_ticket.`photo`='Invalid image file'  or employee_raise_ticket.`photo`='','',concat(:baseurl,employee_raise_ticket.`photo`)) as image ,employee_issue_logs.`status`,employee_raise_ticket.assigned_person,branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join employee_issue_logs ON employee_issue_logs.issue_sno=employee_raise_ticket.sno inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE  employee_issue_logs.created_by like :empid  and employee_issue_logs.`status` like (:status '' :role)  AND date(employee_raise_ticket.created_on) between :fdate and :tdate group by employee_issue_logs.issue_sno order by employee_issue_logs.`sno` DESC");
	$stmt1->bindValue(":empid", "%{$row['userid']}%", PDO::PARAM_STR);
	$stmt1->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$stmt1->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$stmt1->bindValue(":status", "%{$status}%", PDO::PARAM_STR);
	$stmt1->bindValue(":role", "%{$row['role']}%", PDO::PARAM_STR);
	$stmt1->bindparam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt1->execute();
	}elseif($status=="Rejected" || $status=="Resolved"){
			
	$displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `issue_level_designations` WHERE PL IN ('Sub unit head','Sub Corporate head','sub Director Operations','sub ED') AND FIND_IN_SET(:role,`PL`)<>0 LIMIT 1";
     $stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
     $rows = $stmt12->fetch();	
		          //$button1=$rows['assign_button'];
		          $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2=$rows['textescalate'];
				$buttontext3=$rows['textreject'];
				$buttontext4=$rows['textresolve'];
				$buttontext5=$rows['textprocessing'];
	}else{
		         //$button1=$rows['assign_button'];
	               $button1="No";
				$button2='No';
				$button3='No';
				$button4='No';
				$button5='No';
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2='No';
				$buttonbg3='No';
				$buttonbg4='No';
				$buttonbg5='No';
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2='No';
				$buttontext3='No';
				$buttontext4='No';
				$buttontext5='No';
	}
	$stmt1 = $pdo->prepare("SELECT employee_raise_ticket.`sno`, employee_raise_ticket.`type`, employee_raise_ticket.`ticket_id`, employee_raise_ticket.`category`, employee_raise_ticket.`issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, employee_raise_ticket.`issue_details`,employee_raise_ticket.`remarks`,employee_raise_ticket.`photo`, case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') else concat(:baseurl,'appicons/solve.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' ELSE '#419505' END AS color ,employee_issue_logs.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(employee_raise_ticket.`photo`='Please select an image file to upload.' OR employee_raise_ticket.`photo`='Yes' or employee_raise_ticket.`photo`='Invalid image file'  or employee_raise_ticket.`photo`='','',concat(:baseurl,employee_raise_ticket.`photo`)) as image ,employee_issue_logs.`status`,employee_raise_ticket.assigned_person,branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join employee_issue_logs ON employee_issue_logs.issue_sno=employee_raise_ticket.sno inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE  employee_issue_logs.created_by like :empid  and employee_issue_logs.`status` like (:status '' :role)  AND date(employee_raise_ticket.created_on) between :fdate and :tdate group by employee_issue_logs.issue_sno order by employee_issue_logs.`sno` DESC");
	$stmt1->bindValue(":empid", "%{$row['userid']}%", PDO::PARAM_STR);
	$stmt1->bindValue(":status", "%{$status}%", PDO::PARAM_STR);
	$stmt1->bindValue(":role", "%{$row['role']}%", PDO::PARAM_STR);
	$stmt1->bindparam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt1->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$stmt1->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$stmt1->execute();		
	}elseif($status=="Inprocessing"){
	$displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `issue_level_designations` WHERE PL IN ('Sub unit head','Sub Corporate head','sub Director Operations','sub ED') AND FIND_IN_SET(:role,`PL`)<>0 LIMIT 1";
     $stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $row['role'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
     $rows = $stmt12->fetch();	
		          //$button1=$rows['assign_button'];
		          $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2=$rows['textescalate'];
				$buttontext3=$rows['textreject'];
				$buttontext4=$rows['textresolve'];
				$buttontext5=$rows['textprocessing'];
	}else{
		          //$button1=$rows['assign_button'];
	               $button1="No";
				$button2='No';
				$button3='No';
				$button4='No';
				$button5='No';
				//$buttonbg1=$rows['bgassign'];
				$buttonbg1="No";
				$buttonbg2='No';
				$buttonbg3='No';
				$buttonbg4='No';
				$buttonbg5='No';
				//$buttontext1=$rows['textassign'];
				$buttontext1="No";
				$buttontext2='No';
				$buttontext3='No';
				$buttontext4='No';
				$buttontext5='No';
	}
	
	$stmt1 = $pdo->prepare("SELECT employee_raise_ticket.`sno`, employee_raise_ticket.`type`, employee_raise_ticket.`ticket_id`, employee_raise_ticket.`category`, employee_raise_ticket.`issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, employee_raise_ticket.`issue_details`,employee_raise_ticket.`remarks`,employee_raise_ticket.`photo`, case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') else concat(:baseurl,'appicons/solve.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' ELSE '#419505' END AS color ,employee_issue_logs.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(employee_raise_ticket.`photo`='Please select an image file to upload.' OR employee_raise_ticket.`photo`='Yes' or employee_raise_ticket.`photo`='Invalid image file'  or employee_raise_ticket.`photo`='','',concat(:baseurl,employee_raise_ticket.`photo`)) as image ,employee_raise_ticket.status,employee_raise_ticket.assigned_person,branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join employee_issue_logs ON employee_issue_logs.issue_sno=employee_raise_ticket.sno inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE employee_issue_logs.created_by like :empid and employee_raise_ticket.`status` like (:status '' :role) and employee_raise_ticket.status!='Pending'  AND DATE(employee_raise_ticket.created_on) between :fdate and :tdate GROUP BY employee_raise_ticket.`sno` order by  employee_raise_ticket.`sno` DESC");
     $stmt1->bindValue(":empid", "%{$row['userid']}%", PDO::PARAM_STR);
	$stmt1->bindValue(":status", "%{$status}%", PDO::PARAM_STR);
	$stmt1->bindValue(":role", "%{$row['role']}%", PDO::PARAM_STR);
	$stmt1->bindparam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt1->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$stmt1->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$stmt1->execute();	
			}
	     if($stmt1 -> rowCount() > 0){
		  http_response_code(200);
            $response['error']= false;
	       $response['message']="Data found";
		 
 $response['sno']="Sno";
 $response['grievanceid']="Grievanceid";
 $response['emp']="Employee";
 $response['type']="Type";
 $response['desc']="Description";
 $response['sufile']="File";
 $response['status']="Status ";
 $response['rmks']="Remarks";
 $response['desig']="Designation";
 $response['branch']="Branch";
 $response['dept']="Department";
 $response['con']="Created_on";
 $response['uploadedbyname']="Uploadedby";
 $response['viewname']="View File";
 $response['resol']="Assign";
 $response['assper']="Assigned Person";
 $response['escalate']="Escalated  To";
 $response['btnlist']=$responsebutton;
 while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){		
         $response['raiseissuelist'][] = [
				'sno'=>$row1['sno'],
				'issuename'=>$row1['issues'],
				'issue_remarks'=>$row1['issue_remarks'],
				'ticket_id'=>$row1['ticket_id'],
				'Status'=>$row1['status'],
				'icon'=>$row1['icon'],
				'department'=>$row1['category'],
				'statuscolour'=>$row1['color'],
				'raisedon'=>$row1['created_on'],
				'type'=>$row1['type'],
				'imageicon'=>$row1['image'],
				'imagecolour'=>'#00ACC1',
				'imagetext'=>'Show Attachment',
				'trackicon'=>$row1['image'],
				'trackcolour'=>'#419505',
				'tracktext'=>'Track',
				'remarks'=>$row1['remarks'],
				'assignedperson'=>$row1['assigned_person'],
				'data'=>$row1['status'],
				'branch'=>$row1['branch'],
				'branchname'=>$row1['branchname'],
				'button1'=>$button1,
				'button2'=>$button2,
				'button3'=>$button3,
				'button4'=>$button4,
				'button5'=>$button5,
				'buttonbg1'=>$buttonbg1,
				'buttonbg2'=>$buttonbg2,
				'buttonbg3'=>$buttonbg3,
				'buttonbg4'=>$buttonbg4,
				'buttonbg5'=>$buttonbg5,
				'buttontext1'=>$buttontext1,
				'buttontext2'=>$buttontext2,
				'buttontext3'=>$buttontext3,
				'buttontext4'=>$buttontext4,
				'buttontext5'=>$buttontext5,
	    ];					
 }
}else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No data found";
	   //$response['solvegrievancelist'][] = [];
     }
	}else{ 
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied!";
}
	}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";    
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo = null;
?>