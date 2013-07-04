<?PHP

function FindThis($URL){
		//Google gets mad if we spam then and gives 0 results.
	
		$URL='http://maps.googleapis.com/maps/api/geocode/xml?address='.$URL.'&sensor=true';
		sleep(.125);
		$xml 	= simplexml_load_file($URL);
		if(!isset($xml->result->geometry->location->lat)||!isset($xml->result->geometry->location->lng))
		{
		return false;
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
			if ($xml->result->address_component[$i]->type[0]=='administrative_area_level_2')
			{
				$Area2=	$xml->result->address_component[$i]->long_name;
			}
			if ($xml->result->address_component[$i]->type[0]=='administrative_area_level_3')
			{
				$Area3=	$xml->result->address_component[$i]->long_name;
			}
			if ($xml->result->address_component[$i]->type[0]=='postal_code')
			{
				$zip=	$xml->result->address_component[$i]->short_name;
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
		
		if(!isset($Area2))
		{
			$Area2="";
		}
	
		if(!isset($Area3))
		{
			$Area3="";
		}
	
		if(!isset($zip))
		{
			$zip = '';
		}
		//clean the data never trust anyones data OWASP Rule
		//stops cross side scripting
		$pattern = '/[^a-zA-Z0-9\. -]/';
		$replacement = '';
		$Area = preg_replace($pattern, $replacement,$Area);
		$lat = preg_replace($pattern, $replacement,$lat);
		$lng = preg_replace($pattern, $replacement,$lng);
		
		return array('zip' => $zip, 'lat' => $lat,'lng' =>$lng,'Area1' =>$Area,'Area2' =>$Area2,'Area3' =>$Area3);
	}


function distence ($x1,$y1,$x2 ,$y2) {
   $R = 3959; // earth's mean radius in miles
   $dLat  = deg2rad($x2 - $x1);
   $dLong = deg2rad($y2 - $y1);

   $a = sin($dLat/2) * sin($dLat/2) +
          cos(deg2rad($x1)) * cos(deg2rad($x2)) * sin($dLong/2) * sin($dLong/2);
   $c = 2 * atan2(sqrt($a), sqrt(1-$a));
   $d = $R * $c;

  return $d; //distence in miles
}


?>