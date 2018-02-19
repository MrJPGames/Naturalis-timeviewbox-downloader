<?php
	echo "Started downloading images";
	$baseURL = "http://naturalis.timeboxview.com/api/project/naturalis/images/{%DATE%}/dayforward";
	//Format: {%YEAR%}-{%MONTH%}-{%DAY%}_{%HOUR%}-{%MINUTE%}-{%SECOND%}

	$startDateTime = "2017-05-01";
	$endDateTime = date_create(date("Y-m-d H:i:s"));
	//$endDateTime = date_create("2017-02-16 15:00");

	$currentTimeFrame = date_create($startDateTime);
	while ($currentTimeFrame < $endDateTime){
		$url = str_replace('{%DATE%}', $currentTimeFrame->format('Y-m-d'), $baseURL);
		$test = shell_exec("wget -O Downloads/temp/json.json " . $url . " >nul 2>&1");
		echo "Downloaded JSON from: " . $url . "\n";
		$string = file_get_contents("Downloads/temp/json.json");
		$jsonData = json_decode($string, true);
		$imagesData = $jsonData["images"];
		$i=0;
		foreach ($imagesData as $image){
			if ($i == 64){
				echo "Getting image for " . $image["date"] . " " . $image["time"] . "\n";
				$dURL = $image["full"];
				shell_exec("wget -O Downloads/" . $image["date"] . "_" . $i . '.jpg "' . $dURL . '" --no-check-certificate >nul 2>&1');
			}
			$i++;
		}
		$currentTimeFrame = date_create($jsonData["images"][0]["date"]);
	}
?>