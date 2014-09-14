<?php if( !defined( 'CORELOADED' ) ) die();
function resize_image($source, $target, $extension, $new_w, $new_h){
    $src_img = null;
    if (preg_match('/jpg|jpeg/i',$extension)) {
        $src_img=@imagecreatefromjpeg($source);
    } elseif(preg_match('/png/i',$extension)) {
        $src_img=@imagecreatefrompng($source);
    } elseif(preg_match('/gif/i',$extension)) {
        $src_img=@imagecreatefromgif($source);
    } 
    if(!$src_img) return(array('success'=>false, 'message'=>"Invalid Image"));
    if(IMAGE_PREPROCESSING) {
        if(IMAGE_TRIM_BORDER) {
            $src_img = image_trim_border($src_img);
            if(!$src_img) return(array('success'=>false, 'message'=>"Image is blank"));
        }
        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);
        if($old_x<$new_w && $old_y<$new_h) {
            $dst_img = $src_img;
        } else {
            if($old_x/$new_w > $old_y/$new_h) {
                $thumb_w=$new_w;
                $thumb_h=$old_y*($new_w/$old_x);
            } elseif($old_x/$new_w < $old_y/$new_h) {
                $thumb_w=$old_x*($new_h/$old_y);
                $thumb_h=$new_h;
            } else {
                $thumb_w=$new_w;
                $thumb_h=$new_h;
            }
            $dst_img=ImageCreateTrueColor($thumb_w, $thumb_h);
            imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
            imagedestroy($src_img); 
        }
    } else {
        $dst_img = $src_img;
    }
    if (preg_match("/png/", $extension)) {
        imagepng($dst_img, $target); 
    } else {
        imagejpeg($dst_img, $target);
    }
    imagedestroy($dst_img);
    return(array('success'=>true));
}

function image_trim_border($img) {
    $start_time = time();
    // find the trimmed image border
    $box = image_get_border_box($img, IMAGE_BORDER_COLOR, IMAGE_BORDER_TOLERANCE);
    if($box['#']==2) return null;
    // copy cropped portion
    $img2 = imagecreate($box['w'], $box['h']);
    imagecopy($img2, $img, 0, 0, $box['l'], $box['t'], $box['w'], $box['h']);
    $end_time = time();
    //echo "<br/>borderbox took: ".($end_time - $start_time)."seconds";
    return($img2);
}



function image_get_border_box($img, $borderColor=null, $tolerance = 0) {
    if (!ctype_xdigit($borderColor)) $borderColor = imagecolorat($img, 0,0);
    $bColor = hex_color_to_array($borderColor);
    $bTop = $bLeft = 0;
    $bRight  = $w1 = $w2 = imagesx($img);
    $bBottom = $h1 = $h2 = imagesy($img);
    $bRight--;
    $bBottom--;
    //top
    for(; $bTop < $h1; $bTop++) {
        for($x = 0; $x < $w1; $x++) {
            $pixelVal = hex_color_to_array(imagecolorat($img, $x, $bTop));
            if(($tolerance == 0 && $pixelVal !== $bColor) || ($tolerance != 0 && euclidean_distance($pixelVal, $bColor) > $tolerance)) {
                break 2;
            }
        }
    }
    // stop if all pixels are trimmed
    if ($bTop == $bBottom) {
        $code  = 2;
    } else {
        // bottom
        for(; $bBottom >= 0; $bBottom--) {
            for($x = 0; $x < $w1; $x++) {
                $pixelVal = hex_color_to_array(imagecolorat($img, $x, $bBottom));
                if(($tolerance == 0 && $pixelVal !== $bColor) || ($tolerance != 0 && euclidean_distance($pixelVal, $bColor) > $tolerance)) {
                    break 2;
                }
            }
        }

        // left
        for(; $bLeft < $w1; $bLeft++) {
            for($y = $bTop; $y <= $bBottom; $y++) {
                $pixelVal = hex_color_to_array(imagecolorat($img, $bLeft, $y));
                if(($tolerance == 0 && $pixelVal !== $bColor) || ($tolerance != 0 && euclidean_distance($pixelVal, $bColor) > $tolerance)) {
                    break 2;
                }
            }
        }

        // right
        for(; $bRight >= 0; $bRight--) {
            for($y = $bTop; $y <= $bBottom; $y++) {
                $pixelVal = hex_color_to_array(imagecolorat($img, $bRight, $y));
                if(($tolerance == 0 && $pixelVal !== $bColor) || ($tolerance != 0 && euclidean_distance($pixelVal, $bColor) > $tolerance)) {
                    break 2;
                }
            }

        }

        $w2 = $bRight - $bLeft;
        $h2 = $bBottom - $bTop;
        $code = ($w2 < $w1 || $h2 < $h1) ? 1 : 0;
    }
    // result codes:
    // 0 = Trim Zero Pixels
    // 1 = Trim Some Pixels
    // 2 = Trim All Pixels
    return array(
        '#'     => $code,   // result code
        'l'     => $bLeft,  // left
        't'     => $bTop,  // top
        'r'     => $bRight,   // right
        'b'     => $bBottom,  // bottom
        'w'     => $w2,     // new width
        'h'     => $h2,     // new height
        'w1'    => $w1,     // original width
        'h1'    => $h1,     // original height
    );
}

function hex_color_to_array($hex) {
    $value = array();
    $value['r'] = ($hex >> 16) & 0xFF;
    $value['g'] = ($hex >> 8) & 0xFF;
    $value['b'] = $hex & 0xFF;
    return($value);
}

function euclidean_distance($color1, $color2) {
    return sqrt(pow(($color2['r']-$color1['r']),2) + pow(($color2['g']-$color1['g']),2) + pow(($color2['b']-$color1['b']),2));
}
?>