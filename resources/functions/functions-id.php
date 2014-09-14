<?php if( !defined( 'CORELOADED' ) ) die();

function make_song_id($title, $tempPath) {
    global $db;
    $id = md5($title);
    $query  = "SELECT * FROM music WHERE songID = '".$db->escape($id)."'";
    $result = $db->get_results($query);
    $i      = 0;
    while ($result) {
        $id = md5($title.$i);
        $query = "SELECT * FROM music WHERE songID='".$db->escape($id)."'";
        $result = $db->get_results($query);
        $i++;
    }
    
    return($id);
}
?>