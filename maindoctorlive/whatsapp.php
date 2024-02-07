<?php
// $bodytext = "Ms. Sarayu,receipt";
// $buttontext = "i?i=TU9QQjIzMDQwOTg0Mg==";
// $mobileno = "917702919740";
// $templateid = "patient_medical_reports";
// whatsapp($bodytext,$buttontext,$mobileno,$templateid);
function whatsapp($bodytext,$buttontext,$mobileno,$templateid){
  
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.medicoverhospitals.in/whatsapp/template',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "accesskey":"Bn8qR9[4-H3lxJfb",
    "bodytext": "'.$bodytext.'",
    "buttontext":"'.$buttontext.'",
    "mobileno":"'.$mobileno.'",
    "templateid":"'.$templateid.'"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
}  
?>