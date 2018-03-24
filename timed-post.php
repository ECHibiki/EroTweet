<?php //When called, make a request to pull a tweet from an SQL table 
	require_once("class/queue-database-construction.php");
	$construction = new QueueDatabaseConstruction();
	//row array
	$oldest = $construction->retrieveOldestEntry();
		echo "<br/>" . var_dump($oldest);
	echo "<hr/>";

	//ob_start();
	require_once("class/twitter-connection.php");
	//ob_end_clean();
	$connection = new TwitterConnection();
	$response = $connection->makeTweet($oldest[0]["Comment"], explode(",", $oldest[0]["ImageLocation"]));
echo "</pre>";

	if($response["created_at"] == null){
		echo "post unsuccessful";
		return;
	} 
	else {
		$construction->deleteOldestEntry($oldest);
		echo "Found, Added and Deleted<br/>";
	}
?>