<?php
require_once("resources/core.php");
$activeMenuItem = "Home";

if(isset($_GET['delete']) && $_GET['delete']) {
    $result = delete_song($_GET['delete']);
    if(!$result['success']) {
        $message = array('type'=>'error', 'text'=>$result['message'], 'destructs'=>true);
    } else {
        $message = array('type'=>'success', 'text'=>$result['message'], 'destructs'=>true);
    }
}
$query    = "SELECT count(*) FROM music";
$areSongs = $db->get_var($query);
include('resources/templates/head.php');
include('resources/templates/songs.php');
include('resources/templates/foot.php');
?>