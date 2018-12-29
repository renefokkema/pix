<?php
include("functions.php");;

$thumbs = scan_dir($_SERVER["DOCUMENT_ROOT"]."/thumb/");
shuffle($thumbs);

$num_pix = count($thumbs);
$thumb_width = ($DEVICE=='Mac') ? 105 : 80;
$info_padding = ($DEVICE=='Mac') ? '5em' : 0;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--, viewport-fit=cover -->
	<?	
		if (isset($_GET["r"]))
		{
			if ($num_pix>intval($_GET["r"])) {
				echo "<meta http-equiv=\"refresh\" content=\"2.5;url=/?r=".$num_pix."\">";
			}
			if ($num_pix==intval($_GET["r"])) {
				echo "<meta http-equiv=\"refresh\" content=\"0;url=/\">";
			}

		}
	?>
	<link rel="stylesheet" type="text/css" href="main.css">
	<title>René Fokkema</title>
	<script type="text/javascript">
	<?	if (!$thumbs)
		{
			echo "document.getElementById('main').innerHTML = 'Er zijn geen foto\'s.'";
		}
	?>

		var thumbs;
		var numbers = new Array();
		var counter = 0;
		var info = 0;
		var current;

		function show_exif(photo)
		{
			document.getElementById('info').innerHTML = '<pre>' + photo.attributes['exif'].value + '</pre>';
			document.getElementById('main').style = 'display: none';
		}

		function check_arrows(event)
		{
			// 	switch (event.code)
			// {
			// 	case 'ArrowRight':
			// 		if (current<thumbs.length)
			// 		{
			// 			item = document.getElementById(++current).attributes.name.value;
			// 			document.getElementById('info').innerHTML = '<img src="/img/' + item + '">' : '';
			// 		}
			// 	break;

			// 	case 'ArrowLeft':
			// 		if (current>0)
			// 		{
			// 			item = document.getElementById(--current).attributes.name.value;
			// 			document.getElementById('info').innerHTML = '<img src="/img/' + item + '">' : '';
			// 		}
			// 	break;
			// }			
		}

		function toggle_info(id)
		{
			current = id ? id : current;

			transition = ['none', 'opacity', 'none'];
			opacity = [0, 1, 0];

			info = (info==0) ? 1 : 0;

			if (id)
			{
				item = document.getElementById(id).attributes.name.value;
				document.getElementById('info').innerHTML = info ? '<img src="/img/' + item + '">' : '';
			}

			//document.getElementById('info').style['transition-property'] = transition[info];
			document.getElementById('info').style.opacity = info;
			
			//document.getElementById('main').style['transition-property'] = transition[info+1];
			document.getElementById('main').style.opacity = opacity[info+1];
		}

		function shuffle(a) {
	    var j, x, i;
	    for (i = a.length - 1; i > 0; i--) {
	        j = Math.floor(Math.random() * (i + 1));
	        x = a[i];
	        a[i] = a[j];
	        a[j] = x;
	    }
	    return a;
		}

		function do_transition()
		{

			if (counter<thumbs.length) {
				thumbs[numbers[counter]].style.visibility='visible';
				thumbs[numbers[counter++]].style.opacity=1;
			} else {
				return;
			}

			setTimeout(function(){do_transition();}, 9);
		}

		function init()
		{
		//document.getElementById('main').style = 'width: ' + screen.width + 'px';
		//alert(screen.width);
		
		thumbs = document.getElementsByTagName("img");

		<? if (is_numeric($_GET['r'])) { ?>

			for (i=0; i<thumbs.length; i++) {
				thumbs[i].style.visibility = 'visible';
				thumbs[i].style.opacity = 1;
			}			
		 
		<? } else { ?>

		for (i=0; i<thumbs.length; i++) { numbers[i] = i; }
		numbers = shuffle(numbers);
		do_transition();
		
		<? } ?>

		}
	</script>
</head>	
<body onload="init()" onkeyup="check_arrows(event)">

	<div id="info" style="padding: <?= $info_padding ?> " onclick="toggle_info()"></div>

	<div id="main">

<?php
	if ($thumbs) {
		$i = 0;
		foreach ($thumbs as $thumb) {
			$exif = @read_exif_data($_SERVER["DOCUMENT_ROOT"]."/new/$thumb");
			$d = $exif['DateTimeOriginal'];
			if (!$d) {
				$d = $exif['FileDateTime'];
				$d = date("j/n/y (D)", $d);
			}

			if (is_numeric($_GET["r"]))
			{
				echo "\t<img id=\"$i\" style=\"width: ".$thumb_width."px; opacity: .3; -webkit-transition-duration: .5s\" src=\"thumb/$thumb\">\n";
			} else {
			echo "\t<img id=\"$i\" style=\"width: ".$thumb_width."px; opacity: ." . rand(0,100) . "; -webkit-transition-duration: " . round(3/rand(1,9), 2) . "s\" onclick=\"toggle_info(this.id)\" src=\"thumb/$thumb\" exif=\"" . str_replace('"', '\'', print_r($exif, true)) . "\" title=\"" . $d . "\" name=\"$thumb\">\n";
			}

			$i++;

		}
	}
?>

	</div>

</body>
</html>