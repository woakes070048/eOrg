<?php
/********************************************

this file will find out user from table for creat post

*********************************************/
session_start();


if( !( isset( $_SESSION['Username'] ) && isset($_SESSION['Name']) && ( $_SESSION['Username'] == 'admin' || isset($_SESSION['PostName']) ) ))

{
	echo "session id :".session_id()." ,You need to login as Admin to add users. Please log in as/contact Admin.";
	die();
}



$done = $_REQUEST['q'];


echo"Want to add another path";
echo "<input type ='radio' name = 'y' value = '$done'  onclick = 'DocumentForCanAccessDatabaseOfUser_newPath(this.value)' > Yes";
echo "<input type ='radio' name = 'y' value = '$done' onclick = 'DocumentForCanAccessDatabaseOfUser_terminate(this.value)' > NO ";

?>
