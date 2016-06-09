


<?php

function authen(){
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt ($ch, CURLOPT_URL, "http://sugang.skku.edu/skku/login?attribute=loginChk&lang=KO&id=2013313541&pwd=dudrhkdud25");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPGET, true);

$contents = curl_exec($ch);
curl_close($ch);

echo $contents;


//$contents = '{"code":"500","msg":"패스워드가 일치하지 않거나, 학번이 존재하지 않습니다.\nID를 학번으로 넣으셨는지 확인하시고 로그인 하여 주십시오.","token":"F8F9B92FEE80EA601191B229E47C86C5"}';
$data = json_decode(utf8_encode($contents), TRUE);
// display file

$da = $data['code'];
//echo "<script>alert('da');</script>";
}

?>

<div class="btn_conf">
        <input type="Submit" value="인증" onclick="authen()">
    </div>