<?php
/*
 *
 * This is to manage the application paths for different groups of the people.
 *
 */


session_start();

if( !( isset( $_SESSION['Username'] ) && isset($_SESSION['Name']) && $_SESSION['Username'] === 'admin' ) )
{
	echo "To access this page you need to login as Admin. Please log in first.";
	die();
}



require_once "../../LocalSettings.php";
require_once "../Globals.php";


$sqlConnAppln = new mysqli( $eorgDBserver , $eorgDBuser , $eorgDBpasswd , $applnDB );
$sqlConnCat = new mysqli( $eorgDBserver , $eorgDBuser , $eorgDBpasswd , $catDB );

if( $sqlConnAppln->connect_errno || $sqlConnCat->connect_errno ) 
{
	echo "Error connecting database. Please check your database credentials and update.";
	die();
}


$html = <<<HTML
<html>
<head>
<title>Application Paths</title>
</head>
<body>
<p>This is to generalise the paths of applications generated by the users. For it, first select the type of the application and then select the group of the users and then give the path (post by post) that is followed by the generated application (of the given type) for the approval (e.g., say there is a the group of users : students->btech->cse->2015and their application (of type say x) follows the path FA2015->dean->hod, then first select the type of application (here x) and then select the categories of users one by one and then give the 'posts' [in order] one by one).<br><br>Now, select the type :<br><br></p>
<form action="initApplnPath.php" method="post">
<select name="applnType" required>
<option value="">Selece the Application type</option>
HTML;

echo $html;

$stmtAppln = $sqlConnAppln->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = \"$applnDB\"");

if ( ! $stmtAppln->execute() )
{
	echo"there is a problem with database<br>";
	die ();
}


$resAppln = $stmtAppln->get_result();

if ( $resAppln->num_rows == 0 )
{
	echo "Sorry, first set the type of application(s)<br>";
	die ();
}


while ($rowAppln = mysqli_fetch_row ($resAppln))
{
	if($rowAppln[0] != $applnPathTable)
	{
		$newRowAppln = str_replace('_', ' ', $rowAppln[0]);
		$newRowAppln = str_replace('$', '.', $newRowAppln);
		echo "<option value=$rowAppln[0]>$newRowAppln</option>";
	}
}

$stmtAppln->close();

echo "</select><br><br>Now, select the users group :<br>";

$qry = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = \"$catDB\"";
$stmtCat = $sqlConnCat->prepare($qry);
if ( ! $stmtCat->execute() )
{
	echo"there is a problem with database<br>";
	die ();
}


$resCat = $stmtCat->get_result();

if ( $resCat->num_rows == 0 )
{
	echo "Sorry, first set the broad categories<br>";
	die ();
}


while ($rowCat = mysqli_fetch_row ($resCat))
{
	$rowCatNew = str_replace('_', ' ', $rowCat[0]);
	$stmtCat2 = $sqlConnCat->prepare("SELECT * FROM $rowCat[0] LIMIT 1");
	if ( ! $stmtCat2->execute() )
	{
		echo"there is a problem with database<br>";
		die ();
	}
	$resCat2 = $stmtCat2->get_result();
	$rowCat2 = mysqli_fetch_row ($resCat2);
	
	echo "<input type='radio' name='users' value=$rowCat[0] id=$rowCat2[0] onclick='giveOptions(this)'>$rowCatNew ";
	$stmtCat2->close();
}

$stmtCat->close();


$html = <<<HTML
<br>
<div id="askingGroups">
</div>
<br>
<div id="backGround">
</div>
<br>
<div id="askingPosts">
<button type="button" onclick="StartGivingPosts()">Click to start specifing the post(s)</button>
</div>
<br>
<div id="anotherBackGround">
</div>
<br><button type='submit' name='submit'>Done!</button>
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
function giveOptions(opted)
{
$("#backGround").hide();
document.getElementById("backGround").innerHTML = "<input name='groupCode' type='text' value='"+opted.id+"' readonly><br>";
var xhttp;
xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function()
{
if (this.readyState == 4 && this.status == 200)
{
document.getElementById("askingGroups").innerHTML = this.responseText;
}
};
xhttp.open("POST", "ApplnPathOptions.php", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("tab="+opted.value+"&levelId="+opted.id);
}
function giveFurtherOptions(opted)
{
$("#backGround").hide();
document.getElementById("backGround").innerHTML = "<input name='groupCode' type='text' value='"+opted.value+"' readonly><br>";
var xhttp;
xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function()
{
if (this.readyState == 4 && this.status == 200)
{
document.getElementById("askingGroups").innerHTML = this.responseText;
}
};
xhttp.open("POST", "ApplnPathOptions.php", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("tab="+opted.name+"&levelId="+opted.value);
}
function StartGivingPosts()
{
$("#anotherBackGround").hide();
var xhttp;
xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function()
{
if (this.readyState == 4 && this.status == 200)
{
document.getElementById("askingPosts").innerHTML = this.responseText;
}
};
xhttp.open("GET", "AskPostsOptions.php", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send();
}
function askAnotherPost(opted)
{
$("#anotherBackGround").hide();
$("#anotherBackGround").append("<input type='text' name='postSequence[]' value='"+opted.value+"' readonly>");
document.getElementById("askingPosts").innerHTML = "<button type='button' onclick='StartGivingPosts()'>Want to add another POST(s)</button>";
}
</script>
</body>
</html>
HTML;

echo $html;





?>
