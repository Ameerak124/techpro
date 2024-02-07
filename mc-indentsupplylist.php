<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "db-pdo.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey=$data->accesskey;
$type=$data->type;
$date=$data->date;
$client_name=$data->client_name;
$response = array();
try {
if(!empty($accesskey) &&!empty($type) && !empty($date)&& !empty($client_name)){
	$check = $pdoread->prepare("SELECT `empid` FROM `pologins` WHERE `accesskey`=:accesskey");
      $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
       $check->execute();
          if ($check->rowCount() > 0) {
               $result = $check->fetch(PDO::FETCH_ASSOC);
               $empid = $result['empid'];
if($type == 'ftdindents'){
      $stmt = $pdoread -> prepare("SELECT DISTINCT `client_indent_list`.`itemcode`AS itemcode,client_indent_list.itemname,SUM(client_indent_list.quantity) as quantity,client_indent_list.`manufacturer` FROM `client_indent` INNER JOIN `client_indent_list` ON `client_indent`.`indent_number` = `client_indent_list`.`indent_number` INNER JOIN `client_category` ON `client_category`.`client_id` = `client_indent`.`clientid` WHERE `client_indent_list`.`status` != 'delete' AND DATE(`client_indent`.`purchase_approvedon`) =:date AND `client_indent`.`purchase_approval` = 'Approved' and `client_category`.`client_name` LIKE :client_name group by `client_indent_list`.`itemcode`");
$stmt->bindparam(':date',$date, PDO::PARAM_STR);
$stmt->bindValue(':client_name',"%{$client_name}%", PDO::PARAM_STR);
$stmt -> execute();
}elseif($type=='mtdindents'){


    $stmt = $pdoread -> prepare("SELECT DISTINCT `client_indent_list`.`itemcode` AS itemcode,client_indent_list.itemname,SUM(client_indent_list.quantity) as quantity,client_indent_list.`manufacturer` FROM `client_indent` INNER JOIN `client_indent_list` ON `client_indent`.`indent_number` = `client_indent_list`.`indent_number` INNER JOIN `client_category` ON `client_category`.`client_id` = `client_indent`.`clientid` WHERE `client_indent_list`.`status` != 'delete' AND MONTH(`client_indent`.`purchase_approvedon`) = MONTH(:date) AND `client_indent`.`approvalstatus` = 'Approved' and client_category.client_name like :client_name  group by `client_indent_list`.`itemcode`");
$stmt->bindparam(':date',$date, PDO::PARAM_STR);
$stmt->bindvalue(':client_name',"%{$client_name}%", PDO::PARAM_STR);
$stmt -> execute();
}elseif($type=='ftdinvoice'){
       $stmt = $pdoread -> prepare("SELECT DISTINCT `so_item`.`item_code` AS itemcode,so_item.item_name,SUM(so_item.quantity) as quantity,so_item.manufacturer FROM `tax_invoice` INNER JOIN `so_item` ON `so_item`.`dc_num` = `tax_invoice`.`dcnumber` INNER JOIN `so_generate` ON `so_generate`.`so_number` = `so_item`.`so_number` INNER JOIN `client_category` ON `client_category`.`client_id` = `so_generate`.`clientid` WHERE DATE(`tax_invoice`.`tax_invoicedate`) = :date AND `so_item`.`item_status` != 'delete' AND `so_generate`.`client_name`  like :client_name  group by `so_item`.`item_code`");
	   $stmt->bindparam(':date',$date, PDO::PARAM_STR);
$stmt->bindvalue(':client_name',"%{$client_name}%", PDO::PARAM_STR);
$stmt -> execute();
	   
	   
}elseif($type=='ftdeinvoice'){
      $stmt = $pdoread->prepare("SELECT DISTINCT `so_item`.`item_code` AS itemcode,so_item.item_name,SUM(so_item.quantity) as quantity,so_item.manufacturer  FROM `tax_invoice` INNER JOIN `so_item` ON `so_item`.`dc_num` = `tax_invoice`.`dcnumber` INNER JOIN `so_generate` ON `so_generate`.`so_number` = `so_item`.`so_number` INNER JOIN `client_category` ON `client_category`.`client_id` = `so_generate`.`clientid` WHERE DATE(`tax_invoice`.`e-invoice-on`) = :date AND `so_item`.`item_status` != 'delete'AND `so_generate`.`client_name` like :client_name group by `so_item`.`item_code`");
	  $stmt->bindparam(':date',$date, PDO::PARAM_STR);
     $stmt->bindvalue(':client_name',"%{$client_name}%", PDO::PARAM_STR);
    $stmt -> execute();
	  
}elseif($type=='mtdinvoice'){
       
            $stmt = $pdoread->prepare("SELECT DISTINCT `so_item`.`item_code` AS itemcode,so_item.item_name,SUM(so_item.quantity) as quantity,so_item.manufacturer  FROM `tax_invoice` INNER JOIN `so_item` ON `so_item`.`dc_num` = `tax_invoice`.`dcnumber` INNER JOIN `so_generate` ON `so_generate`.`so_number` = `so_item`.`so_number` INNER JOIN `client_category` ON `client_category`.`client_id` = `so_generate`.`clientid` WHERE DATE(`tax_invoice`.`e-invoice-on`) = :date AND `so_item`.`item_status` != 'delete'AND `so_generate`.`client_name` =:client_name group by `so_item`.`item_code`");
          $stmt->bindparam(':date',$date, PDO::PARAM_STR);
        $stmt->bindparam(':client_name',"%{$client_name}%", PDO::PARAM_STR);
      $stmt -> execute();
  }else($type=='mtdeinvoice'){
          $stmt = $pdoread->prepare("SELECT DISTINCT `so_item`.`item_code` AS itemcode,so_item.item_name,SUM(so_item.quantity) as quantity,so_item.manufacturer FROM `tax_invoice` INNER JOIN `so_item` ON `so_item`.`dc_num` = `tax_invoice`.`dcnumber` INNER JOIN `so_generate` ON `so_generate`.`so_number` = `so_item`.`so_number` INNER JOIN `client_category` ON `client_category`.`client_id` = `so_generate`.`clientid` WHERE DATE(`tax_invoice`.`e-invoice-on`) = :date AND `so_item`.`item_status` != 'delete'AND `so_generate`.`client_name` =:client_name group by `so_item`.`item_code`");
	   $stmt->bindparam(':date',$date, PDO::PARAM_STR);
         $stmt->bindparam(':client_name',$client_name, PDO::PARAM_STR);
         $stmt -> execute();
}	  
 if($stmt -> rowCount() > 0){
$list = $stmt -> fetchAll(PDO::FETCH_ASSOC);
http_response_code(200);  
		 $response['error']= false;
		 $response['message']="Data found";
		 $response['indentitemlist']=$list;
     }
else{
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No data found";
     }
	 } else {
               http_response_code(400);
               $response['error'] = true;
               $response['message'] = "Access denied!";
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