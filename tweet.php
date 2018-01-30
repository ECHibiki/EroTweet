<?php 
//From https://stackoverflow.com/questions/3295466/another-twitter-oauth-curl-access-token-request-that-fails/3327391#3327391
function generateSingature($params){
	var_dump($params);
	
	  // BUILD SIGNATURE
            // encode params keys, values, join and then sort.
            $keys = _urlencode_rfc3986(array_keys($params));
            $values = _urlencode_rfc3986(array_values($params));
            $params = array_combine($keys, $values);
            uksort($params, 'strcmp');

            // convert params to string 
            foreach ($params as $k => $v) {$pairs[] = _urlencode_rfc3986($k).'='._urlencode_rfc3986($v);}
            $concatenatedParams = implode('&', $pairs);

            // form base string (first key)
            $baseString= "GET&"._urlencode_rfc3986(request_token)."&"._urlencode_rfc3986($concatenatedParams);
            // form secret (second key)
            $secret = _urlencode_rfc3986(secret)."&";
            // make signature and append to params
			$params['oauth_signature'] = _urlencode_rfc3986(base64_encode(hash_hmac('sha1', $baseString, $secret, TRUE)));

}

$msg = "testing";
//dumb approximation
$msg_len = decoct(strlen($msg) + 10);

$consumer_key = "-------";
$access_token = "-------";
$random_value = str_replace("=", "2", base64_encode(rand(10000000000,1000000000000)));
$timestamp = time();
$method = "HMAC-SHA1";

$signature = generateSingature(array(
							"oauth_version" => "1.0",
							"oauth_nonce"=>"$random_value",
							"oauth_timestamp" => "$timestamp",
							"oauth_consumer_key" => "$consumer_key",
							"oauth_signature_method" => "HMAC-SHA1"
));

$curl = curl_init("https://api.twitter.com/1.1/statuses/update.json");
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: */*", "Connection: close","User-Agent: VerniyXYZ-CURL" ,"Host: api.twitter.com",
												"Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
												"Content-Length: $msg_len", "Host: api.twitter.com",
												
												"Authorization:
													OAuth oauth_consumer_key='$consumer_key',
													oauth_nonce='$random_value',
													oauth_signature='$signature',
													oauth_signature_method='$method',
													oauth_timestamp='$timestamp',
													oauth_token='$access_token',
													oauth_version='1.0'
													"
												
												));
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, "include_entities=true&status=$msg");
var_dump(curl_exec($curl));
?>
