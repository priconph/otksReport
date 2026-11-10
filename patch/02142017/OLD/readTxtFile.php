		<form action="readTxtFile.php" method="post" enctype="multipart/form-data">
			<label for="file">UPLOAD SL / VL:</label><br>
			<input type="file" name="file" id="file"><br>
			<input type="submit" name="submit" value="Submit">
		</form>

<?php

	if(isset($_FILES['file']['name'])){
		
		$file = $_FILES['file']['name'];
		$fileCheck = explode(".", $file);
		if($fileCheck[1]!='txt'){
			echo 'ERROR: TEXT FILES ONLY!';
		}
		else{
			if ($_FILES["file"]["error"] > 0)
			{
				echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				if (file_exists($_FILES["file"]["name"]))
				{
					echo $_FILES["file"]["name"] . " already exists. ";
				}
				else
				{
					move_uploaded_file($_FILES["file"]["tmp_name"], 'Reference/'.$_FILES["file"]["name"]);
					//echo "Stored in: " . 'Reference/' . $_FILES["file"]["name"]."<br>";
				}
			}

			if ($_FILES["file"]["error"] > 0)
			{
				echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				if (file_exists($_FILES["file"]["name"]))
				{
					echo $_FILES["file"]["name"] . " already exists. ";
				}
				else
				{
					move_uploaded_file($_FILES["file"]["tmp_name"], 'Reference/'.$_FILES["file"]["name"]);
					echo "FILE: ". $_FILES["file"]["name"]."<br>";
				}
			}
				
				
					$path = 'Reference/';
					$fileName = $path.$_FILES['file']['name'];
					$fd = fopen($fileName, "r");
					$contents = fread($fd, filesize($fileName));
					fclose($fd);
					$delimiter1 = ";";
					$delimiter2 = "\n";
					$splitContents1 = explode($delimiter2, $contents);
					$splitContents2 = explode($delimiter1, $contents);
					$counter = "";
					
					
					foreach($splitContents1 as $perEmployee) {
						foreach(explode($delimiter1, $perEmployee) as $details) {
							if($counter == 0) {
								$empno = $details;
								echo "<b>[1] </b> $details   ";
								$counter++;
							} else if($counter == 1) {
								$temp1 = $details;
								echo "<b>[2] </b> $details    ";
								$counter++;
							} else if($counter == 2) {
								$temp2 = $details;
								echo "<b>[3] </b> $details   ";
								$counter++;
							} else if($counter == 3) {
								$temp3 = $details;
								echo "<b>[4] </b> $details   ";
								$counter++;
							} else if($counter == 4) {
								$temp4 = $details;
								$counter = 0;
								echo "<b>[5] </b> $details<br>";
							}
						}
						echo "EN: ".$empno.",1: ".$temp1.",2: ".$temp2.",3: ".$temp3.",4: ".$temp4."<br>";
					//insert into database
					}
		}		
	}

?>