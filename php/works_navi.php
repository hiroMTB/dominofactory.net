<?php

	// get $db	
	include_once("works_data.php");		
	
	$numNavi = 5;
	$size = count($db);
	$choice = array($size);
	$try = 0;

	echo '<div class="works_navi">';
	
	for ($i = 0; $i<$numNavi; $i++) {		
		$index = 0;
		do{
			$index = rand(0, $size-1);
		}while($choice[$index] != 0);

		$choice[$index] = 1;
		$url	= $db[$index]['url'];
		$img	= $db[$index]['img'];
		$title	= $db[$index]['title'];
		$type	= $db[$index]['type'];
		
		echo '<div class="artwork">';
		echo 	'<a href="works/' . $url . '.html" >';
		echo 		'<img src="http://dominofactory.net/images/thumb/' . $img . '" />';
		echo 		'<div class="caption">';
		echo 			'<p class="title">' . $title .'</p>';
		echo 			'<p class="type">' . $type . '</p>';
		echo		'</div>';
		echo 	'</a>';	
		echo '</div>';
	}
	
	echo '</div> <!-- end of works_navi -->';

?>
