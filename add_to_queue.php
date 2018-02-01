<?php
?>

<html>
<head></head>
<body>
<?php

var_dump($_POST);
echo "<br/>";
var_dump($_FILES);
echo "<br/>";
echo "<br/>";

$upload_location = "images/" . basename($_FILES["file"]["name"]);

echo "$upload_location<br/>";

if (move_uploaded_file($_FILES["file"]["tmp_name"], $upload_location )) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";



//ob_start();
//include("tweet.php");
//ob_end_clean();
//makeTweet($_POST["comment"], $upload_location);

echo "<br/>Added to post queue<br/>";
?>

<a href="http://verniy.xyz/twitter/queue-form.html">Back</a>
</body>
</html>