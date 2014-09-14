<?php
require_once("resources/core.php");
$setID = "";
$set   = null;
$songs = array();
if(isset($_GET['setID']) && $_GET['setID']) {
    $setID=$_GET['setID'];
    $query = "SELECT * FROM sets WHERE setID='".$db->escape($setID)."'";
    $results = $db->get_row($query);
    if($results) {
        if(isset($_GET['addSong']) && $_GET['addSong']) {
            add_song_to_set($_GET['addSong'], $setID);
        }
        $set   = $results;
        $songs = get_set_contents($setID);
    } else {
        //unknown set
        $message = array('type'=>'error', 'text'=>"Unknown SetID.  Please go back and select again.", 'destructs'=>false);
    }
} else {
    // no set given
    $message = array('type'=>'error', 'text'=>"This page can only be used when a setID is specified", 'destructs'=>false);
    header( 'Location: setlists.php' );
}
include('resources/templates/head.php');
include('resources/templates/set.php');
include('resources/templates/foot.php');
?>