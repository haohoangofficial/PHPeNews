<?php
define("ROOTPATH", dirname(__FILE__));
date_default_timezone_set("Asia/Ho_Chi_Minh");
/*
$title = 'THỦ TƯỚNG: MẠNH TAY VỚI CÁC TỈNH XIN TIỀN RỒI KHÔNG CHỊU... TIÊU!';
$description = 'Nhiều anh làm Bí thư, Chủ tịch tỉnh khi xin vốn về cho địa phương mình thì nói cần quá, nhưng khi xin về lại không triển khai, giao phó hết cho cấp dưới" - Thủ tướng nói.';
$image = "https://icdn.dantri.com.vn/2020/07/08/dai-su-my-14-1594197583576.jpg";
create($title,$image,$description,0);
*/
function create($title,$image,$description,$caption,$i) {
    $background = namespace\create_image(ROOTPATH."/source/facebook.png");
    $D = "D:/News/";
    if (!is_dir($D)) {
        mkdir($D);
    }
    $date = date("d-m-Y-H");
    if (!is_dir($D.$date)) {
        mkdir($D.$date);
    }
    $color= imagecolorallocate($background, 0, 0, 0);
    $font = ROOTPATH.'/fonts/Roboto-Regular.ttf';

    file_put_contents(ROOTPATH."/source/photo.png",file_get_contents($image));
    $photo = namespace\create_image(ROOTPATH."/source/photo.png");
    $ratio = imagesx($photo)/imagesy($photo);
    $top = namespace\text_Height($background, 40, 0, $color, $font, str_replace("&quot;", " ",$title), 950, 55);
    $max_height = 1080 - $top;
    $max_width = $ratio*$max_height;
    $source_file =ROOTPATH."/source/photo.png";
    $dst_dir = ROOTPATH."/source/picture.png";
    namespace\resize_crop_image($max_width, $max_height, $source_file, $dst_dir);
    $picture = namespace\create_image(ROOTPATH."/source/picture.png");
    imagecopy($background, $picture, (1080-imagesx($picture))/2, 285, 0, 0, imagesx($picture), imagesy($picture));
    unlink(ROOTPATH."/source/photo.png");
    unlink(ROOTPATH."/source/picture.png");
    namespace\wrapText($background, 40, 0, 135 + ((285 - $top) / 2), $color, $font, str_replace("&quot;", " ",$title), 950, 55);
    namespace\wrapText($background, 25, 0, 1250 , $color, $font, str_replace("&quot;", " ",$description), 950, 35);
    header('Content-type: image/png');
    file_put_contents($D.$date."/caption.txt","- ".str_replace("&quot;", " ",$description).PHP_EOL,FILE_APPEND);
    imagepng($background,$D.$date."/".$i.".png");
}

function create_thumb($article = array(),$i) {
    $date = date("d-m-Y-H");
    $D = "D:/News/";
    if (!is_dir($D)) {
        mkdir($D);
    }
    $date = date("d-m-Y-H");
    if (!is_dir($D.$date)) {
        mkdir($D.$date);
    }
    $background = namespace\create_image(ROOTPATH."/temp/background.png");
    $color= imagecolorallocate($background, 255, 255, 255);
    $font = ROOTPATH.'/temp/Roboto-Regular.ttf';

    file_put_contents(ROOTPATH."/temp/photo.png",file_get_contents($article["image"]));
    $photo = namespace\create_image(ROOTPATH."/temp/photo.png");
    $ratio = imagesx($photo)/imagesy($photo);
    $max_height = 770;
    $max_width = $ratio*$max_height;
    $source_file =ROOTPATH."/temp/photo.png";
    $dst_dir = ROOTPATH."/temp/picture.png";

    namespace\resize_crop_image($max_width, $max_height, $source_file, $dst_dir);
    $picture = namespace\create_image(ROOTPATH."/temp/picture.png");

    imagecopy($background, $picture, (1080-imagesx($picture))/2, 0, 0, 0, imagesx($picture), imagesy($picture));
    unlink(ROOTPATH."/temp/photo.png");
    unlink(ROOTPATH."/temp/picture.png");

    namespace\wrapText($background, 30, 0, 900 , $color, $font, mb_strtoupper($article["description"],'UTF-8'), 1000, 45);

    header('Content-type: image/png');
    imagepng($background,$D.$date."/".$i.".png");
    file_put_contents($D.$date."/caption.txt","- ".str_replace("&quot;", " ",$article["description"]).PHP_EOL,FILE_APPEND);

    #return ROOTPATH."/temp/uploads/".$category.".jpg";
}

function crop_image($img,$w,$h) {
    $image = imagecreatefrompng($img);
    imagecrop( $image, array( 'x' => $x, 'y' => $y, 'width' => $width, 'height' => $height ) );
    imagepng($image,$img);
    imagedestroy($image);
}

//resize and crop image by center
function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 300){
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];
 
    switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;
 
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;
 
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;
 
        default:
            return false;
            break;
    }
     
    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);
     
    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if($width_new > $width){
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    }else{
        //cut point by width
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
     
    $image($dst_img, $dst_dir, $quality);
 
    if($dst_img)imagedestroy($dst_img);
    if($src_img)imagedestroy($src_img);
}

function create_image($source_file) {
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];
 
    switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;
 
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;
 
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;
 
        default:
            return false;
            break;
    }
     
    $src_img = $image_create($source_file);
    return $src_img;
}

//wrapText()
function wrapText(&$image, $size, $angle, $top, $color, $font, $text, $max_width, $linespacing){
    $words = explode(" ",$text);
    $line = "";
    foreach($words as $word) {
        
        $dimensions_line = imagettfbbox($size, $angle, $font, $line);
        $line_width_line = $dimensions_line[2] - $dimensions_line[0];

        $lineword = $line.$word." ";
        $dimensions = imagettfbbox($size, $angle, $font, $lineword);
        $line_width = $dimensions[2] - $dimensions[0];
        $line_height = $dimensions[1] - $dimensions[7];
        if($line_width > $max_width) {
            imagettftext(
                $image, 
                $size, 
                $angle, 
                (imagesx($image) - $line_width_line) / 2,
                $top, 
                $color, 
                $font, 
                $line);
            $line = $word." ";
            $top += $linespacing;
        } else {
            $line = $lineword;
        } 
    }
    $dimensions_line = imagettfbbox($size, $angle, $font, $line);
    $line_width_line = $dimensions_line[2] - $dimensions_line[0];
    imagettftext(
        $image, 
        $size, 
        $angle, 
        (imagesx($image) - $line_width_line) / 2, 
        $top, 
        $color, 
        $font, 
        $line
        );
    #return $top;
}


function text_Height(&$image, $size, $angle, $color, $font, $text, $max_width, $linespacing){
    $words = explode(" ",$text);
    $line = "";
    $line_width;
    $top;
    foreach($words as $word) {
        $lineword = $line.$word." ";
        $dimensions = imagettfbbox($size, $angle, $font, $lineword);
        $line_width = $dimensions[2] - $dimensions[0];
        $line_height = $dimensions[1] - $dimensions[7];
        if($line_width > $max_width) {
            $line = $word." ";
            $top += $line_height + $linespacing;
        } else {
            $line = $lineword;
        } 
    }
    $dimensions_line = imagettfbbox($size, $angle, $font, $line);
    $line_height_line = $dimensions_line[1] - $dimensions_line[7];
    return $top + $line_height_line;
}

function createInstagram($title,$description,$image,$i) {
    $background = namespace\create_image(ROOTPATH."/source/facebook.png");
    $D = "D:/News/";
    $date = date("d-m-Y-H");
    if (!is_dir($D.$date)) {
        mkdir($D.$date);
    }
    if (!is_dir($D.$date."/IG")) {
        mkdir($D.$date."/IG");
    }
    $color= imagecolorallocate($background, 0, 0, 0);
    $font = ROOTPATH.'/fonts/Roboto-Regular.ttf';

    file_put_contents(ROOTPATH."/source/photo.png",file_get_contents($image));
    $photo = namespace\create_image(ROOTPATH."/source/photo.png");
    $ratio = imagesx($photo)/imagesy($photo);
    $top = namespace\text_Height($background, 40, 0, $color, $font, str_replace("&quot;", " ",$title), 950, 55);
    $max_height = 1080 - $top;
    $max_width = $ratio*$max_height;
    $source_file =ROOTPATH."/source/photo.png";
    $dst_dir = ROOTPATH."/source/picture.png";
    namespace\resize_crop_image($max_width, $max_height, $source_file, $dst_dir);
    $picture = namespace\create_image(ROOTPATH."/source/picture.png");
    imagecopy($background, $picture, (1080-imagesx($picture))/2, 285, 0, 0, imagesx($picture), imagesy($picture));
    unlink(ROOTPATH."/source/photo.png");
    unlink(ROOTPATH."/source/picture.png");
    namespace\wrapText($background, 40, 0, 135 + ((285 - $top) / 2), $color, $font, str_replace("&quot;", " ",$title), 950, 55);
    namespace\wrapText($background, 25, 0, 1250 , $color, $font, str_replace("&quot;", " ",$description), 950, 35);
    header('Content-type: image/png');
    file_put_contents($D.$date."/IG/caption.txt","- ".str_replace("&quot;", " ",$description).PHP_EOL,FILE_APPEND);
    imagepng($background,$D.$date."/IG/".$i.".png");
}

function createTiktok($title,$description,$image,$i) {
    $background = namespace\create_image(ROOTPATH."/source/Tiktok.png");
    $D = "D:/News/";
    $date = date("d-m-Y-H");
    if (!is_dir($D.$date)) {
        mkdir($D.$date);
    }
    if (!is_dir($D.$date."/TT")) {
        mkdir($D.$date."/TT");
    }
    $color= imagecolorallocate($background, 0, 0, 0);
    $font = ROOTPATH.'/fonts/Roboto-Regular.ttf';
    #$font = ROOTPATH.'/fonts/VNF-Futura Regular.ttf';

    file_put_contents(ROOTPATH."/source/photo.png",file_get_contents($image));
    $photo = namespace\create_image(ROOTPATH."/source/photo.png");
    $ratio = imagesx($photo)/imagesy($photo);
    $max_height = 720;
    $max_width = $ratio*$max_height;
    $source_file =ROOTPATH."/source/photo.png";
    $dst_dir = ROOTPATH."/source/picture.png";
    namespace\resize_crop_image($max_width, $max_height, $source_file, $dst_dir);
    $picture = namespace\create_image(ROOTPATH."/source/picture.png");
    $top = namespace\text_Height($background, 40, 0, $color, $font, str_replace("&quot;", " ",$title), 650, 55);
    imagecopy($background, $picture, (imagesx($background)-imagesx($picture))/2, (imagesy($background)-imagesy($picture))/2, 0, 0, imagesx($picture), imagesy($picture));
    unlink(ROOTPATH."/source/photo.png");
    unlink(ROOTPATH."/source/picture.png");
    namespace\wrapText(
        $background, 
        40, 
        0, 
        150,//(((imagesy($background)-imagesy($picture))/2 - 180) - $top)/2 + 180, 
        $color, 
        $font, 
        str_replace("&quot;", " ",$title), 
        650, 
        55);
        $top = namespace\text_Height($background, 25, 0, $color, $font, str_replace("&quot;", " ",$description), 650, 35);
    namespace\wrapText(
        $background, 
        25, 
        0, 
        50 + imagesy($background) - ((imagesy($background)-imagesy($picture))/2),
        $color, 
        $font, 
        str_replace("&quot;", " ",
        $description), 
        650, 
        35);
    header('Content-type: image/png');
    #file_put_contents($D.$date."/caption.txt","- ".str_replace("&quot;", " ",$description).PHP_EOL,FILE_APPEND);
    imagepng($background,$D.$date."/TT/".$i.".png");
    return $D.$date."/TT";
}
