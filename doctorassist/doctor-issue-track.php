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
$accesskey = trim($data->accesskey);
$ticket_id = $data->ticket_id;
$response = array();
$response1 = array();
try{
if(!empty($accesskey) && !empty($ticket_id)){
	$accesscheck ="SELECT `userid`,CURRENT_TIMESTAMP As time FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
	   $row = $stmt->fetch();
		
     $empids=$row['time'].'-'.$ticket_id;
     $ImagePath = "documents/raiseticketattach/$empids";
		
	$stmt1 = $pdoread->prepare("SELECT doctor_raise_ticket.sno,(SELECT `status` FROM `issue_logs` WHERE issue_sno=doctor_raise_ticket.sno order by `sno` desc LIMIT 1) AS status,doctor_raise_ticket.type AS issuetype,doctor_raise_ticket.category,doctor_raise_ticket.issues,if(doctor_raise_ticket.`issues`='Any other Suggestions',doctor_raise_ticket.`issue_details`,'') as issue_remarks,CONCAT(':baseurl',`photo`) AS file,doctor_raise_ticket.remarks,`assigned_person`,Date_format(`created_on`,'%d-%b-%Y %h:%i %p') AS `createdon`,`user_name`,user_id,`ticket_id`,case when status like '%Pending%'  then  '#F1732E' when status like '%Rejected%'  then  '#E65C12' when status like '%Resolved%'  then  '#43A047' else '#43A047' end  as colour FROM doctor_raise_ticket WHERE doctor_raise_ticket.ticket_id=:ticket_id");
	$stmt1->bindParam(":ticket_id", $ticket_id, PDO::PARAM_STR);
	$stmt1->execute();
	     if($stmt1 ->rowCount() > 0){
		 http_response_code(200);
           $response['error']= false;
	      $response['message']="Data found";
		 $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		  
	$stmt5= $pdoread->prepare("SELECT case when status like '%Pending%'  then  '#F1732E' when status like '%Rejected%'  then  '#E65C12' when status like '%Resolved%'  then  '#43A047' else '#43A047' end  as colour,case when `status`like '%Pending%' then concat(:baseurl,'appicons/pending.png') else '' end  as pendingicon FROM issue_logs WHERE ticket_id=:ticket_id  and status=:status");
	$stmt5->bindParam(":ticket_id", $ticket_id, PDO::PARAM_STR);
	$stmt5->bindParam(":status", $row1['status'], PDO::PARAM_STR);
	$stmt5->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt5->execute(); 
	$row2 = $stmt5->fetch(PDO::FETCH_ASSOC);	  
		  
		
 $response['sno']="Sno";
 $response['ticket_id']="ticket_id";
 $response['status']="Status";
 $response['isstype']="Issue Type";
 $response['desc']="issues";
 $response['uploadedby']="file";
 $response['dept']="Category";
 $response['rmks']="Remarks";
 $response['assignperson']="Assigned Person";
 $response['viewname']="Attachment";
 $response['resol']="Resolution";
  
 $response['sno']=$row1['sno'];	 
 $response['ticket_id']=$row1['ticket_id']; 	  
 $response['status']=$row1['status'];
 $response['Issuetype']=$row1['issuetype'];
 $response['description']=$row1['issues'];
 $response['issue_remarks']=$row1['issue_remarks'];
 $response['uploadedby']=$row1['file'];
 $response['department']=$row1['category'];
 $response['remarks']=$row1['remarks'];
 $response['assignedperson']=$row1['assigned_person'];
 $response['empdetails']=$row1['user_name']."  (".$row1['user_id'].")"; 
 $response['createdon']=$row1['createdon'];
 $response['colour']=$row2['colour'];  
 $response['icon']=$row2['pendingicon'];  
  
$mylist= $pdoread->prepare("SELECT `sno`,`issue_sno`,`assigned_person`,RTRIM(`role`) AS designation,`created_by`,Date_format(`created_on`,'%d-%b-%Y %h:%i %p') AS `createdon`,`reason`,`status`,`created_by_name`,   if(`status` like '%Reject%','#FFD9D9',if(`status` like '%Assign%','#E7FDEA',if(`status` like '%Escalate%','#E5E9FF',if(`status` like '%Resolve%','#BBF4DB','#f5b3ae')))) AS backcolor,if(`status` like '%Reject%','#CC0000',if(`status` like '%Assign%','#43A047',if(`status` like '%Escalate%','#43A047',if(`status` like '%Resolve%','#43A047','#E65C12')))) AS textcolor, case when status like '%Pending%'  then  '#F1732E' when status like '%Reject%'  then  '#E65C12' when status like '%Resolve%'  then  '#43A047' else '#43A047' end  as colour,case when `status`like '%Pending%' then concat(:baseurl,'appicons/pending.png') when `status` like '%Reject%' then concat(:baseurl,'appicons/rejected.png') else concat(:baseurl,'appicons/accepted.png') end  as pendingicon ,(select status from issue_logs where issue_logs.ticket_id=:ticket_id order by sno desc limit 1) as realstatuss FROM `issue_logs` where issue_logs.ticket_id=:ticket_id order by sno desc");
 $mylist->bindParam(":ticket_id", $ticket_id, PDO::PARAM_STR);
 $mylist->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
 $mylist->execute();
 if($mylist ->rowCount() > 0){	
    while($row11 = $mylist->fetch(PDO::FETCH_ASSOC)){
		
	$stmt6= $pdoread->prepare("SELECT case  when status like '%Rejected%'  then  '#E65C12' when status like '%Resolved%'  then  '#43A047' else '#43A047' end  as colour,case when `status`=:realstatus then '#F1732E' else   '#43A047' end  as colour,case when `status` like :realstatus and (`status` not like '%Reject%' and `status` not like '%Resolve%')  then concat(:baseurl,'appicons/pending.png') else  concat(:baseurl,'appicons/accepted.png') end  as pendingicon,if((:status=:realstatus and :status not like '%Reject%' and :status not like '%Resolve%'),concat(:baseurl,'appicons/pending.png'),if((:status=:realstatus and :status like '%Reject%'),concat(:baseurl,'appicons/rejected.png'),concat(:baseurl,'appicons/accepted.png')))  as iconcurr,if((:status=:realstatus and :status not like '%Reject%' and :status not like '%Resolve%'),'#F1732E',if((:status=:realstatus and :status like '%Reject%'),'#CC0000',if((:status=:realstatus and :status like '%Resolve%'),'#43A047', '#43A047')))  as colourcurr FROM issue_logs WHERE ticket_id=:ticket_id  order by sno desc limit 1");
	
	$stmt6->bindParam(":ticket_id", $ticket_id, PDO::PARAM_STR);
	$stmt6->bindValue(":realstatus", $row11['status'], PDO::PARAM_STR);
	$stmt6->bindValue(":status", $row1['status'], PDO::PARAM_STR);
	$stmt6->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt6->execute(); 
	$row3 = $stmt6->fetch(PDO::FETCH_ASSOC);	  
			$temp=[
			"heading"=>$row11['designation'],
			"user"=>$row11['created_by_name']."  (".$row11['created_by'].")",
			"dateon"=>"Date & Time : ".$row11['createdon'],
			"details"=>"Name : ".$row11['assigned_person'],
			"comments"=>"Comments : ".$row11['reason'],
			"status"=>"Status : ".$row11['status'],
			"textcolor"=>$row3['colourcurr'],
			"backcolor"=>$row11['backcolor'],
			"colour"=>$row3['colourcurr'],
			"pendingicon"=>$row3['iconcurr'],
			];
			array_push($response1,$temp);
	}
		    $response['tracklist']=$response1;
 }else{
	         $response['tracklist']=[];
 }	 		 
}else{
		 http_response_code(503);
           $response['error']= true;
	      $response['message']="No data found";
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
}catch(PDOException $e){
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
//$pdo4 = null;
$pdoread = null;
?>