<?php if( !defined( 'CORELOADED' ) ) die();

function create_set($name, $description="", $songID = "") {
    global $db;
    $setID  = md5($name.time());
    $query  = "INSERT INTO sets (setID, name, description, size, updated)";
    $query .= "VALUES ('".$db->escape($setID)."', '".$db->escape($name)."', '".$db->escape($description)."', 0, ".time().")";
    $db->query($query);
    if($songID) add_song_to_set($songID, $setID);
}

function edit_set($setID, $newName, $newDesc = null) {
    global $db;
    $query  = "UPDATE sets SET name='".$db->escape($newName)."', updated=".time();
    if(!is_null($newDesc))
        $query .= ", description='".$db->escape($description)."'";
    $query .= " WHERE setID='".$db->escape($setID)."'";
    $db->query($query);
}

function delete_set($setID) {
    global $db;
    $query="DELETE FROM sets WHERE setID='".$db->escape($setID)."'";
    $db->query($query);
    $query="DELETE FROM setData WHERE setID='".$db->escape($setID)."'";
    $db->query($query);
}

function get_set_contents($setID) {
    global $db;
    $songs = array();
    $query = "SELECT * FROM setData INNER JOIN music ON setData.songID = music.songID WHERE setID='".$db->escape($setID)."' ORDER BY position ASC";
    $results = $db->get_results($query);
    if($results) {
        foreach($results as $row) {
            $songs[$row->songID] = (array)$row;
        }
    }
    return $songs;
}
function get_prev_next_songs($setID, $songID) {
    $songs    = get_set_contents($setID);
    $first    = null;
    $last     = null;
    $prev     = null;
    $passed   = false;
    $next     = null;
    $position = 1;
    $count    = count($songs);
    foreach($songs as $key=>$val) {
        if(!$first) $first = $key;
        if($key == $songID || $passed) {
            if($passed && !$next) $next = $key;
            $passed = true;
        } else {
            $prev = $key;
            $position++;
        }
        $last = $key;
    }
    if(!$prev) $prev = $last;
    if(!$next) $next = $first;
    return array('prev'=>$prev, 'next'=>$next, 'position'=>$position, 'count'=>$count);
}

function add_song_to_set($songID, $setID, $position = false) {
    global $db;
    $query  = "SELECT * FROM music WHERE songID='".$db->escape($songID)."'";
    $result = $db->get_row($query);
    if(!$result) return false; // not a real song
    $query  = "SELECT count(*) FROM setData WHERE setID='".$db->escape($setID)."' AND songID='".$db->escape($songID)."'";
    $result = $db->get_var($query);
    if($result) return false; // song already in set
    $query = "SELECT count(*) FROM setData WHERE setID='".$db->escape($setID)."'";
    $count = $db->get_var($query);
    if($position && $position >= 1 && $position <= $count) {
        $query = "UPDATE setData SET position=position+1 WHERE setID='".$db->escape($setID)."' AND position >= $position";
        $db->query($query);
    } else {
        $position = $count+1;
    }
    $query  = "INSERT INTO setData (setID, songID, position)";
    $query .= "VALUES ('".$db->escape($setID)."', '".$db->escape($songID)."', ".intval($position).")";
    $db->query($query);
    
    $query="UPDATE sets SET updated=".time().", size=size+1 WHERE setID='".$db->escape($setID)."'";
    $db->query($query);
    return true;
}

function delete_song_from_set($songID, $setID = null) {
    global $db;
    $sets = array();
    if($setID) {
        $sets[] = $setID;
    } else {
        $query = "SELECT setID FROM sets WHERE 1";
        $rows  = $db->get_results($query);
        if($rows) {
            foreach($rows as $row) $sets[] = $row->setID;
        } else {
            return;
        }
    }
    foreach($sets as $setID) {
        $query    = "SELECT position FROM setData WHERE songID='".$db->escape($songID)."' AND setID='".$db->escape($setID)."'";
        $position = $db->get_var($query);
        if(!$position) continue; // not in set
        
        $query    = "DELETE FROM setData WHERE songID='".$db->escape($songID)."' AND setID='".$db->escape($setID)."'";
        $result   = $db->query($query);
        if(!$result) continue;
        
        $query = "UPDATE setData SET position=position-1 WHERE setID='".$db->escape($setID)."' AND position >=".$position;
        $db->query($query);
        
        $query="UPDATE sets SET updated=".time().", size=size-1 WHERE setID='".$db->escape($setID)."'";
        $db->query($query);
    }
}

function move_song_in_set($songID, $setID, $position) {
    global $db;
    delete_song_from_set($songID, $setID);
    add_song_to_set($songID, $setID, $position);
}
?>