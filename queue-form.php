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
	$construction->buildQueueForm();
?>
<hr />
<p id="errorMsg">Input a comment and/or file</p>
<input id="submit" type="submit" /></form>
</body>
<script src="form-script.js?1"></script>
</html>