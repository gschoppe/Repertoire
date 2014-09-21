<?php
require_once("resources/core.php");
$result      = null;
$setlist     = null;
$setControls = null;
if(isset($_GET['songID']) && $_GET['songID']) {
    $songID = $_GET['songID'];
    $query = "SELECT * FROM music WHERE songID='".$db->escape($songID)."' LIMIT 1";
    $song = $db->get_row($query);
    if (!$song) {
        $message = array('type'=>'error', 'text'=>"Song ID $songID not found.", 'destructs'=>false);
    } else {
        $views = $song->views+1;
        $query = "UPDATE music SET views=".$db->escape($views)." WHERE songID='".$db->escape($songID)."'";
        $db->query($query);
        if(isset($_GET['setID']) && $_GET['setID']) {
            // deal with setlist next/prev here
            $setID = $_GET['setID'];
            $setControls = get_prev_next_songs($setID, $songID);
        }
    }
} else {
    $message = array('type'=>'error', 'text'=>"No Song Selected");
}
include('resources/templates/head.php');
include('resources/templates/song.php');
include('resources/templates/foot.php');
?>