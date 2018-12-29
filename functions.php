<?php

$DEVICE = "Mac";

if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone')) $DEVICE = 'iPhone';

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

function image_fix_orientation($filename)
    {
        $exif = exif_read_data($filename);
        if (!empty($exif['Orientation']))
        {
            $image = imagecreatefromjpeg($filename);
            switch ($exif['Orientation'])
            {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;

                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }

            imagejpeg($image, $filename, 90);
        }
    }

?>