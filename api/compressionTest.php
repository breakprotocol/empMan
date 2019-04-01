<?php

    function compress_image($source_url, $destination_url, $quality) {
        echo "First echo".$source_url;
       $info = getimagesize($source_url);
       echo "Second echo".$info;
       

        if ($info['mime'] == 'image/jpeg')
              $image = imagecreatefromjpeg($source_url);

        elseif ($info['mime'] == 'image/gif')
              $image = imagecreatefromgif($source_url);

      elseif ($info['mime'] == 'image/png')
              $image = imagecreatefrompng($source_url);

        imagejpeg($image, $destination_url, $quality);
    return $destination_url;
    }

    compress_image("DSC_5241.JPG",'DSC_5241.JPG','25');

?>