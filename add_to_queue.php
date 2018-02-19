<html>
<head></head>
<body>
<br/>
<a href="http://verniy.xyz/twitter/queue_form.html">Back to Form</a>
<br/><hr/>
<?php
$FILE_MAX = 5242880;
$COMMENT_MAX = 500;

$die_state = false;

var_dump($_POST);
echo "<br/>";
if(mb_strlen($_POST["comment"]) > $COMMENT_MAX){
	echo "Comment too long-Server<br/>";
	$die_state = true;
} 

var_dump($_FILES);
echo "<br/>";
echo "<br/>";


$file_arr = array();
$file_string = "";
$first = true;
for($file = 1; $file <= 4; $file++){
	$upload_location = "images/" . basename($_FILES["file" . (string)$file]["name"]);
	if($_FILES["file" . (string)$file]["error"] == 0 && $upload_location !== "images/" && $_FILES["file" . (string)$file]["size"] < $FILE_MAX){
		$file_arr[$file - 1] = $upload_location;
		if($first){
			$file_string .= rawurlencode($upload_location);
			$first = false;
		}
		else{
			$file_string .=  "," . rawurlencode($upload_location);
		}
		echo "$upload_location<br/>";

		if (move_uploaded_file($_FILES["file" . (string)$file]["tmp_name"], $upload_location )) {
			echo "File is valid, and was successfully uploaded.\n";
		} 
		else {
			echo "file" . (string)$file . " Upload Error <br/>";
			$file_arr[$file - 1] = "0";
			$die_state = true;
			continue;
		}

		echo 'Here is some more debugging info: <br/>';
		print_r($_FILES["file" . (string)$file]);
		echo "<br/>";
	}
	else{
		$file_arr[$file - 1] = 0;
		if($_FILES["file" . (string)$file]["size"] >= $FILE_MAX){
			echo "file" . (string)$file ." Over filesize limit-Server<br/>";
			$die_state = true;
		}
		else if($_FILES["file" . (string)$file]["error"] == 1){
			echo "file" . (string)$file ." PHP err " . $_FILES["file" . (string)$file]["error"] . "<br/>";
			$die_state = true;
		}
		else if($_FILES["file" . (string)$file]["error"] == 2){
			echo "file" . (string)$file ." Over filesize limit-Client<br/>";
			$die_state = true;
		}
		else if($_FILES["file" . (string)$file]["error"] == 3){
			echo "file" . (string)$file ." The uploaded file was only partially uploaded. <br/>";
			$die_state = true;
		}
		else if($_FILES["file" . (string)$file]["error"] == 4){
			echo "file" . (string)$file ." Empty<br/>";
			if(!$die_state)$die_state = false;
		}
		else{
			echo "file" . (string)$file . " Unkown Upload Error <br/>";
			$die_state = true;
		}
	}
}


var_dump($file_arr);

echo "<hr/><pre>";

if($die_state){
	echo "Upload Error\n";
	die;
}

//sql database calls
$sql_ini = fopen("settings/sql.ini", "r");
$sql_data = array();
while(!feof($sql_ini)){
	$line = fgets($sql_ini);
	$key = substr($line,0,strpos($line, ":"));
			//eat last character
	$value = trim(substr($line, strpos($line, ":")+1));
	$sql_data[$key] = $value;
}

$connection = new mysqli($sql_data["connection"], $sql_data["user"], $sql_data["pass"], $sql_data["database"]);
if (!$connection) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;

}

$insert_query = "INSERT INTO TweetQueue(PostNo,Comment,ImageLocation) VALUES ('','" 
	. $_POST["comment"]. "','" . $file_string . "')";
	echo $insert_query . "<br/>";
$result = $connection->query($insert_query);

echo "\n\n---------------\n\n";

$result = $connection->query("Select * from TweetQueue;");
print_r($result); 
echo "\n";

for($row = $result->num_rows - 1; $row >= 0 ; $row--){
	$result->data_seek($row);
	print_r($result->fetch_assoc());
}

echo "<hr/>Added to post queue<br/>";
?>

<a href="http://verniy.xyz/twitter/queue_form.html">Back to Form</a>
</body>
</html>