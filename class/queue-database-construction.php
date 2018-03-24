<?php

class QueueDatabaseConstruction{
	
	private $sql_data = array();
	private $connection = null;
	
	public $die_state = array();
	public $comment_error = false;
	public $delete_status = false;
	
	public $alphabet = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p",
"q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
	
	function __construct(){
		$sql_ini = fopen("settings/sql.ini", "r");
		while(!feof($sql_ini)){
			$line = fgets($sql_ini);
			$key = substr($line, 0, strpos($line, "="));
			$value = trim(substr($line, strpos($line, "=")+1));
			$this->sql_data[$key] = $value;
		}
		$this->connectToDatabase();
	}
	
	function connectToDatabase(){
		
		try {
            $this->connection = new PDO ("mysql:dbname=" . $this->sql_data["database"] . ";host=" . $this->sql_data["connection"],
												$this->sql_data["user"], 	$this->sql_data["pass"]);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            $this->logsys .= "Failed to get DB handle: " . $e->getMessage() . "\n";
        }
	}
	
	
	function getConnection(){
		return $this->connection;
	}
	function addToTable($tablename, $paramaters){
		$param_len = sizeof($paramaters);
		$bind_string = "";
		$table_string= "";
		$first_comma = false;
		foreach($paramaters as $key => $param){
			if(!$first_comma){
			$bind_string = ":$key";
			$table_string = "`$key`";
			$first_comma = true;
			}
			else{
				$bind_string .= ",:$key";
				$table_string .= ",`$key`";
			} 
		}
		$statement = $this->connection->prepare("INSERT INTO `".$this->sql_data["database"] ."`.`$tablename`($table_string) VALUES(" . $bind_string . ")");
	
		$index = 0;
		foreach($paramaters as $key => $param){
			$success =	$statement->bindParam(":" . $key , $paramaters[$key]);
			$index++;
		}
		try{
			$statement->execute();
		}catch(Exception  $e){
		   echo "<strong>" . $e->getMessage() . "</strong><br/>";
		}	
	}
	
	//Get the count of all items that are not unverified and not replies
	function getThreads(){
		$statement = $this->connection->prepare("SELECT * FROM `Tweet` 
										LEFT OUTER JOIN `Response` ON `Response`.`PostID` = `Tweet`.`PostID` 
										LEFT OUTER JOIN `Unverified` ON `Unverified`.`PostID` = `Tweet`.`PostID` 
										WHERE `Unverified`.`PostID` IS NULL AND `Response`.`PostID` IS NULL
										ORDER BY Tweet.PostID DESC");
		try{	
			$statement->execute();
			$threads = $statement->fetchAll();
		}catch(Exception  $e){
		   echo "<strong>" . $e->getMessage() . "</strong><br/>";
		}					
		return $threads;
	}
	
	function deleteThread($postID){
		$postID_for_reference = $postID;
		$statement = $this->connection->prepare("DELETE FROM `" . $this->sql_data["database"] ."`.`Tweet` WHERE `PostID` = :PostID");
		$statement->bindParam(":PostID", $postID_for_reference);
		try{
			$response = $statement->execute();
		}catch(Exception  $e){
		   echo "<strong>" . $e->getMessage() . "</strong><br/>";
		}	
		return $response;
	}
	
	function deleteFromUnprocessedImageString($image_path_uprocessed){
		if($image_path_uprocessed === null) return;
		$image_path_uprocessed_arr = explode(",", $image_path_uprocessed);
		foreach($image_path_uprocessed_arr as $unprocessed_path){
			$path = rawurldecode($unprocessed_path);
			unlink ($path);
		}
	}
	
	function buildQueueForm(){
		echo'<form action="add-to-queue.php" enctype="multipart/form-data" method="POST" target="_self">
			<label>Comment:</label><br />
			<textarea id="Comment" name="comment" rows="10" cols="60">';
			
		echo '
<Comment String❤>
Artist: @<Artist>
@HentaiAdvisor @Hentai_Retweet @DoujinsApp @waifu_trash @HentaiTeengirl @Hentai_Babess
<Specific Tagging>
#hentai #hentaileft #hentaicommunity #nsfw  #lewd #porn #hibiki #verniy
#Me_On_The_Left';
			
		echo '</textarea>
			<p id="CharacterCount"></p>

			<input name="MAX_FILE_SIZE" type="hidden" value="5242880" />
			<input name="file1" type="file" id="f1" /><input name="file2" type="file" id="f2" /><br/>
			<input name="file3" type="file" id="f3" /><input name="file4" type="file" id="f4" /><br/>
			<hr />
			<p id="errorMsg">Input a comment and/or file</p>
			<input id="submit" type="submit" /></form>
		';
	}
	
	function buildPassForm(){
		echo"<form action='' method='POST'>
		<input name='name'><br/>
		<input name='pass' type='password'><br/>
		<input type='submit' id='authorization-input' value='Authorize'></form>";
	}
	
	function checkCommentValid($tweet_comment){
		$COMMENT_MAX = 500;

		if(mb_strlen($tweet_comment) > $COMMENT_MAX){
			echo "Comment too long[Server]<br/>";
			$this->comment_error = true;
			return "";
		}
		$this->comment_error = false;
		return $tweet_comment;
	}
	
	function uploadAndVerify($files){								
		$FILE_MAX = 5242880;
		$file_arr = array();
		$file_string = "";
		$first = true;
		for($file = 1; $file <= 4; $file++){
			$upload_location = "images/" . basename($files["file" . (string)$file]["name"]);
			
			if(!file_exists($upload_location) && $files["file" . (string)$file]["error"] == 0 && $upload_location !== "images/" && $files["file" . (string)$file]["size"] < $FILE_MAX){
				$file_arr[$file - 1] = $upload_location;
				if($first){
					$file_string .= rawurlencode($upload_location);
					$first = false;
				}
				else{
					$file_string .=  "," . rawurlencode($upload_location);
				}
				if (move_uploaded_file($files["file" . (string)$file]["tmp_name"], $upload_location )) {
					echo "File: $file  was valid.<br/>";
				} 
				else {
					echo "File: $file_location " . " Detected an error <br/>";
					$file_arr[$file - 1] = "0";
					$die_state[$file - 1] = true;
					continue;
				}
				$die_state[$file - 1] = false;
			}
			else{
				$file_arr[$file - 1] = 0;
				if($files["file" . (string)$file]["size"] >= $FILE_MAX){
					echo "file" . (string)$file ." Over filesize limit-Server<br/>";
					$this->die_state[$file - 1] = true;
				}
				else if($files["file" . (string)$file]["error"] == 1){
					echo "file $file, PHP err " . $files["file" . (string)$file]["error"] . " <br/>";
					$this->die_state[$file - 1] = true;
				}
				else if($files["file" . (string)$file]["error"] == 2){
					echo "file $file, Over size limit-Client<br/>";
					$this->die_state[$file - 1] = true;
				}
				else if($files["file" . (string)$file]["error"] == 3){
					echo "file $file, The uploaded file was only partially uploaded. <br/>";
					$this->die_state[$file - 1] = true;
				}
				else if($files["file" . (string)$file]["error"] == 4){
					echo "file $file, Empty<br/>";
					$this->die_state[$file - 1] = false;
				}
				else if(file_exists($upload_location)) {
					echo "file " . (string)$file .", Duplicate<br/>";
					$this->die_state[$file - 1] = true;
				}
				else{
					echo "file $file, Unkown Upload Error " . $files["file" . (string)$file]["error"] . "<br/>";	
					$this->die_state[$file - 1] = true;
				}
			}
		}
		return $file_string;
	}
	
	function displayTabularDatabase($table_name, $display_images = false){
		echo "<br/>Displaying All entries(lower number means posted sooner): <br/>";
		$statement = $this->connection->query("Select * from ". $table_name . " ORDER BY PostNo DESC;");
		$statement->execute();
		$result_arr = $statement->fetchAll();
		
		foreach($result_arr[0] as $key=>$head){
			if(is_numeric ($key)) unset($result_arr[0][$key]);
		}
		
		echo "<table border='1'><tr>";
		foreach($result_arr[0] as $key=>$head_item)
			echo "<th>$key</th>";
		echo "</tr>";	
		
		
		
		for($row = sizeof($result_arr) - 1; $row >= 0 ; $row--){
			echo"<tr>";
			$tupple = $result_arr[$row];
			$column = 0;
			foreach($tupple as $key=>$col){
				if(is_numeric ($key)) unset($result_arr[0][$key]);
				else {
					if($column == 2 && $display_images){
						$img_arr = explode(",", $col);
						foreach($img_arr as $img){
							$img = urldecode($img);
							$img_ext = pathinfo($img, PATHINFO_EXTENSION);
							if(strcmp($img_ext, "png") == 0 || strcmp($img_ext, "jpg")  == 0|| strcmp($img_ext, "gif") == 0) 
								echo "<td>" . $this->createImageNode($img) . "</td>";
							else
								echo "<td>" . $this->createVideoNode($img) . "</td>";
							
						}
					}
					else{
						if($key == "PostNo") echo "<td>$col - $row</td>";
						else echo "<td>$col</td>";
					}
					$column++;
				}
			}
			echo"</tr>";
		}
		echo "</table><hr/>";
	}

	function createImageNode($img_path){
		return "<img src='$img_path' width='250px'/>";
	}
	function createVideoNode($vid_path){
		return "<video src='$vid_path' autoplay='true' loop='true' width='250px'/>";
	}

	function retrieveOldestEntry(){
		echo "<pre>";
		$retrieval_query = $this->connection->prepare("SELECT * FROM TweetQueue ORDER BY PostNo ASC LIMIT 1");

		$most_recent = $retrieval_query->execute();

		$data_arr = $retrieval_query->fetchAll();

		print_r($data_arr);

		$file_arr  = explode(",", ($data_arr[0]["ImageLocation"] ));
		return $data_arr;
	}
	
	function deleteOldestEntry($oldest){
		print_r($oldest);

		$this->deleteFromUnprocessedImageString($oldest[0]["ImageLocation"]);
		
		$delete_querry = $this->connection->prepare("DELETE FROM TweetQueue WHERE PostNo=:PostNo;");
		$delete_querry->bindParam(":PostNo", $oldest[0]["PostNo"]);
		$this->delete_status = $delete_querry->execute();
		
		if($this->delete_status !== 1){
			echo "<pre><hr/>Delete Err" . $delete_query->error;
		}
	}
}

?>