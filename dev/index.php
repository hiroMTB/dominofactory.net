<?php
/********************************
Simple PHP File Manager
Copyright John Campbell (jcampbell1)
https://github.com/jcampbell1/simple-file-manager
Liscense: MIT
********************************/

/* Uncomment section below, if you want a trivial password protection */

/*
$PASSWORD = 'sfm';
session_start();
if(!$_SESSION['_sfm_allowed']) {
	// sha1, and random bytes to thwart timing attacks.  Not meant as secure hashing.
	$t = bin2hex(openssl_random_pseudo_bytes(10));	
	if($_POST['p'] && sha1($t.$_POST['p']) === sha1($t.$PASSWORD)) {
		$_SESSION['_sfm_allowed'] = true;
		header('Location: ?');
	}
	echo '<html><body><form action=? method=post>PASSWORD:<input type=password name=p /></form></body></html>'; 
	exit;
}
*/

// must be in UTF-8 or `basename` doesn't work
setlocale(LC_ALL,'en_US.UTF-8');

$tmp = realpath($_REQUEST['file']);
if($tmp === false)
	err(404,'File or Directory Not Found');
if(substr($tmp, 0,strlen(__DIR__)) !== __DIR__)
	err(403,"Forbidden");

if(!$_COOKIE['_sfm_xsrf'])
	setcookie('_sfm_xsrf',bin2hex(openssl_random_pseudo_bytes(16)));
if($_POST) {
	if($_COOKIE['_sfm_xsrf'] !== $_POST['xsrf'] || !$_POST['xsrf'])
		err(403,"XSRF Failure");
}

$file = $_REQUEST['file'] ?: '.';
if($_GET['do'] == 'list') {
	if (is_dir($file)) {
		$directory = $file;
		$result = array();
		$files = array_diff(scandir($directory), array('.','..'));
	    foreach($files as $entry) if($entry !== basename(__FILE__)) {

    		$i = $directory . '/' . $entry;
	    
		    if(basename($i)[0] != '.') {
		    	$stat = stat($i);
		        $result[] = array(
		        	'mtime' => $stat['mtime'],
		        	'size' => $stat['size'],
		        	'name' => basename($i),
		        	'path' => preg_replace('@^\./@', '', $i),
		        	'is_dir' => is_dir($i),
		        	'is_deleteable' => (!is_dir($i) && is_writable($directory)) || 
		        					   (is_dir($i) && is_writable($directory) && is_recursively_deleteable($i)),
		        	'is_readable' => is_readable($i),
		        	'is_writable' => is_writable($i),
		        	'is_executable' => is_executable($i),
		        	'type' => mime_content_type($i)
		        );
		    }
	    }
	} else {
		err(412,"Not a Directory");
	}
	echo json_encode(array('success' => true, 'is_writable' => is_writable($file), 'results' =>$result));
	exit;
}

function is_recursively_deleteable($d) {
	$stack = array($d);
	while($dir = array_pop($stack)) {
		if(!is_readable($dir) || !is_writable($dir)) 
			return false;
		$files = array_diff(scandir($dir), array('.','..'));
		foreach($files as $file) if(is_dir($file)) {
			$stack[] = "$dir/$file";
		}
	}
	return true;
}

function err($code,$msg) {
	echo json_encode(array('error' => array('code'=>intval($code), 'msg' => $msg)));
	exit;
}

function asBytes($ini_v) {
	$ini_v = trim($ini_v);
	$s = array('g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10);
	return intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
}
$MAX_UPLOAD_SIZE = min(asBytes(ini_get('post_max_size')), asBytes(ini_get('upload_max_filesize')));
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head profile="http://gmpg.org/xfn/11">
	<title>DOMINO FACTORY | dev</title>
	<?php include_once("../php/head_include.php") ?>			
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="../css/style.css" media="screen" /> 

	<style>
		#filemanager {
			font-size: 14px;
			text-transform:none;
			font-weight: 100;
			line-height: 1.0;
		 	letter-spacing: 0em;
		}
		
		th {font-weight: normal; color: #444; padding:.5em 1em .9em .2em; 
			text-align: left;cursor:pointer;user-select: none;}
		th .indicator {margin-left: 6px }
		thead {border-bottom: 1px solid #999; }
		#top {height:80px;}
		label { display:block; color:#755;}
		#breadcrumb { padding:10px 0; display:inline-block;float:left;}
		.sort_hide{ display:none;}
		table {width:100%;}
		thead {max-width: 1024px}
		td { padding:.2em 1em .2em .2em; height:40px; white-space: nowrap;}
		td.first {white-space: normal; }
		td.empty { color:#777; font-style: italic; text-align: center;padding:3em 0;}
		.is_dir .size {color:transparent;font-size:0;}
		/* .is_dir .size:before {content: "-"; color:#333; font-size:14px;} */
		.name { padding:15px 0 10px 00px;}
		.is_dir .name { padding:15px 0 10px 0px;}
	</style>

	<!-- start js code -->
	<script>
	(function($){
		$.fn.tablesorter = function() {
			var $table = this;
			this.find('th').click(function() {
				var idx = $(this).index();
				var direction = $(this).hasClass('sort_asc');
				$table.tablesortby(idx,direction);
			});
			return this;
		};
		$.fn.tablesortby = function(idx,direction) {
			var $rows = this.find('tbody tr');
			function elementToVal(a) {
				var $a_elem = $(a).find('td:nth-child('+(idx+1)+')');
				var a_val = $a_elem.attr('data-sort') || $a_elem.text();
				return (a_val == parseInt(a_val) ? parseInt(a_val) : a_val);
			}
			$rows.sort(function(a,b){
				var a_val = elementToVal(a), b_val = elementToVal(b);
				return (a_val > b_val ? 1 : (a_val == b_val ? 0 : -1)) * (direction ? 1 : -1);
			})
			this.find('th').removeClass('sort_asc sort_desc');
			$(this).find('thead th:nth-child('+(idx+1)+')').addClass(direction ? 'sort_desc' : 'sort_asc');
			for(var i =0;i<$rows.length;i++)
				this.append($rows[i]);
			this.settablesortmarkers();
			return this;
		}
		$.fn.retablesort = function() {
			var $e = this.find('thead th.sort_asc, thead th.sort_desc');
			if($e.length)
				this.tablesortby($e.index(), $e.hasClass('sort_desc') );
			
			return this;
		}
		$.fn.settablesortmarkers = function() {
			this.find('thead th span.indicator').remove();
			this.find('thead th.sort_asc').append('<span class="indicator">&darr;<span>');
			this.find('thead th.sort_desc').append('<span class="indicator">&uarr;<span>');
			return this;
		}
	})(jQuery);
	$(function(){
		var XSRF = (document.cookie.match('(^|; )_sfm_xsrf=([^;]*)')||0)[2];
		var MAX_UPLOAD_SIZE = <?php echo $MAX_UPLOAD_SIZE ?>;
		var $tbody = $('#list');
		$(window).bind('hashchange',list).trigger('hashchange');
		$('#table').tablesorter();
	
		function list() {
			var hashval = window.location.hash.substr(1);
			$.get('?',{'do':'list','file':hashval},function(data) {
				$tbody.empty();
				$('#breadcrumb').empty().html(renderBreadcrumbs(hashval));
				if(data.success) {
					$.each(data.results,function(k,v){
						$tbody.append(renderFileRow(v));
					});
					!data.results.length && $tbody.append('<tr><td class="empty" colspan=5>This folder is empty</td></tr>')
					data.is_writable ? $('body').removeClass('no_write') : $('body').addClass('no_write');
				} else {
					console.warn(data.error.msg);
				}
				$('#table').retablesort();
			},'json');
		}
		function renderFileRow(data) {
			var $link = $('<a class="name" />')
				.attr('href', data.is_dir ? '#' + data.path : './'+data.path)
				.text(data.name);
			//var perms = [];
			//if(data.is_readable) perms.push('r');　else perms.push('-');
			//if(data.is_writable) perms.push('w');  else perms.push('-');
			//if(data.is_executable) perms.push('x'); else perms.push('-');
			var $html = $('<tr />')
				.addClass(data.is_dir ? 'is_dir' : '')
				.append( $('<td class="first" />').append($link) )
				.append( $('<td/>').attr('data-sort',data.is_dir ? -1 : data.size)
					.html($('<span class="size" />').text(formatFileSize(data.size))) ) 
				.append( $('<td/>').text(data.type) )
				.append( $('<td/>').attr('data-sort',data.mtime).text(formatTimestamp(data.mtime)) )
				//.append( $('<td/>').text(perms.join('')) )
				
				//.append( $('<td/>').append($dl_link).append( data.is_deleteable ? $delete_link : '') )
			return $html;
		}
		function renderBreadcrumbs(path) {
			var base = "",
				$html = $('<div/>').append( $('<a href=#>dev</a></div>') );
			$.each(path.split('/'),function(k,v){
				if(v) {
					$html.append( $('<span/>').text(' ▸ ') )
						.append( $('<a/>').attr('href','#'+base+v).text(v) );
					base += v + '/';
				}
			});
			return $html;
		}
		function formatTimestamp(unix_timestamp) {
			var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			var d = new Date(unix_timestamp*1000);
			return [m[d.getMonth()],' ',d.getDate(),', ',d.getFullYear()," ",
				(d.getHours() % 12 || 12),":",(d.getMinutes() < 10 ? '0' : '')+d.getMinutes(),
				" ",d.getHours() >= 12 ? 'PM' : 'AM'].join('');
		}
		function formatFileSize(bytes) {
			var s = ['bytes', 'KB','MB','GB','TB','PB','EB'];
			for(var pos = 0;bytes >= 1000; pos++,bytes /= 1024);
			var d = Math.round(bytes*10);
			return pos ? [parseInt(d/10),".",d%10," ",s[pos]].join('') : bytes + ' bytes';
		}
	})		
	</script>
</head>

<body>
	<div id="container">
		<?php include_once("../php/menu.php") ?>
	
		<div id="filemanager">
			<div id="top">	
				<div id="breadcrumb">&nbsp;</div>
			</div>
	
		<table id="table">
			<thead>
			<tr>
				<th>Name</th>
				<th>Size</th>
				<th>Type</th>
				<th>Date Modified</th>
				<!--<th>Perm</th>-->
			</tr>
			</thead>
		
			<tbody id="list"></tbody>
		</table>
	</div>
</div>

</body>

</html>
