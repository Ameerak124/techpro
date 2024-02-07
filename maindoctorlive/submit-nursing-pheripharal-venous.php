<?php
 header("Content-Type: application/json; charset=UTF-8");
 include "pdo-db.php";
 $data=json_decode(file_get_contents("php://input"));
 $accesskey =trim($data->accesskey);
 $umrno =trim($data->umrno);
 $admission_no =trim($data->admission_no);
 $created_name =trim($data->created_name);
 $consent_form =trim($data->consent_form);
 $insertion_type =trim($data->insertion_type);
 $adhered_to =trim($data->adhered_to);
 $site_of_insertion =trim($data->site_of_insertion);
 $reason_for_insertion =trim($data->reason_for_insertion);
 $size =trim($data->size);
 $number_of_attempts =trim($data->number_of_attempts);
 $failure_reason =trim($data->failure_reason);

 $response=array();
 try {
	 if(!empty($accesskey) && !empty($umrno)&& !empty($admission_no)) {
$check = $pdoread->prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
//fetched details from user logins//
if($check->rowcount() > 0) {
	$validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
    $validate->bindParam(':ip', $admission_no, PDO::PARAM_STR);
    $validate -> execute();
    $validates = $validate->fetch(PDO::FETCH_ASSOC);
    if($validate -> rowCount() > 0){
//check if logins already exists
// $get_data=$con->prepare("SELECT `umr_no` FROM `nursing_pheripheral_venous` WHERE `status`='Active' AND `created_by`=:userid AND `umr_no`=:umr ");
// $get_data->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
// $get_data->bindParam(':umr', $umrno, PDO::PARAM_STR);
// $get_data->execute();
// if($get_data->rowCount() > 0){
// $insert=$con->prepare("UPDATE `nursing_pheripheral_venous` SET  `created_name`=:created_name,`consent_form`=:consent_form,`insertion_type`=:insertion_type,`adhered_to`=:adhered_to,`site_of_insertion`=:site_of_insertion,`reason_for_insertion`=:reason_for_insertion,`size`=:size,`number_of_attempts`=:number_of_attempts,`failure_reason`=:failure_reason,`modified_by`=:userid,`modified_on`=CURRENT_TIMESTAMP  WHERE `umr_no`=:umr_no AND `status`='Active' AND `cost_center`=:branch AND  `admission_no`=:admission_no AND `page_id`='V1' ");
// $insert->bindParam(':umr_no', $umrno, PDO::PARAM_STR);
// $insert->bindParam(':admission_no', $admission_no, PDO::PARAM_STR);
// $insert->bindParam(':created_name', $created_name, PDO::PARAM_STR);
// $insert->bindParam(':consent_form', $consent_form, PDO::PARAM_STR);
// $insert->bindParam(':insertion_type', $insertion_type, PDO::PARAM_STR);
// $insert->bindParam(':adhered_to', $adhered_to, PDO::PARAM_STR);
// $insert->bindParam(':site_of_insertion', $site_of_insertion, PDO::PARAM_STR);
// $insert->bindParam(':reason_for_insertion', $reason_for_insertion, PDO::PARAM_STR);
// $insert->bindParam(':size', $size, PDO::PARAM_STR);
// $insert->bindParam(':number_of_attempts', $number_of_attempts, PDO::PARAM_STR);
// $insert->bindParam(':failure_reason', $failure_reason, PDO::PARAM_STR);
// $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
// $insert->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
// $insert->execute();

// if($insert->rowCount() > 0){
//     $response['error']=false;
//     $response['message']="Data Updated Successfully";
// }else{
//     $response['error']=true;
//     $response['message']="Please Try Again!";
// }

// }else{
$insert=$pdo4->prepare("INSERT INTO `nursing_pheripheral_venous`(`sno`, `umr_no`, `admission_no`, `created_name`, `consent_form`, `insertion_type`, `adhered_to`, `site_of_insertion`, `reason_for_insertion`, `size`, `number_of_attempts`, `failure_reason`, `created_by`, `created_on`, `modified_by`, `modified_on`, `cost_center`, `status`, `ip`,`page_id`) VALUES (NULL,:umr_no,:admission_no,:created_name,:consent_form,:insertion_type,:adhered_to,:site_of_insertion,:reason_for_insertion,:size,:number_of_attempts,:failure_reason,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:branch,'Active','','V1')");
$insert->bindParam(':umr_no', $umrno, PDO::PARAM_STR);
$insert->bindParam(':admission_no', $admission_no, PDO::PARAM_STR);
$insert->bindParam(':created_name', $created_name, PDO::PARAM_STR);
$insert->bindParam(':consent_form', $consent_form, PDO::PARAM_STR);
$insert->bindParam(':insertion_type', $insertion_type, PDO::PARAM_STR);
$insert->bindParam(':adhered_to', $adhered_to, PDO::PARAM_STR);
$insert->bindParam(':site_of_insertion', $site_of_insertion, PDO::PARAM_STR);
$insert->bindParam(':reason_for_insertion', $reason_for_insertion, PDO::PARAM_STR);
$insert->bindParam(':size', $size, PDO::PARAM_STR);
$insert->bindParam(':number_of_attempts', $number_of_attempts, PDO::PARAM_STR);
$insert->bindParam(':failure_reason', $failure_reason, PDO::PARAM_STR);
$insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$insert->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
//$insert->bindParam(':ip', $ipaddress, PDO::PARAM_STR);
$insert->execute();

if($insert->rowCount() > 0){
	$response['error']=false;
	$response['message']="Data Added Successfully";
}else{
	$response['error']=true;
	$response['message']="Please Try Again!";
}
// }
	}else{
		$response['error'] = true;
		$response['message']= "You have entered incorrect IP Number / Patient Checked Out";
	}
}else{
	$response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
}
//Check User Access End
}else{
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
 }
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>