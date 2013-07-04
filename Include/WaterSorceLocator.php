<?PHP
//http://iaspub.epa.gov/enviro/efservice/PWS_COUNTY/FIPSCOUNTY/=/13153/PWS/ROWS/0:200/xml
$WSXML = simplexml_load_file("http://iaspub.epa.gov/enviro/efservice/PWS_COUNTY/FIPSCOUNTY/=/".$FIPScountyCode."/PWS/ROWS/0:500/xml");
	echo "<br/><br/>";
			//get the lat lng of the state to eliminte wrong results
		$StateLOC = FindThis($ResArea1);
		$i = 0;
		$WSinfo['probability']=array();
	foreach ($WSXML->PWS_COUNTY as $key => $value) {
			

		//check if active and if the PSW Type Matches the one given
		
    	if($value->PWS->PWS_ROW->STATUS == 'Active' AND $value->PWS->PWS_ROW->PWSTYPE == 'CWS')
		{
			$WSinfo[$i]['name'] 		= $value->PWS->PWS_ROW->PWSNAME;
			$WSinfo[$i]['PWSID'] 		= $value->PWS->PWS_ROW->PWSID;
			$WSinfo[$i]['state'] 		= $value->PWS->PWS_ROW->STATE;
			$WSinfo[$i]['RegName']		= $value->PWS->PWS_ROW->REGULATINGAGENCYNAME;
			$WSinfo[$i]['source']		= $value->PWS->PWS_ROW->PSOURCE_LONGNAME;
			$WSinfo[$i]['owner']		= $value->PWS->PWS_ROW->OWNER;
			$WSinfo[$i]['size']			= $value->PWS->PWS_ROW->SIZECAT5;
			$WSinfo[$i]['served']		= $value->PWS->PWS_ROW->RETPOPSRVD;
			$WSinfo[$i]['ContactName']	= $value->PWS->PWS_ROW->CONTACTORGNAME;
			$WSinfo[$i]['Phone']		= $value->PWS->PWS_ROW->CONTACTPHONE;
			$WSinfo[$i]['Address1']		= $value->PWS->PWS_ROW->CONTACTADDRESS1;
			$WSinfo[$i]['Address2']		= $value->PWS->PWS_ROW->CONTACTADDRESS2;
			$WSinfo[$i]['Address3']		= $value->PWS->PWS_ROW->CONTACTCITY.",".$value->PWS->PWS_ROW->CONTACTSTATE.",".$value->PWS->PWS_ROW->CONTACTZIP;
			
			// Lets initialize some variables for the Solve system here.
			$WSinfo[$i]['DistenceToGuess']	='0';
			$WSinfo[$i]['CityScore']		='0';
			$WSinfo[$i]['CounteyScore']		='0';
			//Lets find the locations
			sleep(.05);
			$WSinfo[$i]['Location'] = FindThis($value->PWS->PWS_ROW->PWSNAME.' , '.$FIPScountyName.' county'.' , '.$ResArea1);
			sleep(.05);
			if($WSinfo[$i]['Location'])
			{
				if ($StateLOC['lat'] == $WSinfo[$i]['Location']['lat'] && $StateLOC['lng'] == $WSinfo[$i]['Location']['lng'])
				{
					$WSPosibleMatches[]=$i;
				}
				//see if result is not in county
				//elseif ($FIPScountyName != $WSinfo[$i]['Location']['Area1'] || $FIPScountyName != $WSinfo[$i]['Location']['Area2'] )
				else
				{	
					/*echo $value->PWS->PWS_ROW->PWSNAME.','.$ResArea1.'<br/>';
					echo "Yes I found it";
					echo '<br/>'.$WSinfo[$i]['name'];
					echo '<br/>Area1:'.$WSinfo[$i]['Location']['Area1'];
					echo '<br/>Area2:'.$WSinfo[$i]['Location']['Area2'];
					echo '<br/>Area3:'.$WSinfo[$i]['Location']['Area3'];
					echo '<br/>Zip:'.$WSinfo[$i]['Location']['zip'];
					echo '<br/>latLng:'.$WSinfo[$i]['Location']['lat'].",".$WSinfo[$i]['Location']['lng']."</br>";
					*/
					$WSinfo[$i]['DistenceToGuess'] = distence ($WSinfo[$i]['Location']['lat'], $WSinfo[$i]['Location']['lng'],$Reslat,$Reslng);
					//echo $WSinfo[$i]['DistenceToGuess'];
					if ($WSinfo[$i]['DistenceToGuess']<10 && $WSinfo[$i]['DistenceToGuess']>4.9)
						{$WSLessThan10[]=$i;}
					if($WSinfo[$i]['DistenceToGuess']<5)
						{$WSLessThan5[]=$i;}
						
						//calc probability
						$WSD = $WSinfo[$i]['DistenceToGuess'];
						$WSS = $WSinfo[$i]['served'];
						if ($WSD<.5){
							$WSS=$WSS*4000;
						}elseif ($WSD<1){
							$WSS=$WSS*1000;
						}elseif ($WSD<1.5){
							$WSS=$WSS*100;
						}elseif ($WSD<2){
							$WSS=$WSS*10;
						}elseif ($WSD<5){
							$WSS=$WSS*4;
						}elseif ($WSD<10){
							$WSS=$WSS*2;
						}
						if ($WSD==0)
						{$WSD = 1;}
						$WSDS =	$WSS/$WSD ;
						$WSinfo['probability'][$WSDS] = $i;
				}
				
			}
			else {
				$WSPosibleMatches[]=$i;
			}
			$i++;
		}
	}
	
	//sort keys in reverse
	krsort($WSinfo['probability']);
	
		echo "<p>I found ".count($WSinfo['probability'])." Locations I have listed them in probability of being your water source.<hr>";
		echo '
		<table border="1">
		<tr>
			<th>Name</th>
			<th>Regulator</th>
			<th>Water Source</th>
			<th>Owner</th>
			<th>Size</th>
			<th>People Served</th>
			<th>Contacts Name</th>
			<th>Phone</th>
			<th>Address</th>
			<th>Prob</th>
		</tr>';
		foreach ($WSinfo['probability'] as $key => $value) {
			echo '<tr>';
			echo '<td><a href="http://oaspub.epa.gov/enviro/sdw_report_v2.first_table?pws_id='.$WSinfo[intval($value)]['PWSID'].'&state='.$WSinfo[intval($value)]['state'].'&source='.$WSinfo[intval($value)]['source'].'&population='.$WSinfo[intval($value)]['served'].'&sys_num=0">'	.$WSinfo[intval($value)]['name']		.'</a></td>';
			echo '<td>' .$WSinfo[intval($value)]['RegName']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['source']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['owner']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['size']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['served']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['ContactName']	.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['Phone']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['Address1']	.',';
			echo 		 $WSinfo[intval($value)]['Address2']	.',';
			echo 		 $WSinfo[intval($value)]['Address3'].'</td>';
			echo '<td>'	.$key									.'</td>';
			echo '</tr>';
			//echo '<br/>latLng:'.$WSinfo[$value]['Location']['lat'].",".$WSinfo[$value]['Location']['lng']."</br>";
		}
		echo "</table><br/></p>";
		
/*	if(isset($WSPosibleMatches))
	{
		sleep(1);
		foreach ($WSPosibleMatches as $key => $value) {
			//try one more time some times google dose not like to play nice
			sleep(.05);
			$WSinfo[$value]['Location'] = FindThis($WSinfo[$value]['name'] .' , '.$FIPScountyName.' county'.' , '.$ResArea1);
			sleep(.05);
			if($WSinfo[$value]['Location'])
			{
				if ($StateLOC['lat'] != $WSinfo[$value]['Location']['lat'] && $StateLOC['lng'] != $WSinfo[$value]['Location']['lng'])
				{
				 unset($WSPosibleMatches[$value]);
			$WSinfo[$value]['DistenceToGuess'] = distence ($WSinfo[$value]['Location']['lat'], $WSinfo[$value]['Location']['lng'],$Reslat,$Reslng);
					//echo $WSinfo[$i]['DistenceToGuess'];
					if ($WSinfo[$value]['DistenceToGuess']<10 && $WSinfo[$value]['DistenceToGuess']>4.9)
						{$WSLessThan10[]=$value;}
					if($WSinfo[$value]['DistenceToGuess']<5)
						{$WSLessThan5[]=$value;}
				}
			}
		}
	}
	
	if(isset($WSPosibleMatches))
	{
		sleep(2);
		//Lest try to find some of those bad ones
		foreach ($WSPosibleMatches as $key => $value) {
		//lets try with out Some stuff
			$WSStufftoRemove = array('WSC','WATER','CO-OP','CORPORATION','CITY','SUD');
			//try one more time some times google dose not like to play nice
			$newName = str_replace($WSStufftoRemove, "", $WSinfo[$value]['name']);
			sleep(.1);
			$WSinfo[$value]['Location'] = FindThis($newName .' , '.$FIPScountyName.' county'.' , '.$ResArea1);
			sleep(.1);
			if($WSinfo[$value]['Location'])
			{
				if ($StateLOC['lat'] != $WSinfo[$value]['Location']['lat'] && $StateLOC['lng'] != $WSinfo[$value]['Location']['lng'])
				{
				 unset($WSPosibleMatches[$value]);
			$WSinfo[$value]['DistenceToGuess'] = distence ($WSinfo[$value]['Location']['lat'], $WSinfo[$value]['Location']['lng'],$Reslat,$Reslng);
					//echo $WSinfo[$i]['DistenceToGuess'];
					if ($WSinfo[$value]['DistenceToGuess']<10 && $WSinfo[$value]['DistenceToGuess']>4.9)
						{$WSLessThan10[]=$value;}
					if($WSinfo[$value]['DistenceToGuess']<5)
						{$WSLessThan5[]=$value;}
				}
			}
		}
	}

	if (isset($WSLessThan5))
	{
		echo "<p>I found ".count($WSLessThan5)." less than 5 miles.<hr>";
		echo '
		<table border="1">
		<tr>
			<th>Name</th>
			<th>Regulator</th>
			<th>Water Source</th>
			<th>Owner</th>
			<th>Size</th>
			<th>People Served</th>
			<th>Contacts Name</th>
			<th>Phone</th>
			<th>Address</th>
		</tr>';
		foreach ($WSLessThan5 as $key => $value) {
			echo '<tr>';
			echo '<td>'	.$WSinfo[$value]['name']		.'</td>';
			echo '<td>' .$WSinfo[$value]['RegName']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['source']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['owner']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['size']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['served']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['ContactName']	.'</td>';
			echo '<td>'	.$WSinfo[$value]['Phone']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['Address1']	.',';
			echo 		 $WSinfo[$value]['Address2']	.',';
			echo 		 $WSinfo[$value]['Address3'].'</td>';
			echo '</tr>';
			//echo '<br/>latLng:'.$WSinfo[$value]['Location']['lat'].",".$WSinfo[$value]['Location']['lng']."</br>";
		}
		echo "</table><br/></p>";
	}
	if (isset($WSLessThan10))
	{
		echo "<p>I found ".count($WSLessThan10)." less than 10 miles.<hr></p>";
		echo '
		<table border="1" >
		<tr>
			<th>Name</th>
			<th>Regulator</th>
			<th>Water Source</th>
			<th>Owner</th>
			<th>Size</th>
			<th>People Served</th>
			<th>Contacts Name</th>
			<th>Phone</th>
			<th>Address</th>
		</tr>';
		foreach ($WSLessThan10 as $key => $value) {
			echo '<tr>';
			echo '<td>'	.$WSinfo[$value]['name']		.'</td>';
			echo '<td>' .$WSinfo[$value]['RegName']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['source']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['owner']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['size']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['served']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['ContactName']	.'</td>';
			echo '<td>'	.$WSinfo[$value]['Phone']		.'</td>';
			echo '<td>'	.$WSinfo[$value]['Address1']	.',';
			echo 		 $WSinfo[$value]['Address2']	.',';
			echo 		 $WSinfo[$value]['Address3'].'</td>';
			echo '</tr>';
			//echo '<br/>latLng:'.$WSinfo[$value]['Location']['lat'].",".$WSinfo[$value]['Location']['lng']."</br>";
		}echo '</table><br/>';
	}

*/	

	if(isset($WSPosibleMatches))
	{
	echo "<p>I found ".count($WSPosibleMatches)." sources that I was unable to locate.<hr>";
	echo '
		<table border="1">
		<tr>
			<th>Name</th>
			<th>Regulator</th>
			<th>Water Source</th>
			<th>Owner</th>
			<th>Size</th>
			<th>People Served</th>
			<th>Contacts Name</th>
			<th>Phone</th>
			<th>Address</th>
		</tr>';
		foreach ($WSPosibleMatches as $key => $value) {
			echo '<tr>';
			echo '<td><a href="http://oaspub.epa.gov/enviro/sdw_report_v2.first_table?pws_id='.$WSinfo[intval($value)]['PWSID'].'&state='.$WSinfo[intval($value)]['state'].'&source='.$WSinfo[intval($value)]['source'].'&population='.$WSinfo[intval($value)]['served'].'&sys_num=0">'	.$WSinfo[intval($value)]['name']		.'</a></td>';
			echo '<td>' .$WSinfo[intval($value)]['RegName']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['source']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['owner']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['size']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['served']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['ContactName']	.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['Phone']		.'</td>';
			echo '<td>'	.$WSinfo[intval($value)]['Address1']	.',';
			echo 		 $WSinfo[intval($value)]['Address2']	.',';
			echo 		 $WSinfo[intval($value)]['Address3'].'</td>';
			echo '</tr>';
			//echo '<br/>latLng:'.$WSinfo[$value]['Location']['lat'].",".$WSinfo[$value]['Location']['lng']."</br>";
		}echo '</table><br/></p>';
	}
	//
	
	//Does the name contain a city or county +10 to either area
	
	//Ask Google if it knows were the name is with county,state tacked on
	
	//ask Google if it knows were that would be if it was a road 
	
	//once Google knows were the name is see if it is with a city or not.
	//else just added it to the possible list.

?>