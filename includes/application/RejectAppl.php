<?php
/* 
 * reject application
 *
 */

session_start();

if ( !( isset( $_SESSION['Username'] ) && isset( $_SESSION['Name'] ) && isset( $_SESSION['PostName'] ) ) )
{
	echo "session id :".session_id()." ,You must login first to visit this page.";
	die();
}


$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="/favicon.png" type="image/png">
<link rel="shortcut icon" type="image/png" href="../../image/gogreen.jpg" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    
    background-image: url("../../image/image4.jpg");
     min-height: 500px;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}
button
{
	cursor: pointer; font-size : 25px; height:auto; width:auto ;background-color:transparent;color:white ;
	border: 0.25px solid white;
}
</style>
</head>

<body style = "color:white"><br><br>
<button onclick="document.location.href='handleAppl.php'"> BACK </button>
<center><b><i>
HTML;
echo $html;


require "../ApplHandlingByID.php";
require "../ApplHandlingByStr.php";





if ( ! isset (  $_POST['app_id'] ) )
{
	echo "Sorry, there was a problem.<br>";
	die();
}
else
{
	$app_id = $_POST['app_id'];
	$app_type = $_POST['app_type'];
	$UID = $_SESSION['PostName'];
	
	if ( reject( $app_id, $app_type , $UID ) )
	{
		echo "The application has been successfully rejected<br>";
	}
	else
	{
		echo "Sorry, there was a problem<br>";
		die();
	}
}


?>
