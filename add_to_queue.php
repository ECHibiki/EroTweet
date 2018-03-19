<html>
<head></head>
<body>
<br/>
<a href="http://verniy.xyz/twitter/queue_form.html">Back to Form</a>
<br/><hr/>

<div style="margin:10%">

<?php


checkCommentValid();
$file_string = uploadAndVerify();


echo "<hr/>";


addToDatabase($connection, $file_string);

displayTabularDatabase($connection);

echo "<hr/>Added to post queue<br/>";

?>

</div>
<a href="http://verniy.xyz/twitter/queue_form.html">Back to Form</a>
</body>
</html>

<?php


function checkCommentValid(){
	$COMMENT_MAX = 500;

	if(mb_strlen($_POST["comment"]) > $COMMENT_MAX){
		echo "Comment too long[Server]<br/>";
		die;
	}
}


function uploadAndVerify(){
	$FILE_MAX = 5242880;
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
			if (move_uploaded_file($_FILES["file" . (string)$file]["tmp_name"], $upload_location )) {
				echo "File: $file  was valid.<br/>";
			} 
			else {
				echo "File: $file_location " . " Detected an error <br/>";
				$file_arr[$file - 1] = "0";
				die;
				continue;
			}
		}
		else{
			$file_arr[$file - 1] = 0;
			if($_FILES["file" . (string)$file]["size"] >= $FILE_MAX){
				echo "file" . (string)$file ." Over filesize limit-Server<br/>";
				die;
			}
			else if($_FILES["file" . (string)$file]["error"] == 1){
				echo "file" . (string)$file ." PHP err " . $_FILES["file" . (string)$file]["error"] . "<br/>";
				die;
			}
			else if($_FILES["file" . (string)$file]["error"] == 2){
				echo "file" . (string)$file ." Over filesize limit-Client<br/>";
				die;
			}
			else if($_FILES["file" . (string)$file]["error"] == 3){
				echo "file" . (string)$file ." The uploaded file was only partially uploaded. <br/>";
				die;
			}
			else if($_FILES["file" . (string)$file]["error"] == 4){
				echo "file" . (string)$file ." Empty<br/>";
				if(!$die_state)$die_state = false;
			}
			else{
				echo "file" . (string)$file . " Unkown Upload Error <br/>";
				die;
			}
		}
	}
	return $file_string;
	var_dump($file_arr);
}

function addToDatabase(&$connection, $file_string){
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

	$insert_query = $connection->prepare("INSERT INTO TweetQueue(PostNo,Comment,ImageLocation) VALUES ('',?,?)");
	$comment = $_POST["comment"];
	$file_path = $file_string;
	if (!$insert_query->bind_param("ss", $comment, $file_path)){
		echo "Prepared Statement Error";
		die;
	}

	if (!$insert_query->execute()){
		echo "Execution Error " . $insert_query->errno . "  " . $insert_query->error;
		die;
	}
}

function displayTabularDatabase(&$connection){
	echo "<br/>Displaying All entries(lower number means posted sooner): <br/>";
	$result = $connection->query("Select * from TweetQueue ORDER BY PostNo DESC;");

	echo "<table border='1'>";
	for($row = $result->num_rows - 1; $row >= 0 ; $row--){
		echo"<tr>";
		$tupple = $result->fetch_row();
		foreach($tupple as $col){
			echo "<td>$col</td>";
		}
		echo"</tr>";
	}
    echo "</table>";
}
?>