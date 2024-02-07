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
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$realstatus = $data->status;
$searchterm = $data->searchterm;
$branch = $data->branch;
$response = array();
$response1 = array();
try
{
if(!empty($accesskey)){
	

 $check = $pdo -> prepare("SELECT `userid`,`branch`,`cost_center`,role,username,concat(TRIM(username),' - ','(',userid,')') as assigningperson FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$location = explode(",",$result['storeaccess']);
$i = 0;
if($check -> rowCount() > 0){
	
	
/* 	$stmt=$pdo->prepare("SELECT `display_name`, `branch_name`, `cost_center` FROM `branch_master` where display_name!='test' group by `cost_center`");
	$stmt -> execute();
	if($stmt->rowCount()>0){
		
	$unittotal='0';	
	$corporatetotal='0';	
	$directortotal='0';	
	$edtotal='0';	
	while($result1 = $stmt->fetch(PDO::FETCH_ASSOC)){ */
	
		
	if($realstatus=='Pending at Unit HR'){	
	
	   	    $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `employee_level_designations` inner join user_logins on user_logins.role= employee_level_designations.CL WHERE CL ='Unit HR' AND user_logins.cost_center=:branch AND user_logins.userid=:empid and FIND_IN_SET(:role,`CL`)<>0 LIMIT 1";
	$stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $result['role'], PDO::PARAM_STR);
	$stmt12 -> bindParam(":branch", $branch, PDO::PARAM_STR);
	$stmt12 -> bindParam(":empid", $result['userid'], PDO::PARAM_STR);
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
		$revenue_list = $pdo -> prepare("SELECT employee_raise_ticket.`sno`, employee_raise_ticket.`type`,employee_raise_ticket.user_name, employee_raise_ticket.`ticket_id`, employee_raise_ticket.`category`, employee_raise_ticket.`issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, `issue_details`, `remarks`, `photo`,  case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') WHEN employee_raise_ticket.`status` like '%Rejected%' THEN concat(:baseurl,'appicons/rejected.png')  else concat(:baseurl,'appicons/accepted.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' WHEN employee_raise_ticket.`status` like '%Rejected%' THEN '#fc033d' ELSE '#419505' END AS color ,employee_raise_ticket.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%i:%s') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(`photo`='Please select an image file to upload.' OR `photo`='Yes' or `photo`='Invalid image file'  or `photo`='','',concat(:baseurl,'mobile/employee/',`photo`)) as image ,employee_raise_ticket.status ,assigned_person,realstatus,employee_raise_ticket.branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE `realstatus` in ('Pending at Unit HR','Inprocessing by Unit HR') and employee_raise_ticket.`branch`=:branch and date(employee_raise_ticket.`created_on`) BETWEEN :fdate and :tdate and (ticket_id like :searchterm||user_name like :searchterm) ORDER BY  employee_raise_ticket.created_on DESC");
		
		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	//$revenue_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
	$revenue_list->bindParam(':branch', $branch, PDO::PARAM_STR);
	$revenue_list->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$revenue_list->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
   	$revenue_list -> execute();
	
	}else if($realstatus=='Pending by Regional HR'){	
	
	           $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `employee_level_designations` inner join user_logins on user_logins.role= employee_level_designations.CL WHERE CL ='Regional HR' AND user_logins.cost_center=:branch AND user_logins.userid=:empid and FIND_IN_SET(:role,`CL`)<>0 LIMIT 1";
	$stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $result['role'], PDO::PARAM_STR);
	$stmt12 -> bindParam(":branch", $branch, PDO::PARAM_STR);
	$stmt12 -> bindParam(":empid", $result['userid'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
    $rows = $stmt12->fetch();
	
	           $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
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
		$revenue_list = $pdo -> prepare("SELECT employee_raise_ticket.`sno`, `type`,employee_raise_ticket.user_name, `ticket_id`, `category`, `issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, `issue_details`, `remarks`, `photo`,  case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') WHEN employee_raise_ticket.`status` like '%Rejected%' THEN concat(:baseurl,'appicons/rejected.png')  else concat(:baseurl,'appicons/accepted.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' WHEN employee_raise_ticket.`status` like '%Rejected%' THEN '#fc033d' ELSE '#419505' END AS color,employee_raise_ticket.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(`photo`='Please select an image file to upload.' OR `photo`='Yes' or `photo`='Invalid image file'  or `photo`='','',concat(:baseurl,'mobile/employee/',`photo`)) as image ,employee_raise_ticket.status ,assigned_person,realstatus ,employee_raise_ticket.branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center  WHERE `realstatus` IN ('Pending by Regional HR','Inprocessing by Regional HR','Pending at Regional HR') and employee_raise_ticket.`branch`=:branch and date(employee_raise_ticket.`created_on`) BETWEEN :fdate and :tdate and (ticket_id like :searchterm||user_name like :searchterm) ORDER BY  employee_raise_ticket.created_on DESC");
		
		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	//$revenue_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
	$revenue_list->bindParam(':branch', $branch, PDO::PARAM_STR);
	$revenue_list->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$revenue_list->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
	$revenue_list -> execute();
	
	}else if($realstatus=='Pending by Head HR'){
  $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `employee_level_designations` inner join user_logins on user_logins.role= employee_level_designations.CL WHERE CL ='Head HR' AND user_logins.cost_center=:branch AND user_logins.userid=:empid and FIND_IN_SET(:role,`CL`)<>0 LIMIT 1";
	$stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $result['role'], PDO::PARAM_STR);
	$stmt12 -> bindParam(":branch", $branch, PDO::PARAM_STR);
	$stmt12 -> bindParam(":empid", $result['userid'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
    $rows = $stmt12->fetch();
		
                $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
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
	
	$revenue_list = $pdo -> prepare("SELECT employee_raise_ticket.`sno`, `type`, employee_raise_ticket.`ticket_id`,user_name, `category`, `issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, `issue_details`, `remarks`, `photo`,  case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') WHEN employee_raise_ticket.`status` like '%Rejected%' THEN concat(:baseurl,'appicons/rejected.png')  else concat(:baseurl,'appicons/accepted.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' WHEN employee_raise_ticket.`status` like '%Rejected%' THEN '#fc033d' ELSE '#419505' END AS color ,employee_raise_ticket.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(`photo`='Please select an image file to upload.' OR `photo`='Yes' or `photo`='Invalid image file'  or `photo`='','',concat(:baseurl,'mobile/employee/',`photo`)) as image ,employee_raise_ticket.status ,assigned_person,realstatus ,employee_raise_ticket.branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE `realstatus` in ('Pending by Head HR','Pending at Head HR','Inprocessing by Head HR') and employee_raise_ticket.`branch`=:branch and date(employee_raise_ticket.`created_on`) BETWEEN :fdate and :tdate and (ticket_id like :searchterm||user_name like :searchterm) ORDER BY  employee_raise_ticket.created_on DESC");
	
		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	//$revenue_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
	$revenue_list->bindParam(':branch', $branch, PDO::PARAM_STR);
	$revenue_list->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$revenue_list->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
	$revenue_list -> execute();
	
		}else if($realstatus=='Pending by ED'){	
		
         $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `employee_level_designations` inner join user_logins on user_logins.role= employee_level_designations.CL WHERE CL ='ED' AND user_logins.cost_center=:branch AND user_logins.userid=:empid and FIND_IN_SET(:role,`CL`)<>0 LIMIT 1";
	$stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $result['role'], PDO::PARAM_STR);
	$stmt12 -> bindParam(":branch", $branch, PDO::PARAM_STR);
	$stmt12 -> bindParam(":empid", $result['userid'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
    $rows = $stmt12->fetch();
        			
                 $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
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
	
	$revenue_list = $pdo -> prepare("SELECT employee_raise_ticket.`sno`, `type`, employee_raise_ticket.`ticket_id`,user_name, `category`, `issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, `issue_details`, `remarks`, `photo`,  case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') WHEN employee_raise_ticket.`status` like '%Rejected%' THEN concat(:baseurl,'appicons/rejected.png')  else concat(:baseurl,'appicons/accepted.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' WHEN employee_raise_ticket.`status` like '%Rejected%' THEN '#fc033d' ELSE '#419505' END AS color ,employee_raise_ticket.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%m:%i') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(`photo`='Please select an image file to upload.' OR `photo`='Yes' or `photo`='Invalid image file'  or `photo`='','',concat(:baseurl,'mobile/employee/',`photo`)) as image ,employee_raise_ticket.status ,assigned_person,`realstatus`,employee_raise_ticket.branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE `realstatus`IN ('Pending by ED') and employee_raise_ticket.`branch`=:branch and date(employee_raise_ticket.`created_on`) BETWEEN :fdate and :tdate and (ticket_id like :searchterm||user_name like :searchterm) ORDER BY  employee_raise_ticket.created_on DESC");
	
		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	//$revenue_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
	$revenue_list->bindParam(':branch', $branch, PDO::PARAM_STR);
	$revenue_list->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$revenue_list->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
	$revenue_list -> execute();
	
		}else if($realstatus=='Closed'){
			/* $displaybutton ="SELECT  if(`assign`='1','Assign','No') AS assign_button, if(`escalate`='1','Escalate','No') AS `escalate_button`,if(`reject`='1','Reject','No') AS`reject`, if(`resolve`='1','Resolve','No') AS `resolve`,if(`inprocessing`='1','Inprocessing','No') as inprocessing,if(`assign`='1','#E7FDEA','No') AS bgassign, if(`escalate`='1','#E5E9FF','No') AS `bgescalate`,if(`reject`='1','#FFD9D9','No') AS`bgreject`, if(`resolve`='1','#BBF4DB','No') AS `bgresolve`,if(`inprocessing`='1','#E9bbF4','No') as bgprocessing,if(`assign`='1','#49B888','No') AS textassign, if(`escalate`='1','#369DB3','No') AS `textescalate`,if(`reject`='1','#FD1F0F','No') AS`textreject`, if(`resolve`='1','#407529','No') AS `textresolve`,if(`inprocessing`='1','#5E2975','No') as textprocessing FROM `issue_level_designations` WHERE CL ='ED' and FIND_IN_SET(:role,`CL`)<>0 LIMIT 1";
	$stmt12 = $pdo->prepare($displaybutton);
	$stmt12 -> bindParam(":role", $result['role'], PDO::PARAM_STR);
	$stmt12->execute();
	if($stmt12->rowCount()>0){
    $rows = $stmt12->fetch();

			
                 $button1="No";
				$button2=$rows['escalate_button'];
				$button3=$rows['reject'];
				$button4=$rows['resolve'];
				$button5=$rows['inprocessing'];
				$buttonbg1="No";
				$buttonbg2=$rows['bgescalate'];
				$buttonbg3=$rows['bgreject'];
				$buttonbg4=$rows['bgresolve'];
				$buttonbg5=$rows['bgprocessing'];
				$buttontext1="No";
				$buttontext2=$rows['textescalate'];
				$buttontext3=$rows['textreject'];
				$buttontext4=$rows['textresolve'];		
				$buttontext5=$rows['textprocessing'];	
				
				
		}else{	 */
	
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
	/* }	 */		
	
	$revenue_list = $pdo -> prepare("SELECT employee_raise_ticket.`sno`, `type`, employee_raise_ticket.`ticket_id`,user_name, `category`, `issues`,if(employee_raise_ticket.`issues`='Any other Suggestions',employee_raise_ticket.`issue_details`,'') as issue_remarks, `issue_details`, `remarks`, `photo`, case when employee_raise_ticket.`status`='Pending' then concat(:baseurl,'appicons/pending.png') WHEN employee_raise_ticket.`status` like '%Rejected%' THEN concat(:baseurl,'appicons/rejected.png')  else concat(:baseurl,'appicons/accepted.png') end  as icon , CASE WHEN employee_raise_ticket.`status`='Pending' THEN '#F1732E' WHEN employee_raise_ticket.`status` like '%Rejected%' THEN '#fc033d' ELSE '#419505' END AS color ,employee_raise_ticket.`created_by`, date_format(employee_raise_ticket.`created_on`,'%d-%b-%Y %h:%m:%i') as created_on , employee_raise_ticket.`modified_by`, employee_raise_ticket.`modified_on`, if(`photo`='Please select an image file to upload.' OR `photo`='Yes' or `photo`='Invalid image file'  or `photo`='','',concat(:baseurl,'mobile/employee/',`photo`)) as image ,employee_raise_ticket.status ,assigned_person,`realstatus` ,employee_raise_ticket.branch,branch_master.display_name as branchname FROM `employee_raise_ticket` inner join branch_master on employee_raise_ticket.branch=branch_master.cost_center WHERE (`realstatus` LIKE  '%Resolve%' or `realstatus` LIKE  '%Reject%') and employee_raise_ticket.`branch`=:branch and date(employee_raise_ticket.`created_on`) BETWEEN :fdate and :tdate and (ticket_id like :searchterm||user_name like :searchterm) ORDER BY  employee_raise_ticket.created_on DESC");

		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	//$revenue_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
	$revenue_list->bindParam(':branch', $branch, PDO::PARAM_STR);
	$revenue_list->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
	$revenue_list->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
	$revenue_list -> execute();
		}
		if($revenue_list -> rowCount() > 0){
			http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
   $response['title']="Trend Analysis";
	while($revenue_data = $revenue_list->fetch(PDO::FETCH_ASSOC)){
	//$my_array = array("Unit","Unit Head","Corporate Head","Dir&Oper","ED");
	
	
		
	$response['issuedashboardlist'][]=[
                'sno'=>$revenue_data['sno'],
				'issuename'=>$revenue_data['issues'],
				'issue_remarks'=>$revenue_data['issue_remarks'],
				'ticket_id'=>$revenue_data['ticket_id'],
				'Status'=>$revenue_data['status'],
				'icon'=>$revenue_data['icon'],
				'department'=>$revenue_data['category'],
				'statuscolour'=>$revenue_data['color'],
				'raisedon'=>$revenue_data['created_on'],
				'type'=>$revenue_data['type'],
				'imageicon'=>$revenue_data['image'],
				'imagecolour'=>'#00ACC1',
				'imagetext'=>'Show Attachment',
				'trackicon'=>$revenue_data[image],
				'trackcolour'=>'#419505',
				'tracktext'=>'Track',
				'remarks'=>$revenue_data['remarks'],
				'assignedperson'=>$revenue_data['assigned_person'],
				'data'=>$revenue_data['status'],
				'realstatus'=>$revenue_data['realstatus'],
				'branch'=>$revenue_data['branch'],
				'branchname'=>$revenue_data['branchname'],
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
	/* $unittotal=$unittotal+$revenue_data['unitheadpending'];
$corporatetotal=$corporatetotal+$revenue_data['pencorhead'];
$directortotal=$directortotal+$revenue_data['pendirector'];
$edtotal=$edtotal+$revenue_data['pendinged']; */
	/* array_push($response1,$temp);
	} */
	 /* $response['unittotal']=strval($unittotal);
 $response['corporatetotal']=strval($corporatetotal);
 $response['directortotal']=strval($directortotal);
 $response['edtotal']=strval($edtotal);
 for($x = 0; $x < sizeof($my_array); $x++){
		$response['titlelist'][$x]['type']=$my_array[$x];
     } */
     /* $response['issuedashboardlist'] = $response1; */
	}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No Data Found";
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
   unset($con);
   
   
   function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
?>