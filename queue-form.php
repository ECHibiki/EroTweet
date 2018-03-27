<?php
	require("class/queue-database-construction.php");
	$construction = (new QueueDatabaseConstruction());
	$construction->deletePost("SubmissionHalt", "VerificationID", $_COOKIE["submission_id"]);
	
	session_start();
	setcookie("submission_id", rand(0,1000000));
	if($_POST["name"] == "ecorvid" && $_POST["pass"] == "notsecret"){
		 $_SESSION["HentaiQueue"] = "pervert";
		header("Location: ");
	} 

echo '
	<html>
	<head>
		<base href="http://verniy.xyz/erotweet/">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<base href="http://verniy.xyz/twitter/" />
		<title>Submit to @VerniyEro</title>
	</head>
	<body style="margin: 2% 5%;">
	<h1>Submit to <a href="https://twitter.com/VerniyEro/">@VerniyEro</a></h1>
';

	if($_SESSION["HentaiQueue"] != "pervert"){
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