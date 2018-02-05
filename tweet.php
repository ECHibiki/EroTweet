<?php 
require_once('lib/twitteroauth.php');

function statusEncodeCustom($value){
		$value = rawurlencode($value);
		
		//$value = str_replace('.', '%2E', $value);
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
	$paramter_string = urlEncodeCustom("include_entities=". $message["include_entities"] . "&media_ids=" . $message["media_ids"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
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
function generateMediaSingatureINIT($request, $message, $params, $secrets){	
  // BUILD SIGNATURE
	$request_method = strtoupper($request["request_method"]);
	$base_url = urlEncodeCustom($request["base_url"]);
	$paramter_string = urlEncodeCustom("command=" . $message["command"] . "&media_type=" . $message["media_type"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 "&oauth_version=" .$params["oauth_version"] . "&total_bytes=" . $message["total_bytes"]); 
	
	$base_string = ($request_method . "&" .  $base_url  . "&" . $paramter_string);
			
	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	
	//using binary production
	$signature =  (hash_hmac("SHA1",$base_string, $secret_string, true));	
	$signature = urlEncodeCustom(base64_encode($signature));
	return $signature;
}
function generateMediaSingatureAPPEND($request, $message, $params, $secrets){	
  // BUILD SIGNATURE
	$request_method = strtoupper($request["request_method"]);
	$base_url = urlEncodeCustom($request["base_url"]);
	//$message["media_data"] = 123;
	$paramter_string = urlEncodeCustom("command=" . $message["command"] . "&media=" . $message["media"] . "&media_id="  . $message["media_id"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 "&oauth_version=" .$params["oauth_version"] . "&segment_index=" . $message["segment_index"]); 

	
	// echo ("command=" . $message["command"] . "&media=" . $message["media"] . "&media_id="  .  $message["media_id"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 // . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 // "&oauth_version=" .$params["oauth_version"] . "&segment_index=" . $message["segment_index"]) . "<br><br>"; 
	
	
	// echo "<hr>" . $message["media"] . "<hr>";
	
	
	$base_string = ($request_method . "&" .  $base_url  . "&" . $paramter_string);
			
	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];

	$signature =  strtoupper(hash_hmac("SHA1",$base_string, $secret_string));	
	
	$split_hex_signature = "";
	foreach(str_split($signature, 2) as $split){
		$split_hex_signature .= chr(hexdec($split));
	}

	$signature = urlEncodeCustom(base64_encode($split_hex_signature));
	return $signature;
}
function generateMediaSingatureCOMBINED($request, $message, $params, $secrets){	
  // BUILD SIGNATURE
	$request_method = strtoupper($request["request_method"]);
	$base_url = urlEncodeCustom($request["base_url"]);
	$paramter_string = urlEncodeCustom("media=" . $message["media"] . "&oauth_consumer_key=" . $params["oauth_consumer_key"]
	 . "&oauth_nonce=" . $params["oauth_nonce"] . "&oauth_signature_method=" . $params["oauth_signature_method"] . "&oauth_timestamp=" . $params["oauth_timestamp"]."&oauth_token=" . $params["oauth_token"] .
	 "&oauth_version=" .$params["oauth_version"]); 
	
	$base_string = ($request_method . "&" .  $base_url  . "&" . $paramter_string);
			
	$secret_string = $secrets["consumer_secret"] . "&" . $secrets["oauth_secret"];
	
	//using binary production
	echo "$base_string<hr>";
	$signature =  (hash_hmac("SHA1",$base_string, $secret_string, true));	
	$signature = urlEncodeCustom(base64_encode($signature));
	return $signature;
}
function getMediaID($binary_file, $size, $mime){
	$media_api = "https://upload.twitter.com/1.1/media/upload.json";
	
	$settings = fopen("settings/keys.ini","r");
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
	
	//Secrets
	$consumer_secret = $keys["consumer_secret"];
	$oauth_secret = $keys["oauth_secret"];
	
	
			echo "<hr/>";
	/////////////MAKE INIT////////////
	//post data
	$command = "INIT";
	$postfield_string = "command=$command&total_bytes=$size&media_type=$mime";
	$msg_len = (strlen($postfield_string));
	
	//header data
		  // BUILD SIGNATURE			
		$signature =   generateMediaSingatureINIT(array(
									"base_url" => $media_api,
									"request_method" => "POST"
									),
								array(
									"command" => "$command",
									"total_bytes" => "$size",
									"media_type" => "$mime"
									),			  
								array(
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
								);
									

	
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
	print_r ($header_data) .  "++";
	$media_id = json_decode(curl_exec($curl), true);
	print_r ($media_id);
	$media_id = $media_id["media_id_string"];
	
	
			echo "<hr/>";
		/////////////MAKE APPEND////////////
			//post data
	$command = "APPEND";
	$segment_index = 0;
	$postfield_string = "command=$command&media_id=$media_id&segment_index=$segment_index&media=$binary_file";
	$msg_len = (strlen($postfield_string));
	//header data
		  // BUILD SIGNATURE			
		$signature =  generateMediaSingatureAppend(array(
									"base_url" => $media_api,
									"request_method" => "POST"
									),
								array(
									"command" => "$command",
									"media" => "$binary_file",
									"media_id"=>"$media_id",
									"segment_index"=>"$segment_index"
									),			  
								array(
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
								);
									

	
	$header_data = array("Accept: */*", "Connection: close","User-Agent: VerniyXYZ-CURL" ,"Content-Transfer-Encoding: binary",
												"Content-Type: multipart/form-data;charset=UTF-8", 
												"Content-Length: $msg_len", "Host: upload.twitter.com",
"Authorization: OAuth oauth_consumer_key=\"$consumer_key\",oauth_nonce=\"$random_value\",oauth_signature=\"$signature\",oauth_signature_method=\"$method\",oauth_timestamp=\"$timestamp\",oauth_token=\"$access_token\",oauth_version=\"$oauth_version\""																				
												);									
	//request
	$curl = curl_init($media_api);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);
	curl_setopt($curl, CURLOPT_HEADER  , true);  // we want headers
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	print_r ($header_data) . "++";
	$http_response = curl_exec($curl);
	echo $http_response;
	
			echo "<hr/>";
		/////////////MAKE FINAL/
}
function getMediaIDCOMBINED($binary_file, $size, $mime){
	$media_api = "https://upload.twitter.com/1.1/media/upload.json";
	
	$settings = fopen("settings/keys.ini","r");
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
	
	//Secrets
	$consumer_secret = $keys["consumer_secret"];
	$oauth_secret = $keys["oauth_secret"];

			echo "<hr/>";
		/////////////MAKE COMBO////////////
			//post data
	$command = "APPEND";
	$segment_index = 0;
	$postfield_string = "media=$binary_file";
	$msg_len = (strlen($postfield_string));
	//header data
		  // BUILD SIGNATURE			
		$signature =  generateMediaSingatureCOMBINED(array(
									"base_url" => $media_api,
									"request_method" => "POST"
									),
								array(
									"media" => "$binary_file",
									),			  
								array(
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
								);
									

	
	$header_data = array("Accept: */*", "Connection: close","User-Agent: VerniyXYZ-CURL" ,"Content-Transfer-Encoding: binary",
												"Content-Type: multipart/form-data;charset=UTF-8", 
												"Content-Length: $msg_len", "Host: upload.twitter.com",
"Authorization: OAuth oauth_consumer_key=\"$consumer_key\",oauth_nonce=\"$random_value\",oauth_signature=\"$signature\",oauth_signature_method=\"$method\",oauth_timestamp=\"$timestamp\",oauth_token=\"$access_token\",oauth_version=\"$oauth_version\""																				
												);									
	//request
	$curl = curl_init($media_api);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postfield_string);
	curl_setopt($curl, CURLOPT_HEADER  , true);  // we want headers
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	print_r ($header_data) . "++";
	$http_response = curl_exec($curl);
	echo $http_response;
	
			echo "<hr/>";
}

function makeTweet($comment, $file_arr){
	/*
		1. Create a set of media ID's.
		2. Add to $postfield_string if there's an image
		3. Authenticate post and submit it to Twitter
	*/
	
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
		echo $base64;
		//upload file to twitter and get id for use in files?
		$size = filesize($file_arr[$file]);
		//$image_string = getMediaID($base64, $size, 'image/' . $type);
		//getMediaIDCOMBINED($base64, $size, 'image/' . $type);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	//////////////////////////---------------------========================================----------------------------//////////////////////////////////////////	
		
		
		
		//authorization details
$settings = fopen("settings/keys.ini","r");
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

//Secrets
$consumer_secret = $keys["consumer_secret"];
$oauth_secret = $keys["oauth_secret"];
		
		
		
		
		
				//connect to twitter
		$oTwitter = new TwitterOAuth($keys["consumer_key"], $consumer_secret, $access_token, $oauth_secret);
		$oTwitter->host = "https://api.twitter.com/1.1/";

		//make output visible in browser
		if (!empty($_SERVER['HTTP_HOST'])) {
			echo '<pre>';
		}

		//load args

		define('DS', DIRECTORY_SEPARATOR);
		
		
		
				//upload image and save media id to attach to tweet
        printf("Uploading to Twitter: %s\n", $file_arr[$file]);
		$sImageBinary = base64_encode(file_get_contents($file_arr[$file]));
		if ($sImageBinary && strlen($sImageBinary) < 15 * pow(1024, 2)) { //max size is 15MB

			$oRet = $oTwitter->upload('media/upload', array('media' => $sImageBinary));
			if (isset($oRet->errors)) {
				printf('Twitter API call failed: media/upload (%s)', $oRet->errors[0]->message);
				return FALSE;
			} else {
				printf("- uploaded %s to attach to next tweet\n", $file_arr[$file]);
			}

			print_r ($oRet);
			$image_string .= $oRet->media_id_string;
			if($file > 0 && $file < sizeof($file_arr) - 1) $image_string .= ","; 
			
        } else {
            printf("- picture is too large!\n");
		}




	//////////////////////////---------------------========================================----------------------------//////////////////////////////////////////	
		





		
	}
}


			echo ("IMAGE-STRING: " . $image_string);
$image_string = urlencode($image_string);
			
//access info
$access_url = "https://api.twitter.com/1.1/statuses/update.json";
$request_method = "POST";

//message info
$encode_tweet_msg = statusEncodeCustom($comment);
$include_entities = "true";

//append to postfield_string the media code via media_ids=$media_id
$postfield_string = "include_entities=$include_entities&status=$encode_tweet_msg&media_ids=$image_string";
$msg_len = (strlen($postfield_string));


echo " <br/>$postfield_string<br/>";

//authorization details
$settings = fopen("settings/keys.ini","r");
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
									"media_ids" => "$image_string"
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