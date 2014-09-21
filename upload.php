<?php
require_once("resources/core.php");
$activeMenuItem = "Upload";

if(isset($_POST) && $_POST) {
    if(!(isset($_POST['title'], $_POST['genre'], $_POST['rating']) && $_POST['title'] && $_POST['genre'] && $_POST['rating'])) {
        $message = array('type'=>'error', 'text'=>"Please complete all fields.", 'destructs'=>false);
    } else {
        // <--------------------------------------------------------------------------- begin image code
        $simplename = basename($_FILES['file']['name']);
        $extension = strtolower(pathinfo($simplename, PATHINFO_EXTENSION));
        $uploadFile = __ROOT__.'/'.__TEMP__.md5($simplename).'.'.$extension;
        if(!is_uploaded_file($_FILES['file']['tmp_name'])) {
            $message = array('type'=>'error', 'text'=>"the file was not uploaded correctly, try again", 'destructs'=>false);
        } elseif(!@move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $message = array('type'=>'error', 'text'=>"error moving file from ".$_FILES["file"]["tmp_name"]." to $uploadFile", 'destructs'=>false);
        } else {
            $result = upload_song($uploadFile, $_POST['title'], $_POST['artist'], $_POST['genre'], $_POST['rating']);
            if($result['success']) {
                $message = array('type'=>'success', 'text'=>"Image added to database!", 'destructs'=>true);
            } else {
                $message = array('type'=>'error', 'text'=>$result['message'], 'destructs'=>false);
            }
        }
    }
}
include('resources/templates/head.php');
include('resources/templates/upload.php');
include('resources/templates/foot.php');
?>