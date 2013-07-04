<?PHP

	$completeurl = $_POST['location'];
	
	//$completeurl = "http://maps.googleapis.com/maps/api/geocode/xml?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true";
	$completeurl='http://maps.googleapis.com/maps/api/geocode/xml?address='.$completeurl.'&sensor=true';
	$xml 	= simplexml_load_file($completeurl);
	if(!isset($xml->result->geometry->location->lat)||!isset($xml->result->geometry->location->lng))
	{
		die('I am sorry we were unable to find a location for that.<br />'.$completeurl);
	}
	
	$lat 	= $xml->result->geometry->location->lat;
	$lng 	= $xml->result->geometry->location->lng;

	//do a for loop here to look for the right data
	$i=0;
	foreach ($xml->result->address_component as $key => $value) {
		if ($xml->result->address_component[$i]->type[0]=='locality')
		{
			$HArea=	$xml->result->address_component[$i]->long_name;
		}
		if ($xml->result->address_component[$i]->type[0]=='administrative_area_level_1')
		{
			$Area=	$xml->result->address_component[$i]->long_name;
		}
		if ($xml->result->address_component[$i]->type[0]=='postal_code')
		{
			$Reszip=	$xml->result->address_component[$i]->short_name;
		}
		
		$i++;
	}
	//catch all just in case Google did not give us these
	if(!isset($HArea))
	{
		$HArea="";
	}
	if(!isset($Area))
	{
		$Area="";
	}

	if(!isset($Reszip))
	{
		$completeurl='http://maps.googleapis.com/maps/api/geocode/xml?address='.$lat.','.$lng.'&sensor=true';
		$xml 	= simplexml_load_file($completeurl);
		if(!isset($xml->result->geometry->location->lat)||!isset($xml->result->geometry->location->lng))
		{
			die('I am sorry, I was not able to find that location please try with more detail like state,city or county.');
		}
		$lat 	= $xml->result->geometry->location->lat;
		$lng 	= $xml->result->geometry->location->lng;
		//do a for loop here to look for the right data
		$i=0;
		foreach ($xml->result->address_component as $key => $value) {
			if ($xml->result->address_component[$i]->type[0]=='postal_code')
			{$Reszip=	$xml->result->address_component[$i]->short_name;}
			$i++;
		}
		  
	}
	//clean the data never trust anyones data OWASP Rule
	//stops cross side scripting
	$pattern = '/[^a-zA-Z0-9\. -]/';
	$replacement = '';
	$ResArea1 = preg_replace($pattern, $replacement,$Area);
	$Reslat = preg_replace($pattern, $replacement,$lat);
	$Reslng = preg_replace($pattern, $replacement,$lng);

	$ResArea = $HArea.','.$ResArea1;
	
	
	
	//Lest get the FIPS County code
	$FIPS = simplexml_load_file("http://data.fcc.gov/api/block/find?format=xml&latitude=".$Reslat."&longitude=".$Reslng."&showall=true");
	$FIPScounty = $FIPS->County[0]-> attributes();
	$FIPScountyCode = $FIPScounty['FIPS'];
	$FIPScountyName = $FIPScounty['name'];
	
	
	
	
	
?>