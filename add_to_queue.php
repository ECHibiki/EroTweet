<html>
<head></head>
<body>
<?php
$FILE_MAX = 5242880;
$COMMENT_MAX = 500;
var_dump($_POST);
echo "<br/>";
if(mb_strlen($_POST["comment"]) > $COMMENT_MAX){
	echo "Comment too long-Server<br/>";
	return;
} 

var_dump($_FILES);
echo "<br/>";
echo "<br/>";


$file_arr = array();
for($file = 1; $file <= 4; $file++){
	$upload_location = "images/" . basename($_FILES["file" . (string)$file]["name"]);
	if($_FILES["file" . (string)$file]["error"] == 0 && $upload_location !== "images/" && $_FILES["file" . (string)$file]["size"] < $FILE_MAX){
		$file_arr[$file - 1] = $upload_location;
		echo "$upload_location<br/>";

		if (move_uploaded_file($_FILES["file" . (string)$file]["tmp_name"], $upload_location )) {
			echo "File is valid, and was successfully uploaded.\n";
		} 
		else {
			echo "file" . (string)$file . " Upload Error <br/>";
			$file_arr[$file - 1] = "0";
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
		}
		else if($_FILES["file" . (string)$file]["error"] == 1){
			echo "file" . (string)$file ." PHP err " . $_FILES["file" . (string)$file]["error"] . "<br/>";
		}
		else if($_FILES["file" . (string)$file]["error"] == 2){
			echo "file" . (string)$file ." Over filesize limit-Client<br/>";
		}
		else if($_FILES["file" . (string)$file]["error"] == 3){
			echo "file" . (string)$file ." The uploaded file was only partially uploaded. <br/>";
		}
		else if($_FILES["file" . (string)$file]["error"] == 4){
			echo "file" . (string)$file ." Empty<br/>";
		}
		else{
			echo "file" . (string)$file . " Unkown Upload Error <br/>";
		}
	}
}
var_dump($file_arr);

print "</pre>";



ob_start();
include("tweet.php");
ob_end_clean();
makeTweet($_POST["comment"], $file_arr);

echo "<br/>Added to post queue<br/>";
?>

<a href="http://verniy.xyz/twitter/queue_form.html">Back to Form</a>
</body>
</html>