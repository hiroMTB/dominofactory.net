<?php

	/*
	
			get work project data and make catalogue for works.html
	
	*/

	// get $db	
	include_once("works_data.php");		
	$size = count($db);
	
	for ($i = 0; $i<$size; $i++) {
		
		$url	= $db[$i]['url'];
		$img	= $db[$i]['img'];
		$title	= $db[$i]['title'];
		$catch	= $db[$i]['catch'];
		$type	= $db[$i]['type'];
		$year	= $db[$i]['year'];				
		$format = $db[$i]['format'];
		$tech 	= $db[$i]['tech'];
		
		echo '<div class="artwork">';		

		echo		'<a href="works/' . $url . '.html" >' . $title . '</a>';

		echo 		'<div class="c1">';
		echo 			'<img src="http://dominofactory.net/images/thumb/' . $img . '" />';
		echo 		'</div>';		

		echo 		'<div class="c2">';
		echo 				'<span class="title">' . $title .'</span>';
		echo 		'</div>';		
		
		echo 		'<div class="c3">';
		echo 			'<span class="type">'. $type . '</span>';		
		echo 		'</div>';
		
		echo 		'<div class="c4">';		
		echo 			'<span class="catch">'. $catch . '</span>';
		echo 	 		'<span class="format">' . $format . '</span>';
		echo 	 		'<span class="tech">' . $tech . '</span>';
		echo 		'</div>';
		
		echo '</div>';
	}
?>
