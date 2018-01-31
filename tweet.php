<?php 

function statusEncodeCustom($value){
		// $value  = str_replace('%', '%25', $value);
		
		$value = rawurlencode($value);
		
		$value = str_replace('.', '%2E', $value);
		$value  = str_replace('-', '%2D', $value);
		$value  = str_replace(':', '%3A', $value);
		$value  = str_replace('/', '%2F', $value);
		$value  = str_replace('=', '%3D', $value);
		$value  = str_replace('+', '%2B', $value);
		$value  = str_replace(',', '%2C', $value);
		$value  = str_replace('!', '%21', $value);
		$value  = str_replace('?', '%3F', $value);
		$value  = str_replace('&', '%26', $value);
		
		return $value;
}
function urlEncodeCustom($value){
		// $value  = str_replace('%', '%25', $value);
	
		$value = rawurlencode($value);

		$value  = str_replace(':', '%3A', $value);
		$value  = str_replace('/', '%2F', $value);
		$value  = str_replace('!', '%21', $value);
		$value  = str_replace('=', '%3D', $value);
		$value  = str_replace('&', '%26', $value);
		
		return $value;
}
function tweetEncode(){}
function generateSingature($request, $message, $params, $secrets){	
	  // BUILD SIGNATURE

	$request_method = strtoupper($request["request_method"]);
	$base_url = urlEncodeCustom($request["base_url"]);
	//echo statusEncodeCustom($message["status"]) . "<br>";
	$paramter_string = urlEncodeCustom("include_entities=". $message["include_entities"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 "&oauth_version=" .$params["oauth_version"]	 . "&status=" . $message["status"]) . "";	
	
	$base_string = ($request_method . "&" .  $base_url  . "&" . $paramter_string);
	echo $base_string . "<br>";
	

	// $request["request_method"] = "POST";
	// $request["base_url"]= "https://api.twitter.com/1.1/statuses/update.json";
	// $message["include_entities"]  = "true";
	// $params["oauth_consumer_key"] = "------";
	// $params["oauth_nonce"] = "-----";
	// $params["oauth_signature_method"] = "HMAC-SHA1";
	// $params["oauth_timestamp"] = "1318622958";
	// $params["oauth_token"] = "----";
	// $params["oauth_version"] = "1.0";
	// $message["status"] = "Hello Ladies + Gentlemen, a signed OAuth request!";
	
	// $request_method = strtoupper($request["request_method"]);
	// $base_url = urlEncodeCustom($request["base_url"]);
	// $paramter_string = urlEncodeCustom("include_entities=". $message["include_entities"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 // . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 // "&oauth_version=" .$params["oauth_version"]	 . "&status=" . statusEncodeCustom($message["status"]) . "");	
	
	// $base_string = $request_method . "&" .  $base_url  . "&" . $paramter_string;
	// echo $base_string . "<br>";
	
	

	
	
			//echo "<br>";
		
		
			
	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	//echo $secret_string . "<br>";
	
	
	// $secrets["consumer_secret"] = "---";
	// $secrets["oauth_secret"] = "--";
	
	// $secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	// echo $secret_string . "<br>";
	
	
		//echo "<br>";
	
	
	$signature =  strtoupper(hash_hmac("SHA1",$base_string, $secret_string));
	//echo $signature . "<br>";
		
		
	// $signature = strtoupper(hash_hmac("SHA1",
	// "POST&https%3A%2F%2Fapi.twitter.com%2F1.1%2Fstatus
	// echo $signature . "<br>";
		// echo "<br>";
	
	
	
	$split_hex_signature = "";
	foreach(str_split($signature, 2) as $split){
		$split_hex_signature .= chr(hexdec($split));
	}
	//echo base64_encode($split_hex_signature) .  " +++++[][][][][]+++++ " . $split_hex_signature . "<br>";
	return base64_encode($split_hex_signature);
}

//access info
$access_url = "https://api.twitter.com/1.1/statuses/update.json";
$request_method = "POST";

//message info
$raw_tweet_msg = "Hello Ladies + Gentlemen, a signed OAuth request!";
$encode_tweet_msg = statusEncodeCustom($raw_tweet_msg);
//echo $encode_tweet_msg . "<br/>";
$include_entities = "true";
$postfield_string = "include_entities=$include_entities&status=$encode_tweet_msg";
$msg_len = (strlen($postfield_string));

//authorization details
$consumer_key = "---";
$random_value = substr(( base64_encode(rand(1000000000,10000000000000)) . base64_encode(rand(1000000000,10000000000000)) . base64_encode(rand(1000000000000,100000000000000))), 0, 32);
$random_value =  str_replace("/", "5", str_replace("=", "2", $random_value));
$method = "HMAC-SHA1";
$timestamp = time();
$access_token = "--";
$oauth_version = "1.0";

//Secrets
$consumer_secret = "---";
$oauth_secret = "---";

$signature = urlEncodeCustom(generateSingature(array(
									"base_url" => $access_url,
									"request_method" => $request_method
									),
								array(
									"include_entities" => "$include_entities",
									"status" => "$encode_tweet_msg"
									),									
								array(
									"oauth_version" => "$oauth_version",
									"oauth_nonce"=>"$random_value",
									"oauth_token"=>"$access_token",
									"oauth_timestamp" => "$timestamp",
									"oauth_consumer_key" => "$consumer_key",
									"oauth_signature_method" => "$method"
									),
								array(
									"consumer_secret" => "$consumer_secret",
									"oauth_secret" => "$oauth_secret"
									)
));

//Miscelaneous

$header_data = array("Accept: */*", "Connection: close","User-Agent: VerniyXYZ-CURL" ,
												"Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
												"Content-Length: $msg_len", "Host: api.twitter.com",
												
"Authorization: OAuth oauth_consumer_key=\"$consumer_key\",oauth_nonce=\"$random_value\",oauth_signature=\"$signature\",oauth_signature_method=\"$method\",oauth_timestamp=\"$timestamp\",oauth_token=\"$access_token\",oauth_version=\"$oauth_version\""
												
												);
												
//var_dump($header_data);

//request
$curl = curl_init($access_url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);
curl_exec($curl);
//echo "<br/>" .  strtoupper(base64_decode($signature) . "") . " +++++[][][][][]+++++ " .  $signature;
?>
