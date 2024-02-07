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
$accesskey= $data->accesskey;
$keyword= $data->keyword;
$response = array();
try{
if(!empty($accesskey) &&!empty($keyword)){
$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	$stmt1 = $pdoread->prepare("SELECT if(attender_name='','-----',attender_name) AS attender_name,`admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt1->bindParam(':search', $keyword, PDO::PARAM_STR);
	$stmt1->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
	$data = $stmt1 -> fetch(PDO::FETCH_ASSOC);
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
		if ($lang=='Hindi'){
	    $response['message1']="<!DOCTYPE html>
<html>
<body>

<p align='justify'>मैं , <b><font color='#000000'>".$data['patientname']." </font><b>&nbsp;&nbsp; आयु &nbsp;&nbsp;<b><font color='#000000'>".$data['Age']." </font><b>&nbsp;&nbsp;  लिंग&nbsp;&nbsp; <b><font color='#000000'>".$data['gender']." </font><b>&nbsp;&nbsp; रिश्ता S/o or w/o or D/o या स्व &nbsp;&nbsp; <b><font color='#000000'>".$data['attender_name']." </font><b> &nbsp;&nbsp;(रोगी प्रतिनिधि का नाम) संचार के लिए पता&nbsp;&nbsp; <b><font color='#000000'>".$data['address']." </font><b> &nbsp;&nbsp; फोन नंबर। &nbsp;<b><font color='#000000'>".$data['mobile']." </font><b>&nbsp;&nbsp;  (Please give full and particulars of the patient . subsequent alteration in Name & age etc will not be possible) so I hereby give consent for myself or for the above-mentioned patient for the following:</p>
<p align='justify'>a) रोगी के निदान और उपचार के लिए मेडिकवर अस्पताल के चिकित्सा, नर्सिंग और तकनीकी कर्मचारियों द्वारा जांच आवश्यक है।</p>
<p align='justify'>b) मेडिकवर अस्पताल के चिकित्सा या नर्सिंग स्टाफ द्वारा उपचार के लिए आवश्यक समझी जाने वाली किसी भी दवा का प्रशासन।</b>
<p align='justify'>c) उपचार के किसी भी समय के दौरान हमारे डॉक्टर द्वारा उचित समझे जाने पर किसी अन्य अस्पताल में स्थानांतरित होने की सहमति अगर इलाज करने वाले डॉक्टर को लगता है कि यह रोगी के ठीक होने के लिए आवश्यक है।</p>
<p align='justify'>मैं/हम अस्पताल के नियमों और विनियमों का पालन करने के लिए सहमत हूं/हैं, जिसमें आने का समय, बाहर का खाना, धूम्रपान न करना और शराब नहीं पीना शामिल है, जो अस्पताल प्रबंधन द्वारा हमें बताए गए हैं। मैं/हम यह भी सुनिश्चित करेंगे कि मुझसे (या) रोगी से संबंधित अन्य सभी इन नियमों का पालन करते हैं।</p>
<p align='justify'>यदि रोगी की बीमा कंपनी/टीपीए अस्पताल के बिलों के आंशिक या पूर्ण भुगतान को बढ़ाने या इनकार करने से इनकार करती है, तो रोगी को अस्पताल के बिल की पूरी शेष राशि का भुगतान नकद भुगतान, डिमांड ड्राफ्ट, या क्रेडिट कार्ड द्वारा करना होगा।</p>
<p align='justify'>मैं/हम अस्पताल को सभी बकाया चुकाने की पूरी जिम्मेदारी लेंगे और आपातकालीन उपचार को पूरा करने के लिए पर्याप्त अग्रिम राशि जमा करने का आश्वासन देंगे। भुगतान मांग अस्पताल उपचार व्यय के अनुसार किया जाएगा।<p>
<p align='justify'>जब उपचार करने वाले डॉक्टर (या) टीम द्वारा नैदानिक ​​परामर्श किया जाता है और बिलिंग टीम द्वारा वित्तीय परामर्श किया जाता है तो मैं/हम रोगी की जिम्मेदारी लेने के लिए अस्पताल परिसर में उपलब्ध रहूंगा।<p>
<p align='justify'>मैं/हम एतद्वारा यह भी प्रमाणित करते हैं कि ऊपर दी गई सभी जानकारी सुश्री/श्रीमती द्वारा मेरी अपनी भाषा में पढ़ी और समझाई गई हैं। (नाम) मेडिकवर अस्पताल। मैं/हम इस सहमति पर पूरी तरह सतर्क मन की स्थिति में अपनी मर्जी से हस्ताक्षर करते हैं।<p>
<p align='justify'>चूंकि रोगी सहमति देने में असमर्थ है क्योंकि (कारण), मैं (रोगी के साथ नाम और संबंध) उसकी ओर से सहमति देता/देती हूं।</p>


</body>
</html>";}
else if ($lang=='Telugu'){
$response['message1']="<!DOCTYPE html>
<html>
<body>

<p align='justify'>నేను,  <b><font color='#000000'>".$data['patientname']." </font><b>&nbsp;&nbsp; వయస్స &nbsp;&nbsp;<b><font color='#000000'>".$data['Age']." </font><b>&nbsp;&nbsp;  సెక్స్&nbsp;&nbsp; <b><font color='#000000'>".$data['gender']." </font><b>&nbsp;&nbsp; సంబంధం S/o or w/o or D/o లేదా నేనే &nbsp;&nbsp; <b><font color='#000000'>".$data['attender_name']." </font><b> &nbsp;&nbsp;(రోగి ప్రతినిధి పేరు) కమ్యూనికేషన్ కోసం చిరునామా&nbsp;&nbsp; <b><font color='#000000'>".$data['address']." </font><b> &nbsp;&nbsp; మొబైల్ నంబర్ &nbsp;<b><font color='#000000'>".$data['mobile']." </font><b>&nbsp;&nbsp;  (దయచేసి రోగి యొక్క పూర్తి మరియు వివరాలను ఇవ్వండి. పేరు మరియు వయస్సు మొదలైన వాటిలో తదుపరి మార్పు సాధ్యం కాదు) కాబట్టి నేను ఈ క్రింది వాటికి నా కోసం లేదా పైన పేర్కొన్న రోగి కోసం సమ్మతిని తెలియజేస్తున్నాను:</p>
<p align='justify'>a) రోగి యొక్క రోగనిర్ధారణ మరియు చికిత్స కోసం అవసరమైన మెడికోవర్ ఆసుపత్రి యొక్క వైద్య, నర్సింగ్ మరియు సాంకేతిక సిబ్బంది పరిశోధనలు.</p>
<p align='justify'>b) మెడికోవర్ ఆసుపత్రికి చెందిన వైద్య లేదా నర్సింగ్ సిబ్బంది ఏదైనా మందుల నిర్వహణ, చికిత్సకు అవసరమైనదిగా భావించబడుతుంది.</b>
<p align='justify'>c) చికిత్స చేసే వైద్యుడు రోగి కోలుకోవడానికి ఇది అవసరమని భావిస్తే, ఏ సమయంలోనైనా మా వైద్యుడు సరిపోతుందని భావించిన ఇతర ఆసుపత్రికి బదిలీ చేయడానికి మరింత సమ్మతి.</p>
<p align='justify'>ఆసుపత్రి నిర్వహణ ద్వారా మాకు తెలియజేయబడిన సందర్శన వేళలు, బయటి ఆహారం, ధూమపానం మరియు మద్య పానీయాలు వంటి వాటితో సహా ఆసుపత్రి నియమాలు మరియు నిబంధనలకు కట్టుబడి ఉండటానికి నేను/మేము అంగీకరిస్తున్నాము. నేను/మేము నాకు సంబంధించిన (లేదా) రోగి ఈ నిబంధనలకు కట్టుబడి ఉండేలా కూడా నిర్ధారిస్తాము.</p>
<p align='justify'>రోగి యొక్క బీమా కంపెనీ/TPA ఆసుపత్రి బిల్లులను పాక్షికంగా లేదా పూర్తిగా చెల్లించడానికి నిరాకరించిన లేదా తిరస్కరించిన సందర్భంలో, రోగి ఆసుపత్రి బిల్లు మొత్తం బ్యాలెన్స్‌ను నగదు చెల్లింపు, డిమాండ్ డ్రాఫ్ట్ లేదా క్రెడిట్ కార్డ్ ద్వారా చెల్లించాలి.</p>
<p align='justify'>నేను/మేము ఆసుపత్రికి బకాయిలన్నింటిని క్లియర్ చేయడానికి పూర్తి బాధ్యత తీసుకుంటాము మరియు అత్యవసర చికిత్సకు తగిన ముందస్తు మొత్తాన్ని డిపాజిట్ చేస్తామని హామీ ఇస్తాము. ఆసుపత్రి చికిత్స ఖర్చుల ప్రకారం చెల్లింపు అభ్యర్థనలు చేయబడతాయి.<p>
<p align='justify'>చికిత్స చేసే డాక్టర్ (లేదా) బృందం ద్వారా క్లినికల్ కౌన్సెలింగ్ మరియు బిల్లింగ్ బృందం ఆర్థిక సలహాలు ఇచ్చినప్పుడు రోగికి బాధ్యత వహించడానికి నేను/మేము ఆసుపత్రి ఆవరణలో అందుబాటులో ఉంటాము.<p>
<p align='justify'>పైన ఇచ్చిన సమాచారం అంతా శ్రీమతి/శ్రీమతి ద్వారా నా స్వంత భాషలో చదివి నాకు వివరించబడిందని నేను/మేము కూడా దీని ద్వారా ధృవీకరిస్తున్నాము. మెడికోవర్ హాస్పిటల్ (పేరు). నేను/మేము పూర్తిగా అప్రమత్తమైన మానసిక స్థితిలో మా స్వంత ఇష్టానుసారం ఈ సమ్మతిని సంతకం చేస్తాము.<p>
<p align='justify'>రోగి సమ్మతించలేనందున (కారణం), నేను (పేషెంట్‌తో పేరు మరియు సంబంధం) అతని/ఆమె తరపున సమ్మతిని తెలియజేస్తున్నాను.</p>
</body>
</html>";}
else {
	$response['message1']="<!DOCTYPE html>
	<html>
	<body>
	
	<p align='justify'>I , <b><font color='#000000'>".$data['patientname']." </font><b>&nbsp;&nbsp; Age &nbsp;&nbsp;<b><font color='#000000'>".$data['Age']." </font><b>&nbsp;&nbsp;  Sex&nbsp;&nbsp; <b><font color='#000000'>".$data['gender']." </font><b>&nbsp;&nbsp; Relation S/o or w/o or D/o or Self &nbsp;&nbsp; <b><font color='#000000'>".$data['attender_name']." </font><b> &nbsp;&nbsp;(Patient Representative name) Address for communication&nbsp;&nbsp; <b><font color='#000000'>".$data['address']." </font><b> &nbsp;&nbsp; Phone No. &nbsp;<b><font color='#000000'>".$data['mobile']." </font><b>&nbsp;&nbsp;  (Please give full and particulars of the patient . subsequent alteration in Name & age etc will not be possible) so I hereby give consent for myself or for the above-mentioned patient for the following:</p>
	<p align='justify'>a) Investigations by medical, nursing, and technical staff of Medicover hospital necessary for the
	diagnosis and treatment of the patient.</p>
	<p align='justify'>b) Administration of any medication by medical or nursing staff of Medicover hospital as deemed
	necessary for treatment.</b>
	<p align='justify'>c) Further consent to being transferred to any other hospital as considered fit by our doctor during any time of treatment if the treating doctor feels it is essential for the patient's recovery.</p>
	<p align='justify'>I/we agree to abide by the rules and regulations of the hospital, including visiting hours, outside food, no smoking, and no alcoholic drinks, which are conveyed to us by the hospital management. I/we will also ensure that all others related to me (or) the patient abide by these rules.</p>
	<p align='justify'>In the event that the patient's insurance company/TPA refuses to extend or denies payment of the
	hospital bills in part or in full, the patient shall pay the entire balance of the hospital bill by cash payment, demand draft, or credit card.</p>
	<p align='justify'>I/we shall take full responsibility for clearing all dues to the hospital and assure the deposit of sufficient advance amount to meet emergency treatment. Payment requisitions will be made according to the hospital treatment expenditures.<p>
	<p align='justify'>I/we will be available on the hospital premises to take responsibility for the patient when clinical counseling is done by the treating doctor (or) team and financial counseling is done by the billing team.<p>
	<p align='justify'>I/we also hereby certify that all the above-given information has been read over and explained to me in my own language by Ms/Mrs. (Name) of Medicover Hospital. I/we sign this consent on our own free will in a fully alert state of mind.<p>
	<p align='justify'>Since the patient is unable to consent because (reason), I (name and relationship with the patient) give consent on his/her behalf.</p>
	</body>
	</html>";	
}
     }
	 else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="No Data Found!";
     }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
	}	
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdoread = null;
?>