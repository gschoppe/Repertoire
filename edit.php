<?php
require_once("resources/core.php");

if(isset($_GET['songID']) && $_GET['songID']) {
    $songID = $_GET['songID'];
    if(isset($_POST) && !empty($_POST)) {
        $response = edit_song_info($songID);
        if($response['success']) {
            $message = array('type'=>'success', 'text'=>$response['message'], 'destructs'=>true);
        } else {
            $message = array('type'=>'error', 'text'=>$response['message'], 'destructs'=>true);
        }
    }
    $query = "SELECT * FROM music WHERE songID='".$db->escape($songID)."' LIMIT 1";
    $row   = $db->get_row($query);
    if(!$row) {
        $message = array('type'=>'error', 'text'=>"songID was not found", 'destructs'=>false);
    }
} else {
    $message = array('type'=>'error', 'text'=>"no songID specified", 'destructs'=>false);
}

include('resources/templates/head.php');
include('resources/templates/edit.php');
include('resources/templates/foot.php');
?>
