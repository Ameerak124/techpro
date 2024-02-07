<?php


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://10.74.1.234/Launch_Viewer.asp?AccessionNumber=BEG2300000000001&Username=hisuser&Password=hisuser',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>
