<?php 
header("Content-Type: application/json"); 
$error = FALSE;
$doWhatCheck = array('getDates', 'getSeason', 'getEvents', 'getAthletes', 'getAthleteDetails');

if (empty($_GET['directive'])==FALSE) {
   $doWhat = $_GET['directive'];  
} 

if(in_array($doWhat, $doWhatCheck) == FALSE) {
  $error = TRUE;
}

if($error == FALSE) {

$con = mysqli_connect('localhost','XXX','XXX') or die("Some error occurred during connection ".mysqli_error($con));

$con->set_charset('utf8');
    
if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con,"johnfoxs_olympics");
$sql = "";

//Build SQL query depending on the directive
    
//getDates
if($doWhat == "getDates") {
  $sql="SELECT DISTINCT Edition FROM main_olympic_data ORDER BY Edition ASC"; 
}
//getSeason
else if($doWhat == "getSeason") {
    if(empty($_GET['year'])==FALSE) {
        $year = $_GET['year']; 
        $sql="SELECT DISTINCT Season FROM main_olympic_data where Edition=$year"; 
    }
    else{
        echo "Error: missing the date!";
    }
}
//getEvents    
else if($doWhat == "getEvents") {
    if(empty($_GET['year'])==FALSE || empty($_GET['season'])==FALSE) {
        $year = $_GET['year'];
        $season = $_GET['season'];
        $sql="SELECT DISTINCT Event FROM main_olympic_data where Edition=$year AND Season='$season'"; 
    }
    else{
        echo "Error: missing data!";
    }
}  
//getAthletes   
else if($doWhat == "getAthletes") {
    if(empty($_GET['year'])==FALSE || empty($_GET['season'])==FALSE || empty($_GET['season'])==FALSE) {
        $year = $_GET['year'];
        $season = $_GET['season'];
        $event = $_GET['event'];
        $sql="SELECT Athlete, Country FROM main_olympic_data where Edition=$year AND Season='$season' AND Event='$event' ORDER BY Athlete ASC"; 
    }
    else{
        echo "Error: missing data!";
    }
}  
//getAthletesDetails 
    else if($doWhat == "getAthleteDetails") {
    if(empty($_GET['year'])==FALSE || empty($_GET['season'])==FALSE || empty($_GET['season'])==FALSE || empty($_GET['athlete'])==FALSE) {
        $year = $_GET['year'];
        $season = $_GET['season'];
        $event = $_GET['event'];
        $athlete = $_GET['athlete'];
        $sql="SELECT Athlete, Medal, Country, Gender, Event, Edition, Season FROM main_olympic_data where Edition=$year AND Season='$season' AND Event='$event' AND Athlete='$athlete'"; 
    }
    else{
        echo "Error: missing data!";
    }
} 
    else {
    echo "Incorrect directive.  Please check.";
    }

    $result = mysqli_query($con,$sql);
    $data = array();
    $returnedDataString = array();
    
    while($row = mysqli_fetch_assoc($result))
    {
        //Format return JSON for jsTree specific JSON object
        if($doWhat == "getDates") {
            $theEditions['text'] = $row['Edition'];
            $theEditions['id'] = $row['Edition'];
            $theEditions['attr'] = "{'node_type':'date'}";
            $theEditions['children'] = true;
            $returnedDataString[] = $theEditions; 
        }
        else if($doWhat == "getSeason") {
            $theSeasons['text'] = $row['Season'];
            $theSeasons['children'] = true;
            $theSeasons['attr'] = "{'node_type':'date'}";
            $returnedDataString[] = $theSeasons;
        }
        else if($doWhat == "getEvents") {
            $theEvents['text'] = $row['Event'];
            $theEvents['children'] = true;
            $returnedDataString[] = $theEvents;
        }
        else if($doWhat == "getAthletes") {
            if(empty($row['Athlete']) == false || $row['Athlete'] = "" || $row['Athlete'] = null || $row['Athlete'] = 0) {
                $theAthletes['text'] = $row['Athlete'];  
                $theAthletes['children'] = false;
            }
            else {
                $theAthletes['text'] = "Athelete Unknown";  
                $theAthletes['children'] = false;   
            }
            $returnedDataString[] = $theAthletes;
        
        }
        else if($doWhat == "getAthleteDetails") {
        
        }
    }
//print_r($arr); 
//Put result set into JSON object
print (json_encode($returnedDataString));
//print (json_encode($arr));
mysqli_close($con);
}
else {
 echo "Directive not understood. Please use getDates, getSeason, getEvents or getAthletes"; 
}
?>