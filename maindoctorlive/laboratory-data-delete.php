<?php
function iplabremoveworklist($con,$userid,$requstionno){
	$mydata=$con->prepare("UPDATE `lab_worklist` SET `bill_status`='C' , `status`='I', `modified_on`=CURRENT_TIMESTAMP , `modified_by`=:userid ,`track_status`='Deleted' WHERE `requisition`=:requstionno");
	$mydata->bindParam(':userid', $userid, PDO::PARAM_STR);
	$mydata->bindParam(':requstionno', $requstionno, PDO::PARAM_STR);
	$mydata->execute();
	if($mydata->rowCount() > 0){
$response3 = 'Service deleted';
	}else{
		$response3 = 'Service not deleted';
	}
	return $response3;
	}
?>