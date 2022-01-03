<?php
define("DB_HOST", "localhost"); 
define("DB_NAME", "t"); 
define("DB_USER", "root"); 
define("DB_PASS", "mysql"); 
define("DB_ENCODING", "utf8");
function connect() {
    return mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}
function insert($url) {
    $connect = namespace\connect();
    $sql = "INSERT INTO news (url) 
            VALUES ('$url')";
    try {
        mysqli_query($connect, $sql);
    }catch(Exception $e) {
        exit;
    }
    mysqli_close($connect);
}
function fetch_urls() {
    $connect = namespace\connect();
    $sql = "SELECT id, url
            FROM news";
    $result = mysqli_query($connect, $sql);
    $urls = [];
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $urls[] = $row["url"];
        }
    } else {
        return false;
    }
    
    return $urls;
    mysqli_close($connect);
}

function check_url_available($url) {
    $urls = namespace\fetch_urls();
    if(!$urls) {
        return FALSE;
    }
    if(in_array($url,$urls) === TRUE) {
        return TRUE;
    }else {
        return FALSE;
    }
}
