<?php 
	//echo file_get_contents();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($ch, CURLOPT_URL, $_POST['address']);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPGET, true);

	$contents = curl_exec($ch);
	curl_close($ch);

	echo $contents;
?>