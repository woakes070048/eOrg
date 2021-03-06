<?php
/*
 * functions to count applications.
 *
 * date format : yyyy-mm-dd
 */


function countTypeOfAppln()
{
	require "../../LocalSettings.php";
	require "../Globals.php";

	$sqlconn = new mysqli ( $eorgDBserver, $eorgDBuser, $eorgDBpasswd, $applnDB );
	$count = array();
	$res = $sqlconn->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = \"$applnDB\"");

	while( $row = mysqli_fetch_row($res) )
	{
		array_push($count,$row[0]);
	}

	$sqlconn->close();
	return $count;
}

function decrYear( $date )
{
	$tmp = new DateTime($date);
	$tmp->modify('-1 year');
	return $tmp->format('Y-m-d');
}

function decrMonth( $date )
{
	$tmp = new DateTime($date);
	$tmp->modify('-1 month');
	return $tmp->format('Y-m-d');
}

function decrDay( $date )
{
	$tmp = new DateTime($date);
	$tmp->modify('-1 day');
	return $tmp->format('Y-m-d');
}


function countApplnInYear( $user , $year )
{
	require "../../LocalSettings.php";
	require "../Globals.php";

	$sqlconn = new mysqli ( $eorgDBserver, $eorgDBuser, $eorgDBpasswd, $applnCount );

	$count = new stdClass();
	$type = countTypeOfAppln();
	$i = 0;

	while( $i < count($type) )
	{
		$qry1 = "SELECT * FROM $user WHERE $tillDate regexp \"$year-[0-9][0-9]-[0-9][0-9]\" AND $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
		
		$result1 = $sqlconn->query($qry1);

		if( mysqli_num_rows($result1) == 0 )
		{
			$v = $type[ $i ];
			$count->$v = '0';
		}
		else
		{
			$row1 = mysqli_fetch_array($result1);
			
			$res = $sqlconn->query("SELECT $tillDate FROM $user ORDER BY $tillDate limit 1");
			$lim = mysqli_fetch_array($res)[0];
			
			$lim = explode('-',$lim)[0];
			$flag = false;
			$yr = $year-1;
			
			$v = $row1[0];

			while( $yr >= $lim )
			{
				$qry = "SELECT * FROM $user WHERE $tillDate regexp \"$yr-[0-9][0-9]-[0-9][0-9]\" AND $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
				
				$result = $sqlconn->query($qry);
				if( mysqli_num_rows($result) != 0 )
				{
					$row2 = mysqli_fetch_array($result);
					$count-> $v = $row1[1]-$row2[1];
					$flag = true;
					break;
				}
				
				$yr = $yr-1;
			}

			if( !$flag )
				$count-> $v = $row1[1];
		}
		
		$i = $i+1;
	}
	$sqlconn->close();
	return $count;
}

function countApplnInMonth( $user, $month, $year )
{
	require "../../LocalSettings.php";
	require "../Globals.php";

	$sqlconn = new mysqli ( $eorgDBserver, $eorgDBuser, $eorgDBpasswd, $applnCount );

	$count = new stdClass();

	$type = countTypeOfAppln();
	$i = 0;

	while( $i < count($type) )
	{
		$qry1 = "SELECT * FROM $user WHERE $tillDate regexp \"$year-$month-[0-9][0-9]\" AND $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
		$result1 = $sqlconn->query($qry1);

		if( mysqli_num_rows($result1) == 0 )
		{
			$v = $type[ $i ];
			$count->$v = 0;
		}
		else
		{
			$row1 = $result1->fetch_array();
			$v = $row1[0];
			
			$res = $sqlconn->query("SELECT $tillDate FROM $user ORDER BY $tillDate limit 1");
			$lim = mysqli_fetch_row($res)[0];
			//$lim = explode('-',$lim);
			$flag = false;

			$date = $year.'-'.$month.'-'. explode('-',$lim)[2] ;
			$date = decrMonth( $date );

			$ar = explode('-',$date);
			$yr = $ar[0];
			$mth = $ar[1];

			while( strtotime($date) >= strtotime($lim) )
			{
				$qry = "SELECT * FROM $user WHERE $tillDate regexp \"$yr-$mth-[0-9][0-9]\" AND $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
				$result = $sqlconn->query($qry);
				if( mysqli_num_rows($result) != 0 )
				{
					$row2 = mysqli_fetch_array($result);
					$count->$v = $row1[1]-$row2[1];
					$flag = true;
					break;
				}

				$date = decrMonth( $date );

				$ar = explode('-',$date);
				$yr = $ar[0];
				$mth = $ar[1];
			}

			if( !$flag )
				$count->$v = $row1[1];
		}

		$i = $i+1;
	}

	$sqlconn->close();
	return $count;
}

function countApplnOnDay( $user, $day, $month, $year )
{
	require "../../LocalSettings.php";
	require "../Globals.php";

	$sqlconn = new mysqli ( $eorgDBserver, $eorgDBuser, $eorgDBpasswd, $applnCount );

	$count = new stdClass();

	$type = countTypeOfAppln();
	$i = 0;

	while( $i < count($type) )
	{
		$qry1 = "SELECT * FROM $user WHERE $tillDate regexp \"$year-$month-$day\" AND $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
		$result1 = $sqlconn->query($qry1);

		if( mysqli_num_rows($result1) == 0 )
		{
			$v = $type[ $i ];
			$count->$v = 0;
		}
		else
		{
			$row1 = $result1->fetch_array();
			$v = $row1[0];
			
			$res = $sqlconn->query("SELECT $tillDate FROM $user ORDER BY $tillDate limit 1");
			$lim = mysqli_fetch_array($res)[0];
			//$lim = explode('-',$lim);
			$flag = false;

			$date = $year.'-'.$month.'-'.$day ;
			$date = decrDay( $date );

			$ar = explode('-',$date);
			$yr = $ar[0];
			$mth = $ar[1];
			$dy = $ar[2];

			while( strtotime($date) >= strtotime($lim) )
			{
				$qry = "SELECT * FROM $user WHERE $tillDate regexp \"$yr-$mth-$dy\" AND $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
				$result = $sqlconn->query($qry);
				if( mysqli_num_rows($result) != 0 )
				{
					$row2 = mysqli_fetch_array($result);
					$count->$v = $row1[1]-$row2[1];
					$flag = true;
					break;
				}

				$date = decrDay( $date );

				$ar = explode('-',$date);
				$yr = $ar[0];
				$mth = $ar[1];
				$dy = $ar[2];
			}

			if( !$flag )
				$count->$v = $row1[1];
		}

		$i = $i+1;
	}

	$sqlconn->close();
	return $count;
}

function totalApplnTillNow( $user )
{
	require "../../LocalSettings.php";
	require "../Globals.php";

	$sqlconn = new mysqli ( $eorgDBserver, $eorgDBuser, $eorgDBpasswd, $applnCount );

	$count = new stdClass();
	
	$type = countTypeOfAppln();
	$i = 0;

	while( $i < count($type) )
	{
		$qry = "SELECT * FROM $user WHERE $applnTy = \"$type[$i]\" ORDER BY $tillDate DESC limit 1";
		$result = $sqlconn->query($qry);
		
		if( mysqli_num_rows($result) == 0 )
		{
			$v = $type[ $i ];
			$count->$v = 0;
		}
		else
		{
			$row = mysqli_fetch_array($result);
			$v = $row[0];
			$count-> $v = $row[1];
		}
		
		$i = $i+1;
	}

	$sqlconn->close();
	return $count;
}

?>
