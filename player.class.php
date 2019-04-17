<?php

class Player { 
    public $player_id;
    public $player_name;
	public $points;
	public $assists;
	public $rebounds;
	public $steals;
    private $noerrors = true;
    private $nameError = null;
	private $pointsError = null;
	private $assistsError = null;
	private $reboundsError = null;
	private $stealsError = null;
    private $title = "Player";
    private $tableName = "player";
	
	// initialize $_FILES variables
	private $fileName = '';
	private $tmpName = '';
	private $fileSize = ''; 
	private $fileType = '';
	private $content = '';
    
    function create_record() { // display "create" form
        $this->generate_html_top (1);
        $this->generate_form_group("player_name", $this->nameError, $this->player_name, "autofocus");
        $this->generate_form_group("points", $this->pointsError, $this->points);
		$this->generate_form_group("assists", $this->assistsError, $this->assists);
		$this->generate_form_group("rebounds", $this->reboundsError, $this->rebounds);
		$this->generate_form_group("steals", $this->stealsError, $this->steals);
		$this->insert_photo();
        $this->generate_html_bottom (1);
    } // end function create_record()
    
    function read_record($player_id, $coach_id) { // display "read" form
        $this->select_db_record($player_id, $coach_id);
        $this->generate_html_top(2);
		$this->display_photo();
        $this->generate_form_group("player_name", $this->nameError, $this->player_name, "disabled");
        $this->generate_form_group("points", $this->pointsError, $this->points, "disabled");
		$this->generate_form_group("assists", $this->assistsError, $this->assists, "disabled");
		$this->generate_form_group("rebounds", $this->reboundsError, $this->rebounds, "disabled");
		$this->generate_form_group("steals", $this->stealsError, $this->steals, "disabled");
        $this->generate_html_bottom(2);
    } // end function read_record()
    
    function update_record($player_id, $coach_id) { // display "update" form
        if($this->noerrors) $this->select_db_record($player_id,$coach_id);
        $this->generate_html_top(3, $player_id); 
		$this->display_photo();
        $this->generate_form_group("player_name", $this->nameError, $this->player_name, "autofocus onfocus='this.select()'");
        $this->generate_form_group("points", $this->pointsError, $this->points);
		$this->generate_form_group("assists", $this->assistsError, $this->assists);
		$this->generate_form_group("rebounds", $this->reboundsError, $this->rebounds);
		$this->generate_form_group("steals", $this->stealsError, $this->steals);
		$this->insert_photo();
        $this->generate_html_bottom(3);
    } // end function update_record()
    
    function delete_record($player_id, $coach_id) { // display "read" form
        $this->select_db_record($player_id, $coach_id);
        $this->generate_html_top(4, $player_id);
		$this->display_photo();
        $this->generate_form_group("player_name", $this->nameError, $this->player_name, "disabled");
        $this->generate_form_group("points", $this->pointsError, $this->points, "disabled");
		$this->generate_form_group("assists", $this->assistsError, $this->assists, "disabled");
		$this->generate_form_group("rebounds", $this->reboundsError, $this->rebounds, "disabled");
		$this->generate_form_group("steals", $this->stealsError, $this->steals, "disabled");
        $this->generate_html_bottom(4);
    } // end function delete_record()
	
	function display_photo () {
		echo "
				<div class='control-group col-md-6'>
					<div class='controls '>
			";
					 
		if ($this->fileSize > 0) 
			echo "<img height='30%' width='30%' src='data:image/jpeg;base64," . 
				base64_encode( $this->content ) . "' />"; 
		else 
			echo 'No photo on file.';	
		echo"	
					</div>
					<br>
				</div>
			";
	}
	
	function insert_photo () {
		echo "<div class='control-group '>
							<div class='controls'>
							<label class='control-label'>Picture</label>
								<input type='hidden' name='MAX_FILE_SIZE' value='16000000'>
								<input name='userfile' type='file' player_id='userfile'>
							</div>
							<br>
				</div>";
	}
    
	//Inserts form input into database
    function insert_db_record ($coach_id) { 
		// initialize $_FILES variables
		$this->fileName = $_FILES['userfile']['name'];
		$this->tmpName  = $_FILES['userfile']['tmp_name'];
		$this->fileSize = $_FILES['userfile']['size'];
		$this->fileType = $_FILES['userfile']['type'];
		$this->content = file_get_contents($this->tmpName); 
		
        if ($this->fieldsAllValid()) { // validate user input
				// if valid data, insert record into table
				$pdo = Database::connect();
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = "INSERT INTO $this->tableName (player_name,coach_id,points,assists,rebounds,steals,filename,filesize,filetype,filecontent)
						values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$q = $pdo->prepare($sql);
				$q->execute(array($this->player_name, $coach_id, $this->points, $this->assists, $this->rebounds,$this->steals,$this->fileName,$this->fileSize,$this->fileType,$this->content));
				Database::disconnect();
				header("Location: $this->tableName.php"); // go back to "list"
        }
        else {
            // if not valid data, go back to "create" form, with errors
            // Note: error fields are set in fieldsAllValid ()method
            $this->create_record(); 
        }
    } // end function insert_db_record
    
	//Retrieves data from database to display and read
    private function select_db_record($player_id, $coach_id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM $this->tableName where player_id = ? AND coach_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($player_id, $coach_id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        $this->player_name = $data['player_name'];
        $this->points = $data['points'];
        $this->assists = $data['assists'];
		$this->rebounds = $data['rebounds'];
		$this->steals = $data['steals'];
		$this->fileName = $data['filename'];
        $this->fileSize = $data['filesize'];
        $this->fileType = $data['filetype'];
		$this->content = $data['filecontent'];
		
    } // function select_db_record()
    
	//Updates values of selected record
       function update_db_record ($player_id) {
		// initialize $_FILES variables
		$this->fileName = $_FILES['userfile']['name'];
		$this->tmpName  = $_FILES['userfile']['tmp_name'];
		$this->fileSize = $_FILES['userfile']['size'];
		$this->fileType = $_FILES['userfile']['type'];
		$this->content = file_get_contents($this->tmpName); 
        $this->player_id = $player_id;
		
        if ($this->fieldsAllValid()) {
			if($this->fileSize > 0) {
				$this->noerrors = true;
				$pdo = Database::connect();
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = "UPDATE $this->tableName  set player_name = ?, points = ?, assists = ?, rebounds = ?, steals = ?, filename = ?, filesize = ?, filetype = ?, filecontent = ? WHERE player_id = ?";
				$q = $pdo->prepare($sql);
				$q->execute(array($this->player_name,$this->points,$this->assists,$this->rebounds,$this->steals,$this->fileName,$this->fileSize,$this->fileType,$this->content,$this->player_id));
				Database::disconnect();
				header("Location: $this->tableName.php");
			} else {
				$this->noerrors = true;
				$pdo = Database::connect();
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = "UPDATE $this->tableName  set player_name = ?, points = ?, assists = ?, rebounds = ?, steals = ? WHERE player_id = ?";
				$q = $pdo->prepare($sql);
				$q->execute(array($this->player_name,$this->points,$this->assists,$this->rebounds,$this->steals,$this->player_id));
				Database::disconnect();
				header("Location: $this->tableName.php");
			}
        }
        else {
            $this->noerrors = false;
            $this->update_record($player_id, $coach_id);  // go back to "update" form
        }
    } // end function update_db_record
    
	//Deletes selected database record
    function delete_db_record($player_id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM $this->tableName WHERE player_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($player_id));
        Database::disconnect();
        header("Location: $this->tableName.php");
    } // end function delete_db_record()
    
	//Creates top part of each form based on inputted function
    private function generate_html_top ($fun, $player_id=null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Create a"; $funNext = "insert_db_record"; 
                break;
            case 2: // read
                $funWord = "Stats of the"; $funNext = "none"; 
                break;
            case 3: // update
                $funWord = "Update the"; $funNext = "update_db_record&player_id=" . $player_id; 
                break;
            case 4: // delete
                $funWord = "Delete the"; $funNext = "delete_db_record&player_id=" . $player_id; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_top()"; 
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord a $this->title</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    "; 
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord $this->title</h3>
                        </p>
                        <form class='form-horizontal' action='$this->tableName.php?fun=$funNext' method='post' enctype='multipart/form-data'>                        
                    ";
    } // end function generate_html_top()
    
	//Creates bottom part of each form based on inputted function
    private function generate_html_bottom ($fun) {
        switch ($fun) {
            case 1: // create
                $funButton = "<button type='submit' class='btn btn-success'>Create</button>"; 
                break;
            case 2: // read
                $funButton = "";
                break;
            case 3: // update
                $funButton = "<button type='submit' class='btn btn-warning'>Update</button>";
                break;
            case 4: // delete
                $funButton = "<button type='submit' class='btn btn-danger'>Delete</button>"; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_bottom()"; 
                exit();
                break;
        }
        echo " 
                            <div class='form-actions'>
                                $funButton
                                <a class='btn btn-secondary' href='$this->tableName.php'>Back</a>
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
    } // end function generate_html_bottom()
    
	 //Creates appropriate form based on inputted value
	 private function generate_form_group ($label, $labelError, $val, $modifier="", $fieldType="text") {
        echo "<div class='form-group";
        echo !empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        echo "<input "
            . "name='$label' "
            . "type='$fieldType' "
            . "$modifier "
            . "placeholder='$label' "
            . "value='";
        echo !empty($val) ? $val : '';
        echo "'>";
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        echo "</div>"; // end div: class='form-group'
    } // end function generate_form_group()
    
	//Checks if every value is valid and not empty in Form
    private function fieldsAllValid () {
        $valid = true;
        if (empty($this->player_name)) {
            $this->nameError = 'Please enter Name';
            $valid = false;
        }
        if (empty($this->points)) {
            $this->points = 0;
        } 
        if (empty($this->assists)) {
            $this->assists = 0;
        }
		if (empty($this->rebounds)) {
            $this->rebounds = 0;
        }
		if (empty($this->steals)) {
            $this->steals = 0;
        }
		
        return $valid;
		
    } // end function fieldsAllValid() 

    
	//Displays main list of records from database
    function list_records($coach_id) {
		/* echo gettype($coach_id);
		echo gettype(intval($coach_id)); */
		$coach_id = intval($coach_id);
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$this->title" . "s" . "</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";  
        echo "
            </head>
            <body>
			<a href='https://github.com/besmith67/Prog7.git' target='_blank'>Github</a><br />
                <div class='container'>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                    </p>
                    <p>
                        <a href='$this->tableName.php?fun=display_create_form' class='btn btn-success'>Create</a>
						<a href='logout.php' class='btn btn-warning'>Logout</a>
                    </p>
                    <div class='row'>
                        <table class='table table-striped table-bordered' id='myTable'>
                            <thead>
                                <tr>
                                    <th onclick='sortTable(0)' style='cursor:pointer'>Name</th>
                                    <th onclick='sortTableByNum(1)' style='cursor:pointer'>Points</th>
                                    <th onclick='sortTableByNum(2)' style='cursor:pointer'>Assists</th>
                                    <th onclick='sortTableByNum(3)' style='cursor:pointer'>Rebounds</th>
									<th onclick='sortTableByNum(4)' style='cursor:pointer'>Steals</th>
									<th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM $this->tableName WHERE coach_id = $coach_id ORDER BY player_id DESC";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
            echo "<td>". $row["player_name"] . "</td>";
            echo "<td>". $row["points"] . "</td>";
            echo "<td>". $row["assists"] . "</td>";
			echo "<td>". $row["rebounds"] . "</td>";
			echo "<td>". $row["steals"] . "</td>";
            echo "<td width=250>";
            echo "<a class='btn btn-info' href='$this->tableName.php?fun=display_read_form&player_id=".$row["player_id"]."'>Read</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-warning' href='$this->tableName.php?fun=display_update_form&player_id=".$row["player_id"]."'>Update</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->tableName.php?fun=display_delete_form&player_id=".$row["player_id"]."'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();        
        echo "
                            </tbody>
                        </table>
                    </div>
                </div>
			<script>
				function sortTable(n) {
				  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
				  table = document.getElementById('myTable');
				  switching = true;
				  // Set the sorting direction to ascending:
				  dir = 'asc';
				  /* Make a loop that will continue until
				  no switching has been done: */
				  while (switching) {
					// Start by saying: no switching is done:
					switching = false;
					rows = table.rows;
					/* Loop through all table rows (except the
					first, which contains table headers): */
					for (i = 1; i < (rows.length - 1); i++) {
					  // Start by saying there should be no switching:
					  shouldSwitch = false;
					  /* Get the two elements you want to compare,
					  one from current row and one from the next: */
					  x = rows[i].getElementsByTagName('TD')[n];
					  y = rows[i + 1].getElementsByTagName('TD')[n];
					  /* Check if the two rows should switch place,
					  based on the direction, asc or desc: */
					  if (dir == 'asc') {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
						  // If so, mark as a switch and break the loop:
						  shouldSwitch = true;
						  break;
						}
					  } else if (dir == 'desc') {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
						  // If so, mark as a switch and break the loop:
						  shouldSwitch = true;
						  break;
						}
					  }
					}
					if (shouldSwitch) {
					  /* If a switch has been marked, make the switch
					  and mark that a switch has been done: */
					  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					  switching = true;
					  // Each time a switch is done, increase this count by 1:
					  switchcount ++;
					} else {
					  /* If no switching has been done AND the direction is 'asc',
					  set the direction to 'desc' and run the while loop again. */
					  if (switchcount == 0 && dir == 'asc') {
						dir = 'desc';
						switching = true;
					  }
					}
				  }
				}
				
				function sortTableByNum(n) {
				  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
				  table = document.getElementById('myTable');
				  switching = true;
				  // Set the sorting direction to ascending:
				  dir = 'asc';
				  /* Make a loop that will continue until
				  no switching has been done: */
				  while (switching) {
					// Start by saying: no switching is done:
					switching = false;
					rows = table.rows;
					/* Loop through all table rows (except the
					first, which contains table headers): */
					for (i = 1; i < (rows.length - 1); i++) {
					  // Start by saying there should be no switching:
					  shouldSwitch = false;
					  /* Get the two elements you want to compare,
					  one from current row and one from the next: */
					  x = rows[i].getElementsByTagName('TD')[n];
					  y = rows[i + 1].getElementsByTagName('TD')[n];
					  /* Check if the two rows should switch place,
					  based on the direction, asc or desc: */
					  if (dir == 'asc') {
						if (Number(x.innerHTML) > Number(y.innerHTML)) {
							//if so, mark as a switch and break the loop:
							shouldSwitch = true;
							break;
						}
					  } else if (dir == 'desc') {
						if (Number(x.innerHTML) < Number(y.innerHTML)) {
							//if so, mark as a switch and break the loop:
							shouldSwitch = true;
							break;
						  }
					  }
					}
					if (shouldSwitch) {
					  /* If a switch has been marked, make the switch
					  and mark that a switch has been done: */
					  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					  switching = true;
					  // Each time a switch is done, increase this count by 1:
					  switchcount ++;
					} else {
					  /* If no switching has been done AND the direction is 'asc',
					  set the direction to 'desc' and run the while loop again. */
					  if (switchcount == 0 && dir == 'asc') {
						dir = 'desc';
						switching = true;
					  }
					}
				  }
				}
				</script>
				

            </body>

        </html>
                    ";  
    } // end function list_records()
    
} // end class Player