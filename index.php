<?php
require "simple_html_dom.php";
require "image.php";
require "video.php";
require "config.php";
date_default_timezone_set("Asia/Ho_Chi_Minh");
/**
 * RUN
 */
$i = vnexpress_url();
#dantri_url($i);
/**
 * VNEXPRESS
 */

function vnexpress_url() {
    $url = "https://vnexpress.net/rss/tin-xem-nhieu.rss";
    $feed = new DOMDocument;
    $feed->load($url);
    $feed_array = array();
    $i = 0;
    foreach($feed->getElementsByTagName('item') as $v => $story){
        if($i>9) {
            exit;
        }
        $link = $story->getElementsByTagName('link')->item(0)->nodeValue;

        $check = namespace\check_url_available($link);
        if($check == TRUE) {
            continue;
        }
        if (strpos($link,"video") !== FALSE) {
            continue;
        }
        if(strpos($link,"gif") !== FALSE) {
            continue;
        }
        $html = file_get_html(str_replace("beta.","",$link));
        $description_1 = $html->find("p[class=description]",0)->plaintext;
        $description_2 = $html->find(" p[class=Normal]",0)->plaintext;
        #if($v ==0) {
            $description_full = $description_1;
            $caption = $description_1." ".$description_2;
        #} else {
        #    $description_full = $description_1." ".$description_2;
        #}
        $title = $story->getElementsByTagName('title')->item(0)->nodeValue;
        $description = $story->getElementsByTagName('description')->item(0)->nodeValue;
        $desc = explode(' ></a></br>',$description)[1];
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $description, $match);
        $image_url = $match[0][1];
        if(!$title || !$desc || !$image_url ||!$description_full) {
            continue;
        }
        namespace\insert($link);
        #namespace\createInstagram($title,$description_full,$image_url,$i);
        $article = [
            "image" => $image_url,
            "description" => $description_full
        ];
        namespace\create_thumb($article,$i);
        namespace\createInstagram($title,$description_full,$image_url,$i);
        namespace\createTiktok($title,$description_full,$image_url,$i);
        #namespace\create_thumb($article,$i);
        $i ++;
    }
    #namespace\createTiktokVideo($titokPath);
    return $i;
}

function dantri_url($i) {
    $html_dantri = file_get_html("https://dantri.com.vn/");
    $dantri_urls = array();
    foreach($html_dantri->find("h2[class=news-item__title] a") as $d => $item) {
        if($i>9) {
            exit;
        }
        $check = namespace\check_url_available("https://dantri.com.vn".$item->href);
        if($check == TRUE) {
            continue;
        }
        if (
            strpos($item->href,"an-sinh") !== FALSE ||
            strpos($item->href,"blog") !== FALSE ||
            strpos($item->href,"dien-dan") !== FALSE ||
            strpos($item->href,"chuyen-la") !== FALSE ||
            strpos($item->href,"du-hoc") !== FALSE ||
            strpos($item->href,"tinh-yeu-gioi-tinh") !== FALSE ||
            strpos($item->href,"ban-doc") !== FALSE ) {
            continue;
        }
        $html = file_get_html(str_replace("beta.","","https://dantri.com.vn".$item->href));
        $title = $html->find("meta[name=twitter:title]", 0)->content;
        $image_url = $html->find("meta[name=twitter:image]",0)->content;
        $description_1 = str_replace("(Dân trí) - ","",$html->find("meta[name=twitter:description]",0)->content);
        $description_2 = $html->find("div[class=dt-news__content] p" ,0)->plaintext;
        $title = str_replace('\\','',$title);
        $description_full = $description_1;
        $caption = $description_1." ".$description_2;
        /*
        if($d == 0) {
            $description_full = $description_1;
        } else {
            $description_full = $description_1." ".$description_2;
        }
        */
        namespace\insert("https://dantri.com.vn".$item->href);
        #namespace\create($title,$image_url,$description_full,$caption,$i);
        $article = [
            "image" => $image_url,
            "description" => $description_full
        ];
        namespace\create_thumb($article,$i);
        $i++;
    }
    foreach($html_dantri->find("h2[class=news-item__related-item] a") as $item_relate) {
        if($i>9) {
            exit;
        }
        $check = namespace\check_url_available("https://dantri.com.vn".$item_relate->href);
        if($check == TRUE) {
            continue;
        }

        if (
            strpos($item_relate->href,"an-sinh") !== FALSE ||
            strpos($item_relate->href,"blog") !== FALSE ||
            strpos($item_relate->href,"dien-dan") !== FALSE ||
            strpos($item_relate->href,"chuyen-la") !== FALSE ||
            strpos($item_relate->href,"du-hoc") !== FALSE ||
            strpos($item_relate->href,"tinh-yeu-gioi-tinh") !== FALSE ||
            strpos($item_relate->href,"ban-doc") !== FALSE ) {
            continue;
        }
        $html = file_get_html(str_replace("beta.","","https://dantri.com.vn".$item_relate->href));
        $title = $html->find("meta[name=twitter:title]", 0)->content;
        $image_url = $html->find("meta[name=twitter:image]",0)->content;
        $description_1 = str_replace("(Dân trí) - ","",$html->find("meta[name=twitter:description]",0)->content);
        $description_2 = $html->find("div[class=dt-news__content] p" ,0)->plaintext;
        $title = str_replace('\\','',$title);
        $description_full = $description_1;
        $caption = $description_1." ".$description_2;
        namespace\insert("https://dantri.com.vn".$item_relate->href);
        #namespace\create($title,$image_url,$description_full,$caption,$i);
        $article = [
            "image" => $image_url,
            "description" => $description_full
        ];
        namespace\create_thumb($article,$i);
        $i++;
        sleep(0.5);
    }
    /*
    foreach($html_dantri->find("h3[class=news-item__title] a") as $item_news) {
        if (
            strpos($item_news->href,"an-sinh") !== FALSE ||
            strpos($item_news->href,"blog") !== FALSE ||
            strpos($item_news->href,"dien-dan") !== FALSE ||
            strpos($item_news->href,"chuyen-la") !== FALSE ||
            strpos($item_news->href,"du-hoc") !== FALSE ||
            strpos($item_news->href,"tinh-yeu-gioi-tinh") !== FALSE ||
            strpos($item_news->href,"ban-doc") !== FALSE ) {
            continue;
        }
        $dantri_urls [] = "https://dantri.com.vn".$item_news->href;
    }
*/
    #print_r($dantri_urls);
    return $i;
}

















/*
$full_link = array();
$i = 0;
$html_vn =  file_get_html("https://vnexpress.net/tin-xem-nhieu");
$list_vn = $html_vn->find("h3[class=title-news] a");

foreach( $list_vn as $k => $item_vn){
    $title_vn = $item_vn->title;
    $link_vn = $item_vn->href;
    if($i>=10) {
        exit;
    }
    if (strpos($link_vn,"video") !== FALSE) {
        continue;
    }
    if(strpos($link_vn,"gif") !== FALSE) {
        continue;
    }

    if(check_url_available($link_vn)) {
        continue;
    }

    $html_vn_item = file_get_html(str_replace("beta.","",$link_vn));

    #$title = $html->find("meta[name=its_title]", 0)->content;
    $img_vn_item = $html_vn_item->find("meta[name=twitter:image]",0)->content;
    #$time = $html->find("span[class=time left]",0)->innertext;
    $item_vn_des = $html_vn_item->find("p[class=description]",0)->plaintext;
    $item_vn_des1 = $html_vn_item->find(" p[class=Normal]",0)->plaintext;
    $item_vn_description = $item_vn_des." ".$item_vn_des1;

    if(strlen($item_vn_description) > 215) {
        continue;
    }

    #$title = str_replace('\\','',$title);
    #insert($link_vn);
    create($title_vn,$img_vn_item,$item_vn_description,$i);
    $i ++;
    #echo $item_vn_description."<br>";
}
*/

/**
 * DANTRI
 */
/*
$html_dantri = file_get_html("https://dantri.com.vn/");
$list_dantri = $html_dantri->find('div[class=box1 clearfix] ',0)->find('div[data-boxtype=homenewsposition] a',0);
$bigtitle_dantri= $list_dantri->title;
$biglink_dantri = "https://dantri.com.vn".$list_dantri->href;
print_r($biglink_dantri);
$list = $html_dantri->find('div[class=xnano-content] a' );
foreach ( $list as $item  ) {
    $link_dantri = "https://dantri.com.vn".$item->href;
    $text_dantri = $item->plaintext;
    echo $link_dantri."<br>";
}
*/
?>