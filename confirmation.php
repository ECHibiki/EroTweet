<?php
	require("class/queue-database-construction.php");
		$construction = new QueueDatabaseConstruction();
		echo'
			<html>
			<head></head>
			<body>
			<br/>
			<a href="http://verniy.ca/erotweet/queue-form">Back to Form</a>
			<br/><hr/>

			<div style="margin:10%">
			';
			
		if($_GET["errmsg"] == '1'){
			echo "Multiple Submissions Detected<hr/>";
		}
		else if($_GET["errmsg"] == '1'){
			echo "Multiple Submissions Detected<hr/>";
		}
		else{
			$do_not_submit = false;
			for($file = 1 ; $file <= 4 ; $file++){
				if($_GET["f" . (string)$file] == -1){
					echo "File: $file  was valid.<br/>";
					continue;//bypass do_not_submit flag
				}
				else if($_GET["f" . (string)$file] == 4){
					echo "file $file, Empty<br/>";
					continue;//bypass do_not_submit flag
				}
				else if($_GET["f" . (string)$file] == 0){
					echo "file" . (string)$file ." Over filesize limit-Server<br/>";
				}
				else if($_GET["f" . (string)$file] == 1){
					echo "file $file, PHP err " . $files["file" . (string)$file]["error"] . " <br/>";
				}
				else if($_GET["f" . (string)$file] == 2){
					echo "file $file, Over size limit-Client<br/>";
				}
				else if($_GET["f" . (string)$file] == 3){
					echo "file $file, The uploaded file was only partially uploaded. <br/>";
				}
				else if($_GET["f" . (string)$file] == 5) {
					echo "file " . (string)$file .", Duplicate<br/>";
				}
				else{
					echo "file $file, Unkown Upload Error " . $files["file" . (string)$file]["error"] . "<br/>";	
				}
				$do_not_submit = true;
			}
			if($_GET["comment"] == 0){
				echo "Comment too long[Server]<br/>";
				$do_not_submit = true;
			} 
			
			echo "<hr/>";			
			
			if($do_not_submit) echo "Error in Tweet. Aborting addition to queue.<br/>";

	}

	$construction->displayTabularDatabase("TweetQueue", true);
?>

</div>
<a href="http://verniy.ca/erotweet/queue-form">Back to Form</a>
</body>
</html>

<?php

?>
