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
$unit_type = trim($data->unit_type);
$temp = trim($data->temp);
$temp_sod_low = trim($data->temp_sod_low);
$temp_sod_high = trim($data->temp_sod_high);
$sys_glu_low = trim($data->sys_glu_low);
$sys_glu_high = trim($data->sys_glu_high);
$dia_cre_low = trim($data->dia_cre_low);
$dia_cre_high = trim($data->dia_cre_high);
$heart_bun_low = trim($data->heart_bun_low);
$heart_bun_high = trim($data->heart_bun_high);
$resp_low = trim($data->resp_low);
$resp_high = trim($data->resp_high);
$urine = trim($data->urine);
$sea = trim($data->sea);
$sea_albumin = trim($data->sea_albumin);
$fi_bilirubin = trim($data->fi_bilirubin);
$ph_hct_low = trim($data->ph_hct_low);
$ph_hct_high = trim($data->ph_hct_high);
$po2 = trim($data->po2);   
$wbc_low = trim($data->wbc_low);
$wbc_high = trim($data->wbc_high);
$pco2 = trim($data->pco2);
$crf_type = trim($data->crf_type);
$crf_text = trim($data->crf_text);
$cancer_type = trim($data->cancer_type);
$cancer_text = trim($data->cancer_text);
$aids_type = trim($data->aids_type);
$aids_text = trim($data->aids_text);
$myeloma_type = trim($data->myeloma_type);
$myeloma_text = trim($data->myeloma_text);
$hepatic_type = trim($data->hepatic_type);
$hepatic_text = trim($data->hepatic_text);
$immuno_type = trim($data->immuno_type);
$immuno_text = trim($data->immuno_text);
$lymphoma_type = trim($data->lymphoma_type);
$lymphoma_text = trim($data->lymphoma_text);
$cirrhosis_type = trim($data->cirrhosis_type);
$cirrhosis_text = trim($data->cirrhosis_text);
$admitteedform = trim($data->admitteedform);
$icu = trim($data->icu);
$post_operative = trim($data->post_operative);
$surgery = trim($data->surgery);
$readmission = trim($data->readmission);
$ventilated = trim($data->ventilated);
$apache = trim($data->apache);
$aps = trim($data->aps);
$logit = trim($data->logit);
$disease = trim($data->disease);
$mortality = trim($data->mortality);
$los = trim($data->los);

try{
    
    if(!empty($accesskey) && !empty($ipno) && !empty($umrno) && !empty($unit_type)){
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
              //check if patient discharged or not
   $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
   $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
   $validate -> execute();
   $validates = $validate->fetch(PDO::FETCH_ASSOC);
   if($validate -> rowCount() > 0) {

       if($unit_type == 'Conventional Units'){
        $wbc_high='';
        $wbc_low='';
        $urine='';
        }else{
        $temp='';
        $resp_high='';
        $resp_low='';
        $pco2='';
        $po2='';
        $sea='';
       }     
    
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `apache_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `unit_type`, `temp`, `temp_sod_low`, `temp_sod_high`, `sys_glu_low`, `sys_glu_high`, `dia_cre_low`, `dia_cre_high`, `heart_bun_low`, `heart_bun_high`, `resp_low`, `resp_high`, `urine`,`sea`, `sea_albumin`, `fi_bilirubin`, `ph_hct_low`, `ph_hct_high`, `po2`, `wbc_low`, `wbc_high`, `pco2`, `crf_type`, `crf_text`, `cancer_type`, `cancer_text`, `aids_type`, `aids_text`, `myeloma_type`, `myeloma_text`, `hepatic_type`, `hepatic_text`, `immuno_type`, `immuno_text`, `lymphoma_type`, `lymphoma_text`, `cirrhosis_type`, `cirrhosis_text`, `admitteedform`, `icu`, `post_operative`, `surgery`, `readmission`, `ventilated`, `apache`, `aps`, `logit`, `disease`, `mortality`, `los`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :unit_type, :temp, :temp_sod_low, :temp_sod_high, :sys_glu_low, :sys_glu_high, :dia_cre_low, :dia_cre_high, :heart_bun_low, :heart_bun_high, :resp_low, :resp_high, :urine, :sea, :sea_albumin,  :fi_bilirubin, :ph_hct_low, :ph_hct_high, :po2, :wbc_low, :wbc_high, :pco2, :crf_type, :crf_text, :cancer_type, :cancer_text, :aids_type, :aids_text, :myeloma_type, :myeloma_text, :hepatic_type, :hepatic_text, :immuno_type, :immuno_text, :lymphoma_type, :lymphoma_text, :cirrhosis_type, :cirrhosis_text, :admitteedform, :icu, :post_operative, :surgery, :readmission, :ventilated, :apache, :aps, :logit, :disease, :mortality, :los, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:cost_center,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':unit_type', $unit_type, PDO::PARAM_STR);
$ninsert->bindParam(':temp', $temp, PDO::PARAM_STR);
$ninsert->bindParam(':temp_sod_low', $temp_sod_low, PDO::PARAM_STR);
$ninsert->bindParam(':temp_sod_high', $temp_sod_high, PDO::PARAM_STR);
$ninsert->bindParam(':sys_glu_low', $sys_glu_low, PDO::PARAM_STR);
$ninsert->bindParam(':sys_glu_high', $sys_glu_high, PDO::PARAM_STR);
$ninsert->bindParam(':dia_cre_low', $dia_cre_low, PDO::PARAM_STR);
$ninsert->bindParam(':dia_cre_high', $dia_cre_high, PDO::PARAM_STR);
$ninsert->bindParam(':heart_bun_low', $heart_bun_low, PDO::PARAM_STR);
$ninsert->bindParam(':heart_bun_high', $heart_bun_high, PDO::PARAM_STR);
$ninsert->bindParam(':resp_low', $resp_low, PDO::PARAM_STR);
$ninsert->bindParam(':resp_high', $resp_high, PDO::PARAM_STR);
$ninsert->bindParam(':urine', $urine, PDO::PARAM_STR);
$ninsert->bindParam(':sea', $sea, PDO::PARAM_STR);
$ninsert->bindParam(':sea_albumin', $sea_albumin, PDO::PARAM_STR);
$ninsert->bindParam(':fi_bilirubin', $fi_bilirubin, PDO::PARAM_STR);
$ninsert->bindParam(':ph_hct_low', $ph_hct_low, PDO::PARAM_STR);
$ninsert->bindParam(':ph_hct_high', $ph_hct_high, PDO::PARAM_STR);
$ninsert->bindParam(':po2', $po2, PDO::PARAM_STR);
$ninsert->bindParam(':wbc_low', $wbc_low, PDO::PARAM_STR);
$ninsert->bindParam(':wbc_high', $wbc_high, PDO::PARAM_STR);
$ninsert->bindParam(':pco2', $pco2, PDO::PARAM_STR);
$ninsert->bindParam(':crf_type', $crf_type, PDO::PARAM_STR);
$ninsert->bindParam(':crf_text', $crf_text, PDO::PARAM_STR);
$ninsert->bindParam(':cancer_type', $cancer_type, PDO::PARAM_STR);
$ninsert->bindParam(':cancer_text', $cancer_text, PDO::PARAM_STR);
$ninsert->bindParam(':aids_type', $aids_type, PDO::PARAM_STR);
$ninsert->bindParam(':aids_text', $aids_text, PDO::PARAM_STR);
$ninsert->bindParam(':myeloma_type', $myeloma_type, PDO::PARAM_STR);
$ninsert->bindParam(':myeloma_text', $myeloma_text, PDO::PARAM_STR);
$ninsert->bindParam(':hepatic_type', $hepatic_type, PDO::PARAM_STR);
$ninsert->bindParam(':hepatic_text', $hepatic_text, PDO::PARAM_STR);
$ninsert->bindParam(':immuno_type', $immuno_type, PDO::PARAM_STR);
$ninsert->bindParam(':immuno_text', $immuno_text, PDO::PARAM_STR);
$ninsert->bindParam(':lymphoma_type', $lymphoma_type, PDO::PARAM_STR);
$ninsert->bindParam(':lymphoma_text', $lymphoma_text, PDO::PARAM_STR);
$ninsert->bindParam(':cirrhosis_type', $cirrhosis_type, PDO::PARAM_STR);
$ninsert->bindParam(':cirrhosis_text', $cirrhosis_text, PDO::PARAM_STR);
$ninsert->bindParam(':admitteedform', $admitteedform, PDO::PARAM_STR);
$ninsert->bindParam(':icu', $icu, PDO::PARAM_STR);
$ninsert->bindParam(':post_operative', $post_operative, PDO::PARAM_STR);
$ninsert->bindParam(':surgery', $surgery, PDO::PARAM_STR);
$ninsert->bindParam(':readmission', $readmission, PDO::PARAM_STR);
$ninsert->bindParam(':ventilated', $ventilated, PDO::PARAM_STR);
$ninsert->bindParam(':apache', $apache, PDO::PARAM_STR);
$ninsert->bindParam(':aps', $aps, PDO::PARAM_STR);
$ninsert->bindParam(':logit', $logit, PDO::PARAM_STR);
$ninsert->bindParam(':disease', $disease, PDO::PARAM_STR);
$ninsert->bindParam(':mortality', $mortality, PDO::PARAM_STR);
$ninsert->bindParam(':los', $los, PDO::PARAM_STR);
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

// } catch(PDOException $e) {
// 	// http_response_code(503);
// 	$response['error'] = true;
// 	$response['messheart']= $e->getMessage();
// }


} catch(PDOException $e) {
	// http_response_code(503);
	$response['error'] = true;
	$response['messheart']= "Connection failed";
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>