<?php
	$options = getopt("t:n:i:h");

	if (!is_dir("Downloads")){
		echo "Download folder not found, creating folder!";
		mkdir("Downloads");
		mkdir("Downloads/temp");
	}

	if (isset($options["h"])){
		echo "Time Writers snapshot downloader help:", PHP_EOL, PHP_EOL,
			"-h           : Display this help screen", PHP_EOL,
			"-n %NAME%    : Give the project name can be found in the URL", PHP_EOL,
			"               http://example.timeboxview.com, in which case", PHP_EOL,
			"               example would be the project name.", PHP_EOL,
			"-i %MINUTES% : Interval in minutes inbetween downloaded images.", PHP_EOL,
			"               Minimum is 5 minutes, and all intervals will be", PHP_EOL,
			"               rounded to the nearest division of 5 as the", PHP_EOL,
			"               service only provides an image every 5 minutes.", PHP_EOL,
			"-t %OPTION%  : Choose if you want to download thumbnails or", PHP_EOL,
			"               full size images. (thumb for thumbnails and full", PHP_EOL,
			"               for full size images). If this is not set full is", PHP_EOL,
			"               default", PHP_EOL, PHP_EOL, PHP_EOL,
			"This script is not associated with Time Writers and is provided", PHP_EOL,
			"as is. It was created by Jasper Peters and falls under the GPL 3", PHP_EOL,
			"License.", PHP_EOL;
			exit;
	}

	if (!isset($options["n"])){
		echo "Name of project is required!", PHP_EOL, 
			"Example of command: php downloader.php -n naturalis", PHP_EOL,
			"For more info use: php downloader.php -h", PHP_EOL;
		exit;
	}

	echo "Started downloading images", PHP_EOL;
	$baseURL = "http://" . $options["n"] . ".timeboxview.com/api/project/" . $options["n"] . "/images/{%DATE%}/";
	//Format: {%YEAR%}-{%MONTH%}-{%DAY%}

	$currentDate = date_create(date("Y-m-d H:i:s"));

	//Enter current date into URL
	$url = str_replace('{%DATE%}', $currentDate->format('Y-m-d'), $baseURL);
	//Get API data for current date
	$test = shell_exec("wget -O Downloads/temp/json.json " . $url . " >nul 2>&1");

	//Create PHP object from data
	$string = file_get_contents("Downloads/temp/json.json");
	$jsonData = json_decode($string, true);

	//Get all available dates from the jsonData
	$dates = $jsonData["dates"];
	if ($dates == NULL){
		echo "No data found, the project name is probably incorrect!", PHP_EOL;
		exit;
	}

	if (!isset($options["i"])){
		$interval = 1;
	}else{
		$interval = round((int)$options["i"]/5);
		if ($interval <= 0){
			$interval = 1;
			echo "WARNING: Wrong interval was given interval was set to 5 minutes!";
		}
	}

	//Init J (current day) and dateSize (total amount of days to download)
	$j=0;
	$dateSize = count($dates);
	foreach ($dates as $date){
		$j++;
		//Modify URL to make API call for day currently downloading
		$url = str_replace('{%DATE%}', $date, $baseURL);
		$test = shell_exec("wget -O Downloads/temp/json.json " . $url . " >nul 2>&1");
		echo "Downloaded JSON from: " . $url . PHP_EOL;
		$string = file_get_contents("Downloads/temp/json.json");
		$jsonData = json_decode($string, true);
		//Get image data for current day (includes full and thumb images)
		$imagesData = $jsonData["images"];
		$i=0;
		foreach ($imagesData as $image){
			if ($i % $interval == 0){
				$dURL = $image["full"];
				$name = $image["date"] . '_' . $i . '.jpg';
				if (isset($options["t"]) && strtolower($options["t"]) === "thumb"){
					$dURL = $image["thumb"];
					$name = "thumb_" . $image["date"] . '_' . $i . '.jpg';
					echo "Getting thumbnail image for " . $image["date"] . " " . $image["time"] . " day " . $j . " of " . $dateSize . PHP_EOL;
				}else{
					echo "Getting image for " . $image["date"] . " " . $image["time"] . " day " . $j . " of " . $dateSize . PHP_EOL;
				}
				$result = shell_exec('wget -O Downloads/' . $name .  ' "' . $dURL . '" --no-check-certificate >nul 2>&1');
			}
			$i++;
		}
	}
?>