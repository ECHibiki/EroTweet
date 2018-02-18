<?php 

$GLOBALS['oauth_data'] = array();

function randomAlphaNumet($len){
	$rand_string = "";
	$opt_str = "1234567890qwertyuiopasdfghjklzxcvbnmZXCVBNMASDFGHJKLQWERTYUIOP";
	$options = str_split($opt_str);
	$max = mb_strlen($opt_str) - 1;
	for($char = 0 ; $char < $len ; $char++){
		 $rand_string .= $options[rand(0, $max)];
	}
	return $rand_string;
}

function buildOAuthParamaterString($param_arr){
	$param_string = "";
	$start = true;
	foreach($param_arr as $key => $param){
		if($start){
			$start=false;
		}
		else{
			$param_string .= urlencode("&");
		}
		$param_string .=  urlencode($key . "=" . $param);
	}
	return $param_string; 		
}

function generateSignature($request, $params, $secrets){	
	  // BUILD SIGNATURE
	$request_method = strtoupper($request["request_method"]);
	$base_url = rawurlencode($request["base_url"]);
	
	ksort($params);
	if(isset($params["media"])) $params["media"] = urlencode($params["media"]);
	
	$paramter_string = buildOAuthParamaterString($params); 		
			
	$base_string = ($request_method . "&" .  $base_url  . "&" . $paramter_string);
						
	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	
	$signature =  (base64_encode(hash_hmac("SHA1",$base_string, $secret_string, true)));	
	return $signature;	
};

function getMediaID($binary_file, $size, $mime){
	$media_api = "https://upload.twitter.com/1.1/media/upload.json";

	$consumer_key = $GLOBALS['oauth_data']["consumer_key"];
	$random_value = randomAlphaNumet(32);
	$random_value =  str_replace("/", "5", str_replace("=", "2", $random_value));
	$method = "HMAC-SHA1";
	$timestamp = time();
	$access_token =  $GLOBALS['oauth_data']["access_token"];
	$oauth_version = "1.0";
	//Secrets
	$consumer_secret = $GLOBALS['oauth_data']["consumer_secret"];
	$oauth_secret = $GLOBALS['oauth_data']["oauth_secret"];
	
	echo "$oauth_secret -- $consumer_secret -- $access_token -- $consumer_key<br/>";
	
			echo "<br/><br/>";
	/////////////MAKE INIT////////////
	//post data
	$command = "INIT";
	$postfield_string = "command=$command&total_bytes=$size&media_type=$mime";
	$msg_len = (strlen($postfield_string));
	
	//header data
		  // BUILD SIGNATURE			
		$signature =   urlencode(generateSignature(array(
									"base_url" => $media_api,
									"request_method" => "POST"),
									array(
									"command" => "$command",
									"total_bytes" => "$size",
									"media_type" => "$mime",
									"oauth_version" => "$oauth_version",
									"oauth_nonce" => "$random_value",
									"oauth_token" => "$access_token",
									"oauth_timestamp" => "$timestamp",
									"oauth_consumer_key" => "$consumer_key",
									"oauth_signature_method" => "$method"
									),
								array(
									"consumer_secret" => "$consumer_secret",
									"oauth_secret" => "$oauth_secret"
									)
								));
									

	
	$header_data = array("Accept: */*", "Connection: close","User-Agent: VerniyXYZ-CURL" ,"Content-Transfer-Encoding: binary",
												"Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
												"Content-Length: $msg_len", "Host: upload.twitter.com",
"Authorization: OAuth oauth_consumer_key=\"$consumer_key\",oauth_nonce=\"$random_value\",oauth_signature=\"$signature\",oauth_signature_method=\"$method\",oauth_timestamp=\"$timestamp\",oauth_token=\"$access_token\",oauth_version=\"$oauth_version\""																				
												);									
	//request
	
	$curl = curl_init($media_api);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$media_id_arr = json_decode(curl_exec($curl), true);
	print_r ($media_id_arr);
	$media_id = $media_id_arr["media_id_string"];

			echo "<br/><br/>";
		/////////////MAKE APPEND////////////
			//post data
	$command = "APPEND";
	$segment_index = 0;
	
	
	//header data
		  // BUILD SIGNATURE			
	$signature =  urlencode(generateSignature(array(
								"base_url" => $media_api,
								"request_method" => "POST"),
								array(
								"command" => "$command",
								"media" => "$binary_file",
								"media_id"=>"$media_id",
								"segment_index"=>"$segment_index",
								"oauth_version" => "$oauth_version",
								"oauth_nonce" => "$random_value",
								"oauth_token" => "$access_token",
								"oauth_timestamp" => "$timestamp",
								"oauth_consumer_key" => "$consumer_key",
								"oauth_signature_method" => "$method"
								),
							array(
								"consumer_secret" => "$consumer_secret",
								"oauth_secret" => "$oauth_secret"
								)
							));
									

	$postfield_string = "media=" . urlencode($binary_file) . "&command=$command&media_id=$media_id&segment_index=$segment_index" ;
	$msg_len = (strlen($postfield_string));
	echo $msg_len . "<br/>";
	$header_data = array("Except:", "Connection: close","User-Agent: VerniyXYZ-CURL" ,"Content-Transfer-Encoding: binary",
												"Content-Type: application/x-www-form-urlencoded", 
												"Content-Length: $msg_len", "Host: upload.twitter.com",
"Authorization: OAuth oauth_consumer_key=\"$consumer_key\",oauth_nonce=\"$random_value\",oauth_signature=\"$signature\",oauth_signature_method=\"$method\",oauth_timestamp=\"$timestamp\",oauth_token=\"$access_token\",oauth_version=\"$oauth_version\""																				
												);									
	//request
	$curl = curl_init($media_api);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);

	echo  "<br/>";
	
	curl_setopt($curl, CURLOPT_HEADER  , true);  // we want headers
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$http_response = curl_exec($curl);
	echo $http_response;

		/////////////MAKE FINAL/
		
		$command = "FINALIZE";
		
		$signature =  urlencode(generateSignature(array(
								"base_url" => $media_api,
								"request_method" => "POST"),
								array(
								"command" => "$command",
								"media_id"=>"$media_id",
								"oauth_version" => "$oauth_version",
								"oauth_nonce" => "$random_value",
								"oauth_token" => "$access_token",
								"oauth_timestamp" => "$timestamp",
								"oauth_consumer_key" => "$consumer_key",
								"oauth_signature_method" => "$method"
								),
							array(
								"consumer_secret" => "$consumer_secret",
								"oauth_secret" => "$oauth_secret"
								)
							));
		$postfield_string = "command=$command&media_id=$media_id" ;
	$msg_len = (strlen($postfield_string));
	echo $msg_len . "<br/>";
	$header_data = array("Except:", "Connection: close","User-Agent: VerniyXYZ-CURL" ,"Content-Transfer-Encoding: binary",
												"Content-Type: application/x-www-form-urlencoded", 
												"Content-Length: $msg_len", "Host: upload.twitter.com",
"Authorization: OAuth oauth_consumer_key=\"$consumer_key\",oauth_nonce=\"$random_value\",oauth_signature=\"$signature\",oauth_signature_method=\"$method\",oauth_timestamp=\"$timestamp\",oauth_token=\"$access_token\",oauth_version=\"$oauth_version\""																				
												);									
	//request
	$curl = curl_init($media_api);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);

	echo  "<br/>";
	
	curl_setopt($curl, CURLOPT_HEADER  , true);  // we want headers
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$http_response = curl_exec($curl);
	echo $http_response;
	
			echo "<hr/>";
			return 	$media_id ;
	
}

function makeTweet($comment, $file_arr){
	/*
		1. Create a set of media ID's.
		2. Add to $postfield_string if there's an image
		3. Authenticate post and submit it to Twitter
	*/
	
	//authorization details
$settings = fopen("settings/keys.ini","r");
while(!feof($settings)){
	$line = fgets($settings);
	$key = substr($line,0,strpos($line, ":"));
			//eat last character
	$value = trim(substr($line, strpos($line, ":")+1));
	$GLOBALS['oauth_data'][$key] = $value;
}
	
	print_r($GLOBALS['oauth_data']);
	

	
//image info
$image_string = "";//delimited by ',' commas
for($file = 0 ; $file < 4 ; $file++){
	if($file_arr[$file] != "0"){
		echo "<hr/>$file: $file_arr[$file] -- "; 
		//create data in binary/b64
		$type = pathinfo($file_arr[$file], PATHINFO_EXTENSION);
		$binary = file_get_contents($file_arr[$file]);
		//$base64 = 'data:image/' . $type . ';base64,' . base64_encode($binary);
		$base64 = base64_encode($binary);
		//echo $base64;
		//upload file to twitter and get id for use in files?
		$size = filesize($file_arr[$file]);
		if($file == 0)
			$image_string = getMediaID($base64, $size, 'image/' . $type);
		else
			$image_string .= "," . getMediaID($base64, $size, 'image/' . $type);		
	}
}

echo ("IMAGE-STRING: " . $image_string);
$image_string = urlencode($image_string);
			
			
//access info
$access_url = "https://api.twitter.com/1.1/statuses/update.json";
$request_method = "POST";

//message info
$encode_tweet_msg = rawurlencode($comment);
$include_entities = "true";

//append to postfield_string the media code via media_ids=$media_id
$postfield_string = "include_entities=$include_entities&status=$encode_tweet_msg&media_ids=$image_string";
$msg_len = (strlen($postfield_string));


echo " <br/>$postfield_string<br/>";

$consumer_key = $GLOBALS['oauth_data']["consumer_key"];
$random_value = randomAlphaNumet(32);
$random_value =  str_replace("/", "5", str_replace("=", "2", $random_value));
$method = "HMAC-SHA1";
$timestamp = time();
$access_token =  $GLOBALS['oauth_data']["access_token"];
$oauth_version = "1.0";

//Secrets
$consumer_secret = $GLOBALS['oauth_data']["consumer_secret"];
$oauth_secret = $GLOBALS['oauth_data']["oauth_secret"];

				//add media id to the signature
$signature = rawurlencode(generateSignature(array(
									"base_url" => $access_url,
									"request_method" => $request_method),
									array("include_entities" => "$include_entities",
									"status" => "$encode_tweet_msg",
									"media_ids" => "$image_string",
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
echo "--<hr/>";
$content = curl_exec($curl);
echo $content;
}
echo"run script from externals";
?>
