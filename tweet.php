<?php 
function encode_custom($value){
		$value = rawurlencode($value);
		$value = str_replace('.', '%2E', $value);
		//$value  = str_replace('-', '%2D', $value);
		$value  = str_replace(':', '%3A', $value);
		$value  = str_replace('/', '%2F', $value);
		$value  = str_replace('!', '%21', $value);
		$value  = str_replace('=', '%3D', $value);
		$value  = str_replace('&', '%26', $value);
		return $value;
}
function urlencode_custom($value){
		$value = rawurlencode($value);
		//$value  = str_replace('-', '%2D', $value);
		$value  = str_replace(':', '%3A', $value);
		$value  = str_replace('/', '%2F', $value);
		$value  = str_replace('!', '%21', $value);
		$value  = str_replace('=', '%3D', $value);
		$value  = str_replace('&', '%26', $value);
		return $value;
}
function generateSingature($request, $message, $params, $secrets){	
	  // BUILD SIGNATURE
	foreach($request as &$value) rawurlencode($value);
	foreach($message as &$value) rawurlencode($value);
	foreach($params as &$value)	rawurlencode($value);
	
	$request_method = strtoupper($request["request_method"]);
	$base_url = urlencode_custom($request["base_url"]);
	$paramter_string = urlencode_custom("include_entities=". $message["include_entities"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 "&oauth_version=" .$params["oauth_version"]	 . "&status=" . $message["status"] . "");
	
	$base_string = $request_method . "&" .  $base_url  . "&" . $paramter_string;

	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	
	$signature =  hash_hmac("SHA1",$base_string, $secret_string);
	return base64_encode($signature);
}

//access info
$access_url = "https://api.twitter.com/1.1/statuses/update.json";
$request_method = "POST";

//message info
$raw_tweet_msg = "testing";
$encode_tweet_msg = encode_custom($raw_tweet_msg);
$include_entities = "true";
$postfield_string = "include_entities=$include_entities&status=$encode_tweet_msg";
$msg_len = (strlen($postfield_string));

//authorization details
$consumer_key = "3LGqPCNXoBQAL1vCvWuHRf3fZ";
$random_value = str_replace("=", "2", base64_encode(rand(10000000000,1000000000000)));
$method = "HMAC-SHA1";
$timestamp = time();
$access_token = "957789221321871366-0atD8nPz8Egs64UnrEM8pQiW2s8ry7T";
$oauth_version = "1.0";

//Secrets
$consumer_secret = "--";
$oauth_secret = "--";

$signature = generateSingature(array(
									"base_url" => $access_url,
									"request_method" => $request_method
									),
								array(
									"include_entities" => "$include_entities",
									"status" => "$raw_tweet_msg"
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
);

//request
$curl = curl_init($access_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: */*", "Connection: close","User-Agent: VerniyXYZ-CURL" ,
												"Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
												"Content-Length: $msg_len", "Host: api.twitter.com",
												
												"Authorization:
													OAuth oauth_consumer_key='$consumer_key',
													oauth_nonce='$random_value',
													oauth_signature='$signature',
													oauth_signature_method='$method',
													oauth_timestamp='$timestamp',
													oauth_token='$access_token',
													oauth_version='$oauth_version'
													"
												
												));
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);
curl_exec($curl);
echo "<br/>" .  strtoupper(base64_decode($signature) . "") . " +++++[][][][][]+++++ " .  $signature;
echo "<br/>842B5299887E88760212A056AC4EC2EE1626B549 +++++[][][][][]+++++ " . base64_encode("842B5299887E88760212A056AC4EC2EE1626B549");
?>
