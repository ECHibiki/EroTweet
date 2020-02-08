<?php
	require("class/queue-database-construction.php");
	$construction = (new QueueDatabaseConstruction());
	$construction->deletePost("SubmissionHalt", "IPAddress", $_SERVER['HTTP_X_REAL_IP']);
	
	session_start();
	if($_POST["name"] == "ecorvid" && $_POST["pass"] == "unsafepassword"){
		 $_SESSION["twitterboard"] = "sessionid";
		header("Location: ");
	} 

echo '
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<base href="http://verniy.ca/twitter/" />
		<title>TweetQueue</title>
	</head>
	<body style="margin: 2% 5%;">
	<h4>Project submits tweets to a specified twitter account at a specified time</a></h4>
';


// echo "<H2>Password verification disabled</H2>";
		// $construction->buildQueueForm();
		// $construction->displayTabularDatabase("TweetQueue", true);
echo "<hr/>";
// $construction->buildPassForm();

	if($_SESSION["twitterboard"] != "sessionid"){
		$construction->buildPassForm();
	}
	else{			
		$construction->buildQueueForm();
		$construction->displayTabularDatabase("TweetQueue", true);
	}
?>
</body>
<script src="form-script.js?2"></script>
</html>