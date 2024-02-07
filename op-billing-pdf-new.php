<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$billno=$data->billno;
//$ipaddress = $_SERVER['REMOTE_ADDR'];
try{
$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
if(!empty($accesskey)&& !empty($billno)){
  $class_obj = new numbertowordconvertsconver();
//Check access 
$check =$pdoread->prepare("SELECT `userid`AS empid ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check -> execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount()>0) {
//Access verified//
$query=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`umr_registration`.`umrno` AS umr,CONCAT((CASE WHEN  TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT(TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y(s)') WHEN  TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN  CONCAT(TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M(s)') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT(FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D(s)') ELSE 0 END),' / ',`umr_registration`.`patient_gender`) AS agegender,`umr_registration`.`referral_type`,(CASE WHEN `refreral_doctor` = '' THEN '--' ELSE `refreral_doctor` END) AS referraldoctor,CONCAT(`title`,'. ',UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname, `umr_registration`.`mobile_no` AS mobile, `op_biling_history`.`billno` AS billno,`op_biling_history`.`servicecode` AS scode, `op_biling_history`.`services` AS services,SUM(`op_biling_history`.`quantity`) AS qty,ROUND(SUM(`total`),2) AS totalamount,ROUND(SUM(`gstvalue`),2) AS gst ,DATE_FORMAT(`op_biling_history`.`createdon`,'%d-%b-%Y')AS billdate,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name,DATE_FORMAT(`op_biling_history`.`createdon`,'%d-%b-%Y %h:%i %p') AS createdon ,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`op_biling_history`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby,DATE_FORMAT(CURRENT_TIMESTAMP,'%d-%b-%Y %h:%i %p') AS printedon,`op_biling_history`.`visit_type` AS visittype,(`op_biling_history`.`refreral_doctor`)AS refdoc,
(SELECT `op_billing_generate`.`invoice_no`  FROM op_billing_generate WHERE `inv_no`=:billno AND `status`!='Cancelled')AS invoice_no
FROM (SELECT @a:=0) AS a,`op_biling_history` INNER JOIN `umr_registration` ON `op_biling_history`.`umr_no`=`umr_registration`.`umrno` WHERE `op_biling_history`.`billno` =:billno  AND TRIM(`op_biling_history`.`credit_debit`) = 'CREDIT' AND TRIM(`op_biling_history`.`status`) = 'Visible'");
$query->bindParam(':billno', $billno, PDO::PARAM_STR);
$query->bindParam(':userid', $result['empid'], PDO::PARAM_STR);
$query->execute();
  if($query->rowCount()>0) {
	  $queryres=$query->fetch(PDO::FETCH_ASSOC);
$det=$pdoread->prepare("SELECT IFNULL((`discount_val`),'')AS discount_val, `after_val`AS billamount, `paymenttype`, `paymentmode` AS pmode,DATE_FORMAT(`op_billing_generate`.`modified_on`,'%d-%b-%Y')AS billdate, `op_billing_generate`.`cost_center`,`branch_master`.`unit` AS display_name,`branch_master`.`address`,CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS city,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no FROM `op_billing_generate` LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `op_billing_generate`.`cost_center` WHERE `op_billing_generate`.`status` !='Cancelled' AND `inv_no`=:billno");
$det->bindParam(':billno', $billno, PDO::PARAM_STR);
$det->execute();
$res=$det->fetch(PDO::FETCH_ASSOC);

    http_response_code(200);
    $response['error']=false;
    $response['message']='Data found';
    $response['sno']=$queryres['sno'];
    $response['billno']=$queryres['billno'];
    $response['invoice_no']=$queryres['invoice_no'];
    $response['patientname']=$queryres['patientname'];
    $response['mobile']=$queryres['mobile'];
    $response['billdate']=$queryres['billdate'];
    $response['qty']=$queryres['qty'];
    $response['gst']=$queryres['gst'];
    $response['totalamount']=$queryres['totalamount'];
    $response['refdoc']=$queryres['refdoc'];
    $response['organization_name']=$queryres['organization_name'];
    $response['umr']=$queryres['umr'];
    $response['agegender']=$queryres['agegender'];
    $response['createdon']=$queryres['createdon'];
    $response['createdby']=$queryres['createdby'];
    $response['printedby']=$queryres['printedby'];
    $response['printedon']=$queryres['printedon'];
    $response['referral_type']=$queryres['referral_type'];
    $response['predoctor']=$queryres['referraldoctor'];
    $response['visittype']=$queryres['visittype'];
    $response['discount_val']=$res['discount_val'];
    $response['billamount']=$res['billamount'];
    $response['paymenttype']=$res['paymenttype'];
    $response['paymentmode']=$res['pmode'];
    $response['display_name']=$res['display_name'];
    $response['address']=$res['address'];
    $response['city']=$res['city'];
    $response['gst_no']=$res['gst_no'];
    $response['contactno']= "Tel. No: 040 6833 4455 (24/7)";
    $category = $pdoread->prepare("SELECT `category`,(CASE WHEN `category` = 'CONSULTATION' THEN CONCAT('Validity: 1 Consultation(s) Before ',DATE_FORMAT(DATE_ADD(`op_biling_history`.`modifiedon`, INTERVAL 7 DAY),'%d-%b-%Y')) ELSE '' END) AS validity FROM `op_biling_history` WHERE `billno` = :billno GROUP BY `category`");
    $category->bindParam(':billno', $billno, PDO::PARAM_STR);
    $category->execute(); 
    $c = 0;
    $sno=1;
    while($catres=$category->fetch(PDO::FETCH_ASSOC)){
      if($catres['validity'] != ''){
       $response['validity'] = $catres['validity'];
      }

    $response['category'][$c]['display'] = $catres['category'];
    $list=$pdoread->prepare("SELECT `op_biling_history`.`servicecode` AS scode,LEFT(CONCAT(`op_biling_history`.`services`),33) AS services,`op_biling_history`.`subcategory` AS department,ROUND(`rate`,2) AS rate,(CASE WHEN `op_biling_history`.`category` = 'CONSULTATION' THEN '--' ELSE (`op_biling_history`.`quantity`) END) AS qty,ROUND(`total`,2) AS totalamount,`item_disc_value`  FROM (SELECT @a:= 0) AS a,`op_biling_history` INNER JOIN `op_billing_generate` ON `op_biling_history`.`umr_no`=`op_billing_generate`.`umrno` WHERE `op_biling_history`.`billno` = :billno AND `op_biling_history`.`category` = :category AND `op_biling_history`.`credit_debit` = 'CREDIT' AND `op_biling_history`.`status` = 'Visible' GROUP BY scode;");
$list->bindParam(':billno', $billno, PDO::PARAM_STR);
$list->bindParam(':category', $catres['category'], PDO::PARAM_STR);
$list->execute();
$sn=0;
while($listres=$list->fetch(PDO::FETCH_ASSOC)){
    // $response['paymentmode']=$listres['paymentmode'];
    // $response['list'][$sn]['snos'] = $listres['snos'];
    $response['category'][$c]['list'][$sn]['sno'] = $sno;
    $response['category'][$c]['list'][$sn]['services'] = $listres['services'];
    $response['category'][$c]['list'][$sn]['department'] = $listres['department'];
    $response['category'][$c]['list'][$sn]['rate'] = $listres['rate'];
    $response['category'][$c]['list'][$sn]['qty'] = $listres['qty'];
    $response['category'][$c]['list'][$sn]['item_disc_value'] = $listres['item_disc_value'];
    $response['category'][$c]['list'][$sn]['totalamount'] = $listres['totalamount'];
  $sn++;
  $fetchpackage = $pdoread->prepare("SELECT `service_name`,`service_code`,`service_department` AS department  FROM `package_list` WHERE `package_id` = :scode AND `status` = 'Active' AND branch = :costcenter ORDER BY  package_list.service_department ASC");
  $fetchpackage->bindParam(':scode',$listres['scode'], PDO::PARAM_STR);
  $fetchpackage->bindParam(':costcenter',$result['cost_center'], PDO::PARAM_STR);
$fetchpackage->execute();
$a = 0;
if($fetchpackage->rowCount() > 0){
 
    $response['package'][$a]['displayname'] = $listres['services'];
    $s = 0;
  while($fetchpackageres=$fetchpackage->fetch(PDO::FETCH_ASSOC)){
    $response['package'][$a]['list'][$s]->scode = $fetchpackageres['service_code'];
    $response['package'][$a]['list'][$s]->service = $fetchpackageres['service_name'];
    $response['package'][$a]['list'][$s]->department = $fetchpackageres['department'];
    $s++;
  }
     
}
  $a++; 
  $sno++;
}
$c++;
  }
$payment = $pdoread->prepare("SELECT @a:=@a+1 sno,`paymentmode`,`amount`,(CASE WHEN `referenceno` = '' THEN '--' ELSE `paymentmode` END) AS referenceno FROM (SELECT @a:= 0) AS a,`payment_history` WHERE `billno` = :billno AND `status` IN ('Visible','Success')");
$payment->bindParam(':billno', $billno, PDO::PARAM_STR);
$payment->execute();
if($payment->rowCount() > 0){
    while($paymentres=$payment->fetch(PDO::FETCH_ASSOC)){
      //  $response['payment'] = 'Payment done';
        $response['payments'][] = $paymentres;
    }
}
//$amount1 = $con->prepare("SELECT SUM(`rate`) AS grossamount,SUM(`gstvalue`) AS gstamount,SUM(`item_disc_value`) AS discountamount,SUM(`total`) AS netamount FROM `op_biling_history` WHERE `billno` = :billno AND `credit_debit` = 'CREDIT' AND `status` = 'Visible'");
$amount1 = $pdoread->prepare("SELECT E.grossamount,E.gstamount,ROUND(E.discountamount+`op_billing_generate`.`discount_val`,0) AS discountamount,ROUND(E.netamount1-`op_billing_generate`.`discount_val`,0) AS netamount FROM (SELECT SUM(`rate`) AS grossamount,SUM(`gstvalue`) AS gstamount,SUM(`item_disc_value`) AS discountamount,SUM(`total`) AS netamount1,`op_biling_history`.`billno` FROM `op_biling_history` WHERE `billno` = :billno AND `credit_debit` = 'CREDIT' AND `status` = 'Visible') AS E LEFT JOIN `op_billing_generate` ON `op_billing_generate`.`inv_no` = E.billno");
$amount1->bindParam(':billno', $billno, PDO::PARAM_STR);
$amount1->execute();
if($amount1->rowCount() > 0){

    while($amountres=$amount1->fetch(PDO::FETCH_ASSOC)){
          $response['amountvalue'][0]['display'] = "Gross Value";
          $response['amountvalue'][0]['value'] = number_format($amountres['grossamount'],2);
          //$response['amountvalue'][1]['display'] = "GST";
          //$response['amountvalue'][1]['value'] = number_format($amountres['gstamount'],2);
          $response['amountvalue'][1]['display'] = "Discount Value";
          $response['amountvalue'][1]['value'] = number_format($amountres['discountamount'],2);
          if($res['paymenttype'] == 'Credit'){
            $response['amountvalue'][2]['display'] = "Due Value";
          $response['amountvalue'][2]['value'] = number_format($amountres['netamount'],2);
          $response['totalinwords'] = "Received with Thanks Rupees ".$class_obj->convert_number(0)." Only";

        }else{
          $response['amountvalue'][2]['display'] = "Paid Value";
          $response['amountvalue'][2]['value'] = number_format($amountres['netamount'],2);
          $response['totalinwords'] = "Received with Thanks Rupees ".$class_obj->convert_number($amountres['netamount'])." Only";
          }
          

      }
}
}
else {
    
    $response['error']=true;
    $response['message']='Sorry! please try again';
}
}else {
    $response['error']=true;
    $response['message']='Access denied! please try to re-login again';
}
}else{   
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
   }
}catch(PDOException $e) {
    http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
class numbertowordconvertsconver {
  function convert_number($number) 
  {
      if (($number < 0) || ($number > 999999999)) 
      {
          throw new Exception("Number is out of range");
      }
      $giga = floor($number / 1000000);
      // Millions (giga)
      $number -= $giga * 1000000;
      $kilo = floor($number / 1000);
      // Thousands (kilo)
      $number -= $kilo * 1000;
      $hecto = floor($number / 100);
      // Hundreds (hecto)
      $number -= $hecto * 100;
      $deca = floor($number / 10);
      // Tens (deca)
      $n = $number % 10;
      // Ones
      $result = "";
      if ($giga) 
      {
          $result .= $this->convert_number($giga) .  "Million";
      }
      if ($kilo) 
      {
          $result .= (empty($result) ? "" : " ") .$this->convert_number($kilo) . " Thousand";
      }
      if ($hecto) 
      {
          $result .= (empty($result) ? "" : " ") .$this->convert_number($hecto) . " Hundred";
      }
      $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
      $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
      if ($deca || $n) {
          if (!empty($result)) 
          {
              $result .= " and ";
          }
          if ($deca < 2) 
          {
              $result .= $ones[$deca * 10 + $n];
          } else {
              $result .= $tens[$deca];
              if ($n) 
              {
                  $result .= "-" . $ones[$n];
              }
          }
      }
      if (empty($result)) 
      {
          $result = "zero";
      }
      return $result;
  }
}
?>