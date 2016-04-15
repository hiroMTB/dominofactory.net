<?php

	$db = array(
		array('dial_2013_update','dial_2013_update_t.jpg'),
		array('TORUKU', 'TORUKU_t.jpg', 'TORUKU', 'tablet application'),
		array('dial_in_c', 'dial_in_c_t.jpg', 'dial in c', 'audiovisual'),
		array('Overbug', 'Overbug_t.jpg', 'Overbug', 'audiovisual'),
		array('spiro_composition_3' , 'spiro_3_t.jpg', 'spiro composition 3', 'audiovisual'),
		array('spiro_composition_2' , 'spiro_2_t.jpg', 'spiro composition 2', 'audiovisual'),
		array('spiro_composition_1' , 'spiro_1_t.jpg', 'spiro composition 1', 'audiovisual'),
		array('Binary_Rhythm_Synthesis' , 'soundwork2_t.jpg', 'Binary Rhythm Synthesis', 'music'),
		array('hypernature' , 'soundwork1_t.jpg', 'hypernature', 'music'),
		array('light_valve_study_1' , 'light_valve_study_1_t.jpg', 'light valve study 1', 'audiovisual installation'),
		array('dial_static' , 'dial_static_t.jpg', 'dial static', 'digital iamge'),
		array('DENKI_DOMINO' , 'denkidomino_t.jpg', 'DENKI DOMINO', 'device')
	);

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
		$url	= $db[$index][0];
		$img	= $db[$index][1];
		$title	= $db[$index][2];
		$type	= $db[$index][3];
		
		echo '<div class="artwork">';
		echo 	'<a href="works/' . $url . '.html" >';
		echo 		'<div class="artwork_image">';
		echo 			'<img src="http://dominofactory.net/images/thumb/' . $img . '" />';
		echo		'</div>';
		echo 		'<div class="caption">';
		echo 			'<p class="title">' . $title .'</p>';
		echo 			'<p class="type">' . $type . '</p>';
		echo		'</div>';

		echo 	'</a>';
	
		echo '</div>';
	}
	
	echo '</div> <!-- end of works_navi -->';

?>




<!--
	<div class="artwork">
		<a href="works/simha.html">
			<div class="artwork_image"><img src="http://dominofactory.net/images/thumb/" /></div>
			<div class="caption">
				<p class="title">simha</p>
				<p class="type">rhythm</p>
			</div>
		</a>
	</div>			
-->