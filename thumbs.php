<?



$doc_root = "/Users/rmfokkema/Sites/Tao";
set_time_limit(1200);

/*$exif = read_exif_data($doc_root."/new/IMG_2993.JPG");
echo $exif['Orientation'];
exit;*/

if (is_dir("$doc_root/img"))
{
	$dir = opendir("$doc_root/img");
	while ($file = readdir($dir)) {
		if (is_file("$doc_root/img/$file")) {
		unlink("$doc_root/img/$file");
		}
	}
} else {
	mkdir($doc_root."/img");
}

///

if (is_dir("$doc_root/thumb"))
{
	$dir = opendir("$doc_root/thumb");
	while ($file = readdir($dir)) {
		if (is_file("$doc_root/thumb/$file")) {
		unlink("$doc_root/thumb/$file");
		}
	}
} else {
	mkdir($doc_root."/thumb");
}

//exit;

if (intval($_GET['r'])==-1) die("jeej");

$location = $doc_root."/img/";
$output_location = $doc_root."/thumb/";

function scan_dir($dir) {
    $files = array();   
    foreach (scandir($dir) as $file) {
        if ($file=="." | $file==".." | $file==".DS_Store") continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

function checkorientation($image)
{
	$exif = @exif_read_data($image);
	switch ($exif['Orientation'])
    {

        case 3:
            $image = imagerotate($image, 180, 0);
            
            break;

        case 6:
            $image = imagerotate($image, -90, 0);
            echo("$input 90 graden naar links!\n\n");
            break;

        case 8:
            $image = imagerotate($image, 90, 0);
            
            break;
    }
}

function createPhoto($input, $maxpx, $quality, $output)
{	
	$input = realpath($input);	
	
	$image = imagecreatefromjpeg($input);

	$width_org	= imagesx($image);
	$height_org	= imagesy($image);
	
	if ($width_org > $height_org)	
	{
		$height = $height_org * ($maxpx/$width_org);
		$image_p = imagecreatetruecolor($maxpx, $height);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $maxpx, $height, $width_org, $height_org);
	}
	else
	{
		$width = $width_org * ($maxpx/$height_org);
		$image_p = imagecreatetruecolor($width, $maxpx);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $maxpx, $width_org, $height_org);
	}

	checkorientation($image_p);
	imagejpeg($image_p, $output, $quality);
	imagedestroy($image_p);
}

function createThumb($input, $maxpx, $quality, $output)
{	
	$input = realpath($input);
	
	$image = imagecreatefromjpeg($input);

	$width_org	= imagesx($image);
	$height_org	= imagesy($image);
	
	$image_p = imagecreatetruecolor($maxpx, $maxpx);
	
	if ($height_org < $width_org)	
	{
		imagecopyresampled(	$image_p, $image, 0, 0, ($width_org*($maxpx/$height_org)-$maxpx)/2, 0,
							$maxpx, $maxpx, $height_org, $height_org);
	}
	elseif ($width_org < $height_org)
	{
		imagecopyresampled(	$image_p, $image, 0, 0, 0, ($height_org*($maxpx/$width_org)-$maxpx)/2,
							$maxpx, $maxpx, $width_org, $width_org);
	}
	else {
		imagecopyresampled(	$image_p, $image, 0, 0, 0, 0,
							$maxpx, $maxpx, $width_org, $height_org);
	}

	checkorientation($image_p);
	imagejpeg($image_p, $output, $quality);
	imagedestroy($image_p);
}

$imgs = scan_dir($doc_root."/new");


for ($i=0; $i<count($imgs); $i++) {
	$file = $doc_root."/new/".$imgs[$i];
	
	createPhoto($doc_root."/new/".$imgs[$i], 550, 100, $doc_root."/img/".rand().$imgs[$i]);
	createThumb($doc_root."/new/".$imgs[$i], 210, 210, $doc_root."/thumb/".rand().$imgs[$i]);
	//@unlink($doc_root."/new/".$imgs[$i]);
}