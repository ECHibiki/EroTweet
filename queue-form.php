<?php
	session_start();
	if($_POST["name"] == "ecorvid" && $_POST["pass"] == "notsecret"){
		 $_SESSION["HentaiQueue"] = "pervert";
		header("Location: /twitter/");
	} 
	
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<base href="http://verniy.xyz/twitter/" />
	<title>Submit to @VerniyEro</title>
</head>
<body>
<h1>Submit to <a href="https://twitter.com/VerniyEro/">@VerniyEro</a></h1>
<?php
	require("class/queue-database-construction.php");
	$construction = (new QueueDatabaseConstruction());
	if($_SESSION["HentaiQueue"] != "pervert"){
		$construction->buildPassForm();
	}
	else{			
		$construction->buildQueueForm();
	}
?>
</body>
<script src="form-script.js?1"></script>
</html>