<?php
define("ROOTPATH", dirname(__FILE__));
date_default_timezone_set("Asia/Ho_Chi_Minh");
#ImagetoVideo("D:/News/07-08-2020-21/TT/0.png",$frame = 30, $duration = 3,1);
#transition("source/1video.mp4",$transition = "random");
createTiktokVideo("D:/News/10-08-2020-21/TT");


function createTiktokVideo($disk) {

#$disk = "D:/News/07-08-2020-21/TT";
$scan = scandir($disk);
$ffmpeg = "C:/ffmpeg/bin/ffmpeg.exe";

$command = "";
foreach($scan as $i => $file) {
    if($i == 0 || $i == 1) {
        continue;
    }
    $ext = strtolower(pathinfo($disk."/".$file, PATHINFO_EXTENSION));
    if (!in_array($ext, ["png"])) {
        continue;
    }
    $path = "'".$disk."/".$file."'";
    for($s = 0; $s < 30; $s ++) {
    $command = "file ".$path.PHP_EOL. "duration 0.1".PHP_EOL;
    file_put_contents("command.txt",$command,FILE_APPEND);
    }
}
    $command_file = "command.txt";
    $converted_name = "video.mp4";
    $ffmpeg_command = $ffmpeg.' -f concat -safe 0 -i '.$command_file.' -c:v copy '.$converted_name;
    echo $ffmpeg_command;
    #shell_exec("D:");
    #shell_exec("cd D:\News\07-08-2020-21\TT");
    shell_exec($ffmpeg_command);
    unlink("command.txt");
    if(file_exists("video.mp4")) {
        rename("video.mp4",$disk."/video.mp4");
    }
}

function ImagetoVideo($filePath,$frame = 30, $duration = 3,$index) {
    $ffmpeg = "C:/ffmpeg/bin/ffmpeg.exe";
    $command = "";
        for($s = 0; $s < ($frame * $duration); $s ++) {
        $command = "file '".$filePath."'".PHP_EOL. "duration ".(float)(1/$frame).PHP_EOL;
        file_put_contents("command.txt",$command,FILE_APPEND);
        }
        $command_file = "command.txt";
        $converted_name = "source/".$index."video.mp4";
        $ffmpeg_command = $ffmpeg.' -f concat -safe 0 -i '.$command_file.' -b 1000k -vcodec mpeg4 -acodec mpeg4 -crf 19 -filter:v '.$converted_name;
        echo $ffmpeg_command;
        #shell_exec("D:");
        #shell_exec("cd D:\News\07-08-2020-21\TT");
        shell_exec($ffmpeg_command);
        unlink("command.txt");
    return ROOTPATH."/source/".$converted_name;
}

function transition($filePath,$transition = "random") {
    $ffmpeg = "C:/ffmpeg/bin/ffmpeg.exe";
    $transitionIns = array(
        "fade=in"
    );
    $transitionOuts = array(
        "fade=out"
    );
    if($transition === "random") {
        $transitionIn = $transitionIns[0];
        $transitionOut = $transitionOuts[0];    
    } else {
        $transitionIn = $transitionIns[$transition];
        $transitionOut = $transitionOuts[$transition];
    }
    $converted_name = "source/transition/1video.mp4";
    $ffmpegtransitionIn = 
    $ffmpeg." -i ".$filePath." -vcodec copy -vf ".$transitionOut.":0:10 ".$converted_name;
    #$ffmpeg.' -i '.$filePath.' -filter_complex "overlay" -vcodec libx265 -crf 10 '.$converted_name;
    echo $ffmpegtransitionIn;
    #shell_exec("cd source");
    shell_exec($ffmpegtransitionIn);
}