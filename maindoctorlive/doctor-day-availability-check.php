<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
	//data credentials
	include 'pdo-db.php';
	//data credential
	$data = json_decode(file_get_contents("php://input"));
	$accesskey = $data->accesskey;
	$doctorcode = $data->doctorcode;
	$value = $data->branch;
	$mon_year = $data->mon_year;
	$response1=array();
	
	if (!empty($accesskey) && !empty($doctorcode) && !empty($value)) {
		//Check access 
		$check = $pdoread->prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check->execute();
		if ($check->rowCount() > 0){
$start = strtotime($mon_year);
$monthyear = strtotime(date("F-Y")); 
$finish = strtotime("+1 months", strtotime(date("F-Y")));
if($start<=$finish && $start>=$monthyear){				
			
			if(!empty($mon_year)){
$stmt=$pdoread->prepare("select * from 
(select adddate('1900-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where DATE_FORMAT(`selected_date`,'%M-%Y')= :mon_year order by selected_date");
$stmt->bindParam(':mon_year', $mon_year, PDO::PARAM_STR);
$stmt->execute();	
			}else{
$stmt=$pdoread->prepare("select * from 
(select adddate('1900-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date between Date_format(Current_date,'%Y-%m-01') and LAST_DAY(Current_date) order by selected_date");
//$stmt->bindParam(':mon_year', $mon_year, PDO::PARAM_STR);
$stmt->execute();

			}				
if ($stmt->rowCount() > 0){	
	while($data = $stmt->fetch(PDO::FETCH_ASSOC)){	
	
$get_availability_data = $pdoread->prepare("SELECT `fdate` FROM `doctor_availability` WHERE `DoctorCode`=:doctorid AND `estatus`='Active' AND :selectdate IN (`fdate`)");
                $get_availability_data->bindParam(':doctorid', $doctorcode, PDO::PARAM_STR);
                $get_availability_data->bindParam(':selectdate',$data['selected_date'], PDO::PARAM_STR);
                $get_availability_data->execute();
                if ($get_availability_data->rowCount()== 0) {
	
	
			$getlist = $pdoread->prepare("SELECT DATE_FORMAT(:selectdate,'%d %b %Y - %W') AS date,`day_name` AS dayname,LEFT(`day_name`,1) AS shortname,(CASE WHEN IFNULL(`doctor_timings`.`day_name`,'--') = '--' THEN 'Not Available' ELSE 'Available' END) AS available,DATE_FORMAT(:selectdate,'%d') AS calenderdate,DATE_FORMAT(:selectdate,'%m') AS calendermonth,DATE_FORMAT(:selectdate,'%Y') AS calenderyear  FROM  `doctor_timings`  where `doctor_timings`.`selecteddate` =:selectdate AND `doctor_timings`.`location_cd` =:valuesd AND `doctor_timings`.`status` = 'Active' AND `doctor_timings`.`doctor_code` =:doctorcode");
			$getlist->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
			$getlist->bindParam(':valuesd', $value, PDO::PARAM_STR);
			$getlist->bindParam(':selectdate', $data['selected_date'], PDO::PARAM_STR);
			$getlist->execute();
			if($getlist->rowCount()>0){
			$data1 = $getlist->fetch(PDO::FETCH_ASSOC);
			 $date=$data1['date'];
			$dayname=$data1['dayname'];
			$shortname=$data1['shortname'];
			$available=$data1['available'];
			$calenderdate=$data1['calenderdate'];
			$calendermonth=$data1['calendermonth'];
			$calenderyear=$data1['calenderyear'];
			//$colour='#ff99cc00';
			$colour='#e6f5ea';
			
			}else{
				$getlist1 = $pdoread->prepare("select DATE_FORMAT(:selectdate,'%d %b %Y - %W') AS date, dayname(:selectdate) as dayname ,left(dayname(:selectdate),1) as shortname,'Not Available' as available,DATE_FORMAT(:selectdate,'%d') AS calenderdate,DATE_FORMAT(:selectdate,'%m') AS calendermonth,DATE_FORMAT(:selectdate,'%Y') AS calenderyear from doctor_timings limit 1");
			$getlist1->bindParam(':selectdate', $data['selected_date'], PDO::PARAM_STR);
			$getlist1->execute();
             $data11 = $getlist1->fetch(PDO::FETCH_ASSOC);
			 $date=$data11['date'];
			$dayname=$data11['dayname'];
			$shortname=$data11['shortname'];
			$available=$data11['available'];
			$calenderdate=$data11['calenderdate'];
			$calendermonth=$data11['calendermonth'];
			$calenderyear=$data11['calenderyear'];
			//$colour='#ffff4444';
			$colour='#f7e6e6';
			}
				}else{
					
					$getlist2 = $pdoread->prepare("select DATE_FORMAT(:selectdate,'%d %b %Y - %W') AS date, dayname(:selectdate) as dayname ,left(dayname(:selectdate),1) as shortname,'Not Available' as available,DATE_FORMAT(:selectdate,'%d') AS calenderdate,DATE_FORMAT(:selectdate,'%m') AS calendermonth,DATE_FORMAT(:selectdate,'%Y') AS calenderyear from doctor_timings limit 1");
			$getlist2->bindParam(':selectdate', $data['selected_date'], PDO::PARAM_STR);
			$getlist2->execute();
             $data2 = $getlist2->fetch(PDO::FETCH_ASSOC);
			 $date=$data2['date'];
			$dayname=$data2['dayname'];
			$shortname=$data2['shortname'];
			$available=$data2['available'];
			$calenderdate=$data2['calenderdate'];
			$calendermonth=$data2['calendermonth'];
			$calenderyear=$data2['calenderyear'];
			//$colour='#ffff4444';
			$colour='#f7e6e6';
					
				}
			
			
				$temp=[
				     'date'=>$date,
					'intime'=>'',
					'outtime'=>'',
					'totaltime'=>'',
					'attendance'=>$available,
					'payroll_status'=>'',
					'shift'=>'',
					'calenderdate'=>intval($calenderdate),
					'calendermonth'=>intval($calendermonth),
					'calenderyear'=>intval($calenderyear),
					'calenderstatus'=>$available,
					'calendercolour'=>$colour,
				
				];
				array_push($response1,$temp);
			
						
			}
				http_response_code(200);
				$response['error'] = false;
				$response['message'] = "Data found";
				$response['doctoravailabledata'] = $response1;
			}else {
				http_response_code(503);
				$response['error'] = true;
				$response['message'] = "No Data found";
			}
		}else{
			http_response_code(503);
				$response['error'] = true;
				$response['message'] = "No Data found";
		}
		} else {
			http_response_code(400);
			$response['error'] = true;
			$response['message'] = "Access denied!";
		}
	} else {
		http_response_code(400);
		$response['error'] = true;
		$response['message'] = "Sorry! some details are missing";
	}
} catch (PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message'] = $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>