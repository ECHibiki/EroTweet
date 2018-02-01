<?php 
function statusEncodeCustom($value){
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
		$value = rawurlencode($value);

		$value  = str_replace(':', '%3A', $value);
		$value  = str_replace('/', '%2F', $value);
		$value  = str_replace('!', '%21', $value);
		$value  = str_replace('=', '%3D', $value);
		$value  = str_replace('&', '%26', $value);
		
		return $value;
}
function generateSingature($request, $message, $params, $secrets){	
	  // BUILD SIGNATURE
	$request_method = strtoupper($request["request_method"]);
	$base_url = urlEncodeCustom($request["base_url"]);
	$paramter_string = urlEncodeCustom("include_entities=". $message["include_entities"] . "&media_id=" . $message["media_id"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 "&oauth_version=" .$params["oauth_version"]	 . "&status=" . $message["status"]) . "";	
	
	$base_string = ($request_method . "&" .  $base_url  . "&" . $paramter_string);
			
	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	
	$signature =  strtoupper(hash_hmac("SHA1",$base_string, $secret_string));	
	
	$split_hex_signature = "";
	foreach(str_split($signature, 2) as $split){
		$split_hex_signature .= chr(hexdec($split));
	}

	return base64_encode($split_hex_signature);
}

function makeTweet($comment, $file_arr){
	/*
		1. Create a set of media ID's.
		2. Add to $postfield_string if there's an image
		3. Authenticate post and submit it to Twitter
	*/
	
//image info
$image_string = "";//delimited by ',' commas
for($i = 0 ; $i < 4 ; $i++){
	if($file_arr[$i] != 0){
		$file_arr[$i] = 0;
		//create data in binary/b64
		//upload file to twitter and get id for use in files?
	}
}

//access info
$access_url = "https://api.twitter.com/1.1/statuses/update.json";
$request_method = "POST";

//message info
$encode_tweet_msg = statusEncodeCustom($comment);
$include_entities = "true";

//append to postfield_string the media code via media_id=$media_id
$postfield_string = "include_entities=$include_entities&status=$encode_tweet_msg&media_id=$image_string";
$msg_len = (strlen($postfield_string));


echo " <br/> <br/>$comment <br/> $encode_tweet_msg <br/> $msg_len<br/>$file_arr<br/>";

//authorization details
$settings = fopen("keys.txt","r");
$keys = array();
while(!feof($settings)){
	$line = fgets($settings);
	$key = substr($line,0,strpos($line, ":"));
			//eat last character
	$value = trim(substr($line, strpos($line, ":")+1));
	$keys[$key] = $value;
}
$consumer_key = $keys["consumer_key"];
$random_value = substr(( base64_encode(rand(1000000000,10000000000000)) . base64_encode(rand(1000000000,10000000000000)) . base64_encode(rand(1000000000000,100000000000000))), 0, 32);
$random_value =  str_replace("/", "5", str_replace("=", "2", $random_value));
$method = "HMAC-SHA1";
$timestamp = time();
$access_token =  $keys["access_token"];
$oauth_version = "1.0";

var_dump($keys);

//Secrets
$consumer_secret = $keys["consumer_secret"];
$oauth_secret = $keys["oauth_secret"];

				//add media id to the signature
$signature = urlEncodeCustom(generateSingature(array(
									"base_url" => $access_url,
									"request_method" => $request_method
									),
								array(
									"include_entities" => "$include_entities",
									"status" => "$encode_tweet_msg",
									"media_id" => "$image_string"
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
										
//request
$curl = curl_init($access_url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
echo "--<br/>";
$content = curl_exec($curl);
echo $content;
}
echo"run script from externals";
?>