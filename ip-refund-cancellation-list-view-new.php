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
$receiptno = trim($data->receiptno);
try {
    if (!empty($accesskey) && !empty($receiptno)) {
        $sql=$pdoread->prepare("SELECT `userid`,`cost_center`,`role`  from `user_logins` WHERE mobile_accesskey =:accesskey AND `status` = 'Active'");
        $sql->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            $items_list = $pdoread->prepare("SELECT `payment_history`.`receiptno`,`registration`.`patientname`, `registration`.`contactno` AS contact,ifnull(CONCAT(`registration`.`umrno`,' /',`payment_history`.`admissionon`),'') as umr_no, `payment_history`.`billno`, `payment_history`.`total`, DATE_FORMAT(`payment_history`.`modifiedon`,'%d-%b-%Y') AS created_date, DATE_FORMAT(`payment_history`.`modifiedon`, '%H:%i %p') as created_time, DATE_FORMAT(`payment_history`.`receiptdate`,'%d-%b-%Y %H:%i %p') AS receiptdate, `payment_history`.`receiptno`, `payment_history`.`remarks`, `payment_history`.`discount_value` AS bulk_discount, (CASE WHEN (`payment_history`.`aadhar_file`='#' OR `payment_history`.`aadhar_file`='' OR `payment_history`.`aadhar_file` IS NULL) THEN '#' ELSE concat(:baseurl2,substr(`payment_history`.`aadhar_file`,3)) END) AS 'aadhar_file', (CASE WHEN (`payment_history`.`bank_file`='#' OR `payment_history`.`bank_file`='' OR `payment_history`.`bank_file` IS NULL) THEN '#' ELSE concat(:baseurl2,substr(`payment_history`.`bank_file`,3)) END) AS 'bank_file' FROM `payment_history` INNER join `registration` ON `payment_history`.`admissionon` = `registration`.`admissionno` WHERE `payment_history`.`receiptno`=:receiptno AND `payment_history`.`cost_center`=:branch AND `payment_history`.`bill_type`='IP-REFUND'");
            $items_list->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
            $items_list->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
            $items_list->bindParam(':baseurl2', $baseurl2, PDO::PARAM_STR);
            $items_list->execute();
            $items_lists = $items_list->fetch(PDO::FETCH_ASSOC);
            if ($items_list->rowCount() > 0) {
                http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Found";
                $response['role'] = $result['role'];
                $response['patientname'] = $items_lists['patientname'];
                $response['contact'] = $items_lists['contact'];
                $response['umr_no'] = $items_lists['umr_no'];
                $response['created_date'] = $items_lists['created_date'];
                $response['created_time'] = $items_lists['created_time'];
                $response['remarks'] = $items_lists['remarks'];
                $response['bulk_discount'] = $items_lists['bulk_discount'];
                $response['aadhar_file'] = $items_lists['aadhar_file'];
                $response['bank_file'] = $items_lists['bank_file'];
                $response['refundlist'][0]['total'] =  $items_lists['total'];
                $response['refundlist'][0]['receiptno'] = $items_lists['receiptno'];
                $response['refundlist'][0]['receiptdate'] = $items_lists['receiptdate'];
                $response['refundlist'][0]['billno'] = $items_lists['billno'];
            } else {
                http_response_code(503);
                $response['error'] = true;
                $response['message'] = "No Data Found";
            }
            // logic code end
        } else {
            http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Session expired. Please re-login again";
        }
        } else {
            http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Sorry! some details are missing";
    }
}           catch (PDOException $e) {
            http_response_code(503);
            $response['error'] = true;
            $response['message'] =  "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>
