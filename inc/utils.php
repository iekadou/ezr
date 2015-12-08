<?php

function raise404()
{
    global $INIT_LOADED;
    header("HTTP/1.0 404 Not Found");
    include(PATH."views/_errors/404.php");
}

function fixOrientation($img) {
  $exif = @exif_read_data($img);
  if (isset($exif['Orientation'])) {
      $orientation = $exif['Orientation'];
      switch($orientation) {
          case 6: // rotate 90 degrees CW
              $degrees = -90;
          break;
          case 8: // rotate 90 degrees CCW
             $degrees = 90;
          break;

      }
      if (isset($degrees)) {
          $source = imagecreatefromjpeg($img);
          $rotate = imagerotate($source, $degrees, 0);
  
          imagejpeg($rotate, $img);
          imagedestroy($source);
          imagedestroy($rotate);
      }   
  }
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
