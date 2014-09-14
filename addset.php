<?php
require_once("resources/core.php");
if(isset($_POST) && $_POST) {
    if(!(isset($_POST['name']) && $_POST['name'])) {
        $message = array('type'=>'error', 'text'=>"Setlist name is required.", 'destructs'=>true);
    } else {
        $name = $_POST['name'];
        $setID = md5($name.time());
        $desc ="";
        if(isset($_POST['desc']) && $_POST['desc'])
            $desc = $_POST['desc'];
        $songID = "";
        if(isset($_POST['songID']) && $_POST['songID'])
            $songID = $_POST['songID'];
        create_set($name, $desc, $songID);
        $message = array('type'=>'success', 'text'=>"Setlist created.", 'destructs'=>true);
    }
}
include('resources/templates/head.php');
include('resources/templates/addset.php');
include('resources/templates/foot.php');
?>