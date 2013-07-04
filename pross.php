<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
if (isset($_POST['submit']))
{
	echo "<!DOCTYPE html>
<html>
<head>
<style>
	table {display:block};
	p{display:block};
</style>
</head>
<body>";
	include './Include/PlaceLocator.php';
	include './Include/functions.php';

	echo "</br>You are located at <br/>";
	echo $ResArea.' '.$Reslat.' '.$Reslng.'<br />';
	echo 'Your zip code:'. $Reszip.'<br />';
	echo "<a href='http://iaspub.epa.gov/enviro/find.SDWIS?pLocation=".$Reszip."&x=9&y=10&pType=zip'>View Full List for your area.</a>";
	include './Include/WaterSorceLocator.php';
	

	



//echo '<br>Your Possible water source\'s are  
//<a href="http://oaspub.epa.gov/enviro/sdw_report_v2.first_table?pws_id=GA1530021&state=GA&source=Groundwater&population=64361&sys_num=0" scope="row">HOUSTON COUNTY-FEAGIN MILL</a>';

//32.5748385 -83.6329198
//32.6119010 -83.6424450

//echo '<br /><table>'.$homepage[1][0][0].'</table>';
	
}

?>