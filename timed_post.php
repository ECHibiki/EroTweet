<?php //When called, make a request to pull a tweet from an SQL table 

$sql_ini = fopen("settings/sql.ini", "r");
$sql_data = array();
while(!feof($sql_ini)){
	$line = fgets($sql_ini);
	$key = substr($line, 0, strpos($line, ":"));
	$value = trim(substr($line, strpos($line, ":")+1));
	$sql_data[$key] = $value;
}

$connection = new mysqli($sql_data["connection"], $sql_data["user"], $sql_data["pass"], $sql_data["database"]);
if(!$connection){
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
echo "<hr/><pre>";

$retrieval_query = "SELECT * FROM TweetQueue ORDER BY PostNo ASC LIMIT 1";

$most_recent = $connection->query($retrieval_query);
print_r($most_recent); 
echo "\n";

$data_arr = $most_recent->fetch_assoc();

print_r($data_arr);

$file_arr  = explode(",", rawurldecode($data_arr["ImageLocation"]));
	
echo "Comm: " . $data_arr["Comment"] . " - ILoc: ";
print_r($file_arr);

ob_start();
require("tweet.php");
ob_end_clean();
makeTweet($data_arr["Comment"], $file_arr);

$delete_query = "DELETE FROM TweetQueue WHERE PostNo=" . $data_arr["PostNo"];
$delete_status = $connection->query($delete_query);
print_r($delete_status);
if($delete_status !== 1){
	echo "Delete Err" . $delete_query->error;
}


echo "<hr/></pre>Found, Added and Deleted<br/>";

?>