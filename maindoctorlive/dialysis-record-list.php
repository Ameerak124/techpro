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
$ipno = trim($data->ipno);
$umrno = trim($data->umrno);

try {
    if (!empty($accesskey) && !empty($ipno) && !empty($umrno)) {
       
        $check = $pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {
            $dialysis_id = $pdo->prepare("SELECT IFNULL(MAX(`did`),CONCAT('DR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS did  FROM `dialysis_record` WHERE `did` LIKE '%DR%'");
            $dialysis_id->execute();
            if ($dialysis_id->rowCount() > 0) {
                $pgid = $dialysis_id->fetch(PDO::FETCH_ASSOC);
                $pageid =  $pgid['did'];
                $pgids = ++$pageid;
            }
                       

            //select into the table
            $dr = $pdoread->prepare("SELECT `dialysis_record`.`sno`,`did`, `ipno`, `umrno`, `diagnosis`, `history`, `machineno`, `dialysis_type`, `total_dialysis`, `total_dialyzer`, DATE_FORMAT(`last_dialysis_date`,'%d-%b-%Y') as last_dialysis_date, `reuse`, `dry_weight`, `pre_weight`, `post_weight`, `gain_weight`, `loss_weight`, `timeon`, `timeoff`, `time_effective`, `pre_resting_bp`, `pre_standard_bp`, `pre_temp`, `pre_pulse`, `post_resting_bp`, `post_standard_bp`, `post_temp`, `post_pulse`, `bld_pre_dialysis`, `bld_post_dialysis`, `bld_investigation`, `bld_doc_orders`, `dialysis_duration`, `dialysis_uf`, `dialysis_qb`, `dialysis_qd`, `dialysis_na`, `dialysis_k`, `dialysis_qinf`, `dialysis_cv`, `medication`, `useno`, `blous`, `heparinization`, `heparin`, `units_hour`, `fistula`, `subclavian`, `jugular`, `femoral`, `tpc1`, `tpc2`, `priming`, `starts`, `closing`, `washing`, `discharge`, `discharge_text`, `dialyzer`, `tubing`, `ivset`, `avf`, `ns1000`, `ns500`, `cc10`, `cc5`, `protector`, `technical_incharge`, `nurse_incharge`, DATE_FORMAT(`dialysis_record`.`createdon`,'%d-%b-%Y %h:%i:%s') as createdon, `user_logins`.`username` AS createdby FROM `dialysis_record` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`dialysis_record`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `dialysis_record`.`cost_center`=:cost_center ORDER BY createdon DESC");
            $dr->bindParam(':ipno', $ipno, PDO::PARAM_STR);
            $dr->bindParam(':umrno', $umrno, PDO::PARAM_STR);
            $dr->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $dr->execute();
            if ($dr->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Found";
				 $response['did'] = $pgids;
           
                while($results = $dr->fetch(PDO::FETCH_ASSOC)) {
              $response['dialysisrecordlist'][]= $results;
              }
              
            
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Data Not Found";
				 $response['did'] = $pgids;
            }
        } else {
			http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Access denied!";
        }
    } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry! Some details are missing";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed";
}

echo json_encode($response);
$pdoread = null;
?>
