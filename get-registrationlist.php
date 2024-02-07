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
try{
$draw = $data['draw'];
$row = $data['start'];
$rowperpage = $data['length']; // Rows display per page
$columnIndex = $data['order'][0]['column']; // Column index
$columnName = $data['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $data['order'][0]['dir']; // asc or desc
$searchValue = $data['search']['value']; // Search value

$searchArray = array();

## Search 
## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " AND (`umrno` LIKE :umrno or 
   `admissionno` LIKE :admissionno OR 
   `patientname` LIKE :patientname ) ";
   $searchArray = array( 
        'umrno'=>"%$searchValue%", 
        'admissionno'=>"%$searchValue%",
        'patientname'=>"%$searchValue%",
   );
}
## Total number of records without filtering
$stmt = $pdoread->prepare("SELECT COUNT(*) AS allcount FROM `registration` ");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

## Total number of records with filtering
$stmt = $pdoread->prepare("SELECT COUNT(*) AS allcount FROM `registration` WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$stmt = $pdoread->prepare("SELECT `umrno`,  `admissionno`, date_format(`admittedon`,'%d-%b-%Y %h:%m %p') AS admtno,  `patientname`, `patientage`, `consultantname`, `department`, DATE(`dischargedon`) AS DDATE, `admittedward` AS Admittedward  FROM `registration`  WHERE 1 $searchQuery ORDER BY `admittedon` DESC  LIMIT :limit,:offset");

// Bind values
foreach($searchArray as $key=>$search){
   $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
}

$stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
$stmt->execute();
$empRecords = $stmt->fetchAll();

$data = array();

foreach($empRecords as $row){
   $data[] = array(
      "umrno"=>$row['umrno'],
      "admissionno"=>$row['admissionno'],
      "admtno"=>$row['admtno'],
      "patientname"=>$row['patientname'],
      "patientage"=>$row['patientage'],
      "consultantname"=>$row['consultantname'],
      "department"=>$row['department'],
      "DDATE"=>$row['DDATE'],
      "Admittedward"=>$row['Admittedward'],
      "edit"=>'<i class="fa fa-male" aria-hidden="true" data-toggle="modal" data-target="#exampleModal"></i>',
   );
}
## Response
$response = array(
   "draw" => intval($draw),
   "iTotalRecords" => $totalRecords,
   "iTotalDisplayRecords" => $totalRecordwithFilter,
   "aaData" => $data
); 
if($stmt-> rowCount() > 0){
	http_response_code(200);
$response['error']= false;
$response['message']= "Data updated";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please try again";
}
// }else{
// 	http_response_code(400);
// 	$response['error']= true;
// 	$response['message']="Access denied! please try to re-login again";
// }
// }else{
// 	http_response_code(400);
// 	$response['error']= true;
// 	$response['message']="Sorry! some details are missing";
// }
echo json_encode($response);
}catch(PDOException $err){
     echo $err -> getMessage();
}
$con= null;
?>