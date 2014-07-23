<?php
#require_once('simple_html_dom.php');
// Create DOM from URL or file
$singleData = array(
				'processed/2013-08.json',
				'processed/2013-09.json',
				'processed/2013-10.json',
				'processed/2013-11.json',
				'processed/2013-12.json',
				'processed/2014-01.json',
				'processed/2014-02.json',
				'processed/2014-03.json',
				'processed/2014-04.json',
				'processed/2014-05.json',
				'processed/2014-06.json',
				'processed/2014-07.json',
				);
$doubleData = array(
		'processed/double-2013-08.json',
		'processed/double-2013-09.json',
		'processed/double-2013-10.json',
		'processed/double-2013-11.json',
		'processed/double-2013-12.json',
		'processed/double-2014-01.json',
		'processed/double-2014-02.json',
		'processed/double-2014-03.json',
		'processed/double-2014-04.json',
		'processed/double-2014-05.json',
		'processed/double-2014-06.json',
		'processed/double-2014-07.json',
		);

function getCount($word) {
	global $singleData;
	$array = array();
	foreach($singleData as $file) {
		$data = file_get_contents($file);
		$json = json_decode($data);
		$wordCount = $json -> $word;
		$array[] = $wordCount;
	}
	$array = implode(", ", $array);
	return $array;
}

function getCountDouble($string) {
	global $doubleData;
	$array = array();
	foreach($doubleData as $file) {
		$data = file_get_contents($file);
		$json = json_decode($data);
		if ($json -> $string) {
			$wordCount = $json -> $string;
			$array[] = $wordCount;
		}
		else {
			$array[] = 0;
		}
	}
	$array = implode(", ", $array);
	return $array;
}

function createChart($words) {
	$ratio = $_GET[ratio];
	if($ratio == "on") {
		global $singleData;
		global $sums;
		$sums = array();
		foreach($singleData as $file) {
			$data = file_get_contents($file);
			$json = json_decode($data);
			$sum = array_sum((array)$json);
			$sums[] = $sum;
		}
	}
	$count = count($words);
	$i = 0;
	foreach($words as $string) {
		createLine($string, $i, $ratio);
		if($i < $count - 1) {
			echo ", ";
		}
		$i++;
	}
}

function createLine($string, $color, $ratio) {
	$colors = array(
				array(
					'fillColor' => "rgba(220,220,220,0)",
					'strokeColor' => "rgb(241, 89, 95)",
					'pointColor' => "rgb(241, 89, 95)",
					'pointStrokeColor' => '#000000',
					'pointHighlightFill' => '#fff',
					'pointHighlightStroke' => '#0000000'
					),
				array(
					'fillColor' => "rgba(220,220,220,0)",
					'strokeColor' => 'rgb(121,196,106)',
					'pointColor' => 'rgb(121,196,106)',
					'pointStrokeColor' => '#cccccc',
					'pointHighlightFill' => '#fff',
					'pointHighlightStroke' => '#cccccc'
					),
				array(
					'fillColor' => "rgba(220,220,220,0)",
					'strokeColor' => 'rgb(89, 154, 211)',
					'pointColor' => 'rgb(89, 154, 211)',
					'pointStrokeColor' => '#eeeeee',
					'pointHighlightFill' => '#fff',
					'pointHighlightStroke' => '#eeeeee'
					),
				array(
					'fillColor' => "rgba(220,220,220,0)",
					'strokeColor' => 'rgb(249, 166, 90)',
					'pointColor' => 'rgb(249, 166, 90)',
					'pointStrokeColor' => '#eeeeee',
					'pointHighlightFill' => '#fff',
					'pointHighlightStroke' => '#eeeeee'
					),
				array(
					'fillColor' => "rgba(220,220,220,0)",
					'strokeColor' => 'rgb(158, 102, 171)',
					'pointColor' => 'rgb(158, 102, 171)',
					'pointStrokeColor' => '#eeeeee',
					'pointHighlightFill' => '#fff',
					'pointHighlightStroke' => '#eeeeee'
					)
				);
	if(strpos($string, ' ') !== false) {
		$array = getCountDouble($string);

	}
	else {
		$array = getCount($string);
	}
	if ($ratio) {
		global $sums;
		$array = explode(", ", $array);
		foreach($array as $index => $monthCount) {
			$array[$index] = $monthCount / $sums[$index];
		}
		$array = implode(", ", $array);
	}
	echo "
		{
			label: \"$string\",
			fillColor: \"" . $colors[$color][fillColor] . "\",
			strokeColor: \"" . $colors[$color][strokeColor] . "\",
			pointColor: \"" . $colors[$color][pointColor] . "\",
			pointStrokeColor: \"" . $colors[$color][pointColor] . "\",
			pointHighlightFill: \"" . $colors[$color][pointHighlightFill] . "\",
			pointHighlightStroke: \"" . $colors[$color][pointHighlightStroke] . "\",
			data: [ $array ]
				}";
	$i++;
}

?>

<!doctype html>
<html>
	<head>
		<title>HN Who's Hiring Trends</title>
		<script type="text/javascript" src="Chart.js"></script>
		<style>
			.line-legend {
				position: relative;
				top: -600px;
				left: 50px;
				list-style: none;
				font-family: helvetica, arial;
			}
			.line-legend span {
				background-color: rgb(255,0,0);
				height: 10px;
				width: 10px;
				display: block;
				float: left;
				position: relative;
				top: 5px;
				left: -5px;
			}
		</style>
	</head>
	<body>
		<form method="get">
			<input name="words[]" type="text"/>
			<input name="words[]" type="text"/>
			<input name="words[]" type="text"/>
			<input name="words[]" type="text"/>
			<label>ratio (straight wordcount if unchecked)</label><input name="ratio" type="checkbox" checked/>
			<input type="submit"/>
		</form>

		<canvas id="custom" width="600" height="600"></canvas>
		<div id="legendCustom"></div>

		<script>
		var options = {
			bezierCurve : false
		}

		var data = {
		    labels: ["August", "September", "October", "November", "December", "January", "February", "March", "April", "May", "June", "July"],
		    datasets: [
		    <?php 
		    if(isset($_GET[words])) {
				$words = $_GET[words];
				$words = array_filter($words,'strlen');
				$words = array_map('trim',$words);
				$words = array_map('strtolower',$words);
				createChart($words);
			}
		    ?>
		    ]};
		var canvas = document.getElementById("custom");
		var ctx = canvas.getContext("2d");
		var customChart =  new Chart(ctx).Line(data, options);
		document.getElementById("legendCustom").innerHTML = customChart.generateLegend();

		</script>
		<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-51541248-1', 'austinhutchison.com');
			  ga('send', 'pageview');

		</script>

	</body>