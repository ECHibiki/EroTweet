<?php
		require("class/queue-database-construction.php");
		$construction = new QueueDatabaseConstruction();
		$halt_check = $construction->getPostDetails("SubmissionHalt", "IPAddress", $_SERVER['HTTP_X_REAL_IP']);
		if(sizeof($halt_check) == 0){
			$construction->addToTable("SubmissionHalt", array("IPAddress"=>$_SERVER['HTTP_X_REAL_IP']));
			$comment = $construction->checkCommentValid($_POST["comment"]);
			
			$file_string = $construction->uploadAndVerify($_FILES);

			$do_not_submit = false;
			//Duplicate code = 5
			for($file = 0 ; $file < 4 ; $file++) if($construction->die_state[$file] != -1 && $construction->die_state[$file] != 4){
				$do_not_submit = true;
			} 
			if( $construction->comment_error == 0) $do_not_submit = true;

			if($do_not_submit) {
				header("location: /twitter/confirmation.php?" . "comment=" . $construction->comment_error 
							. "&f1=".$construction->die_state[0] 
							."&f2=". $construction->die_state[1]
							."&f3=". $construction->die_state[2]
							."&f4=".$construction->die_state[3]);
			}
			else $construction->addToTable("TweetQueue.php", ["ImageLocation" => $file_string, "Comment"=>$comment]);
			header("location: /twitter/confirmation?" . "comment=" . $construction->comment_error 
							. "&f1=".$construction->die_state[0] 
							."&f2=". $construction->die_state[1]
							."&f3=". $construction->die_state[2]
							."&f4=".$construction->die_state[3]);
		}
		else
			header("location: /twitter/confirmation?errmsg=1");
?>