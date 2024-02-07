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
$did = trim($data->did);
$ipno = trim($data->ipno);
$umrno = trim($data->umrno);
$diagnosis = trim($data->diagnosis);
$history = trim($data->history);
$machineno =trim($data->machineno);
$dialysis_type = trim($data->dialysis_type);
$total_dialysis = trim($data->total_dialysis);
$total_dialyzer = trim($data->total_dialyzer);
$last_dialysis_date = trim($data->last_dialysis_date);
$reuse = trim($data->reuse);
$dry_weight = trim($data->dry_weight);
$pre_weight = trim($data->pre_weight);
$post_weight = trim($data->post_weight);
$gain_weight = trim($data->gain_weight);
$loss_weight = trim($data->loss_weight);
$timeon = trim($data->timeon);
$timeoff = trim($data->timeoff);
$time_effective = trim($data->time_effective);
$pre_resting_bp = trim($data->pre_resting_bp);
$pre_standard_bp = trim($data->pre_standard_bp);
$pre_temp = trim($data->pre_temp);
$pre_pulse = trim($data->pre_pulse);
$post_resting_bp = trim($data->post_resting_bp);
$post_standard_bp = trim($data->post_standard_bp);
$post_temp = trim($data->post_temp);
$post_pulse = trim($data->post_pulse);
$bld_pre_dialysis = trim($data->bld_pre_dialysis);
$bld_post_dialysis = trim($data->bld_post_dialysis);
$bld_investigation = trim($data->bld_investigation);
$bld_doc_orders = trim($data->bld_doc_orders);
$dialysis_duration = trim($data->dialysis_duration);
$dialysis_uf = trim($data->dialysis_uf);
$dialysis_qb = trim($data->dialysis_qb);
$dialysis_qd = trim($data->dialysis_qd);
$dialysis_na = trim($data->dialysis_na);
$dialysis_k = trim($data->dialysis_k);
$dialysis_qinf = trim($data->dialysis_qinf);
$dialysis_cv = trim($data->dialysis_cv);
$medication = trim($data->medication);
$useno = trim($data->useno);
$blous = trim($data->blous);
$heparinization = trim($data->heparinization);
$heparin = trim($data->heparin);
$units_hour = trim($data->units_hour);
$fistula = trim($data->fistula);
$subclavian = trim($data->subclavian);
$jugular = trim($data->jugular);
$femoral = trim($data->femoral);
$tpc1 = trim($data->tpc1);
$tpc2 = trim($data->tpc2);
$priming = trim($data->priming);
$starts = trim($data->starts);
$closing = trim($data->closing);
$washing = trim($data->washing);
$discharge = trim($data->discharge);
$discharge_text = trim($data->discharge_text);
$dialyzer = trim($data->dialyzer);
$tubing = trim($data->tubing);
$ivset = trim($data->ivset);
$avf = trim($data->avf);
$ns1000 = trim($data->ns1000);
$ns500 = trim($data->ns500);
$cc10 = trim($data->cc10);
$cc5 = trim($data->cc5);
$protector = trim($data->protector);
$technical_incharge = trim($data->technical_incharge);
$nurse_incharge = trim($data->nurse_incharge);

try{ 
    
    if(!empty($accesskey) && !empty($ipno) && !empty($umrno) && !empty($diagnosis)){
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){

            $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
            $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
            $validate -> execute();
            $validates = $validate->fetch(PDO::FETCH_ASSOC);
            if($validate -> rowCount() > 0){

            $didcheck = $pdoread -> prepare("SELECT `did` FROM `dialysis_record` WHERE `did` = :did ");
            $didcheck->bindParam(':did', $did, PDO::PARAM_STR);
            $didcheck -> execute();
            $results = $didcheck->fetch(PDO::FETCH_ASSOC);
            if($didcheck -> rowCount() > 0){
                $response['error']=true;
                $response['message']="Data Already Exists On :".$did;
            }else{
            
    
        
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `dialysis_record`(`sno`, `did`,`ipno`, `umrno`, `diagnosis`, `history`, `machineno`, `dialysis_type`, `total_dialysis`, `total_dialyzer`, `last_dialysis_date`, `reuse`, `dry_weight`, `pre_weight`, `post_weight`, `gain_weight`, `loss_weight`, `timeon`, `timeoff`, `time_effective`, `pre_resting_bp`, `pre_standard_bp`, `pre_temp`, `pre_pulse`, `post_resting_bp`, `post_standard_bp`, `post_temp`, `post_pulse`, `bld_pre_dialysis`, `bld_post_dialysis`, `bld_investigation`, `bld_doc_orders`, `dialysis_duration`, `dialysis_uf`, `dialysis_qb`, `dialysis_qd`, `dialysis_na`, `dialysis_k`, `dialysis_qinf`, `dialysis_cv`, `medication`, `useno`, `blous`, `heparinization`, `heparin`, `units_hour`, `fistula`, `subclavian`, `jugular`, `femoral`, `tpc1`, `tpc2`, `priming`, `starts`, `closing`, `washing`, `discharge`, `discharge_text`, `dialyzer`, `tubing`, `ivset`, `avf`, `ns1000`, `ns500`, `cc10`, `cc5`, `protector`, `technical_incharge`, `nurse_incharge`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`) VALUES (NULL,:did, :ipno, :umrno, :diagnosis, :history, :machineno, :dialysis_type, :total_dialysis, :total_dialyzer, :last_dialysis_date, :reuse, :dry_weight, :pre_weight, :post_weight, :gain_weight, :loss_weight, :timeon, :timeoff, :time_effective, :pre_resting_bp, :pre_standard_bp, :pre_temp, :pre_pulse, :post_resting_bp, :post_standard_bp, :post_temp, :post_pulse, :bld_pre_dialysis, :bld_post_dialysis, :bld_investigation, :bld_doc_orders, :dialysis_duration, :dialysis_uf, :dialysis_qb, :dialysis_qd, :dialysis_na, :dialysis_k, :dialysis_qinf, :dialysis_cv, :medication, :useno, :blous, :heparinization, :heparin, :units_hour, :fistula, :subclavian, :jugular, :femoral, :tpc1, :tpc2, :priming, :starts, :closing, :washing, :discharge, :discharge_text, :dialyzer, :tubing, :ivset, :avf, :ns1000, :ns500, :cc10, :cc5, :protector, :technical_incharge, :nurse_incharge, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:cost_center) ");
$ninsert->bindParam(':did', $did, PDO::PARAM_STR);
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':diagnosis', $diagnosis, PDO::PARAM_STR);
$ninsert->bindParam(':history', $history, PDO::PARAM_STR);
$ninsert->bindParam(':machineno', $machineno, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_type', $dialysis_type, PDO::PARAM_STR);
$ninsert->bindParam(':total_dialysis', $total_dialysis, PDO::PARAM_STR);
$ninsert->bindParam(':total_dialyzer', $total_dialyzer, PDO::PARAM_STR);
$ninsert->bindParam(':last_dialysis_date', $last_dialysis_date, PDO::PARAM_STR);
$ninsert->bindParam(':reuse', $reuse, PDO::PARAM_STR);
$ninsert->bindParam(':dry_weight', $dry_weight, PDO::PARAM_STR);
$ninsert->bindParam(':pre_weight', $pre_weight, PDO::PARAM_STR);
$ninsert->bindParam(':post_weight', $post_weight, PDO::PARAM_STR);
$ninsert->bindParam(':gain_weight', $gain_weight, PDO::PARAM_STR);
$ninsert->bindParam(':loss_weight', $loss_weight, PDO::PARAM_STR);
$ninsert->bindParam(':timeon', $timeon, PDO::PARAM_STR);
$ninsert->bindParam(':timeoff', $timeoff, PDO::PARAM_STR);
$ninsert->bindParam(':time_effective', $time_effective, PDO::PARAM_STR);
$ninsert->bindParam(':pre_resting_bp', $pre_resting_bp, PDO::PARAM_STR);
$ninsert->bindParam(':pre_standard_bp', $pre_standard_bp, PDO::PARAM_STR);
$ninsert->bindParam(':pre_temp', $pre_temp, PDO::PARAM_STR);
$ninsert->bindParam(':pre_pulse', $pre_pulse, PDO::PARAM_STR);
$ninsert->bindParam(':post_resting_bp', $post_resting_bp, PDO::PARAM_STR);
$ninsert->bindParam(':post_standard_bp', $post_standard_bp, PDO::PARAM_STR);
$ninsert->bindParam(':post_temp', $post_temp, PDO::PARAM_STR);
$ninsert->bindParam(':post_pulse', $post_pulse, PDO::PARAM_STR);
$ninsert->bindParam(':bld_pre_dialysis', $bld_pre_dialysis, PDO::PARAM_STR);
$ninsert->bindParam(':bld_post_dialysis', $bld_post_dialysis, PDO::PARAM_STR);
$ninsert->bindParam(':bld_investigation', $bld_investigation, PDO::PARAM_STR);
$ninsert->bindParam(':bld_doc_orders', $bld_doc_orders, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_duration', $dialysis_duration, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_uf', $dialysis_uf, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_qb', $dialysis_qb, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_qd', $dialysis_qd, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_na', $dialysis_na, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_k', $dialysis_k, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_qinf', $dialysis_qinf, PDO::PARAM_STR);
$ninsert->bindParam(':dialysis_cv', $dialysis_cv, PDO::PARAM_STR);
$ninsert->bindParam(':medication', $medication, PDO::PARAM_STR);
$ninsert->bindParam(':useno', $useno, PDO::PARAM_STR);
$ninsert->bindParam(':blous', $blous, PDO::PARAM_STR);
$ninsert->bindParam(':heparinization', $heparinization, PDO::PARAM_STR);
$ninsert->bindParam(':heparin', $heparin, PDO::PARAM_STR);
$ninsert->bindParam(':units_hour', $units_hour, PDO::PARAM_STR);
$ninsert->bindParam(':fistula', $fistula, PDO::PARAM_STR);
$ninsert->bindParam(':subclavian', $subclavian, PDO::PARAM_STR);
$ninsert->bindParam(':jugular', $jugular, PDO::PARAM_STR);
$ninsert->bindParam(':femoral', $femoral, PDO::PARAM_STR);
$ninsert->bindParam(':tpc1', $tpc1, PDO::PARAM_STR);
$ninsert->bindParam(':tpc2', $tpc2, PDO::PARAM_STR);
$ninsert->bindParam(':priming', $priming, PDO::PARAM_STR);
$ninsert->bindParam(':starts', $starts, PDO::PARAM_STR);
$ninsert->bindParam(':closing', $closing, PDO::PARAM_STR);
$ninsert->bindParam(':washing', $washing, PDO::PARAM_STR);
$ninsert->bindParam(':discharge', $discharge, PDO::PARAM_STR);
$ninsert->bindParam(':discharge_text', $discharge_text, PDO::PARAM_STR);
$ninsert->bindParam(':dialyzer', $dialyzer, PDO::PARAM_STR);
$ninsert->bindParam(':tubing', $tubing, PDO::PARAM_STR);
$ninsert->bindParam(':ivset', $ivset, PDO::PARAM_STR);
$ninsert->bindParam(':avf', $avf, PDO::PARAM_STR);
$ninsert->bindParam(':ns1000', $ns1000, PDO::PARAM_STR);
$ninsert->bindParam(':ns500', $ns500, PDO::PARAM_STR);
$ninsert->bindParam(':cc10', $cc10, PDO::PARAM_STR);
$ninsert->bindParam(':cc5', $cc5, PDO::PARAM_STR);
$ninsert->bindParam(':protector', $protector, PDO::PARAM_STR);
$ninsert->bindParam(':technical_incharge', $technical_incharge, PDO::PARAM_STR);
$ninsert->bindParam(':nurse_incharge', $nurse_incharge, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
}       
            }else{
                $response['error'] = true;
	$response['message']= "You have entered incorrect IP Number / Patient Checked Out";
            }
}else {	
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}


} catch(PDOException $e) {
	// http_response_code(503);
	$response['error'] = true;
	$response['messheart']= "Connection failed";
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>