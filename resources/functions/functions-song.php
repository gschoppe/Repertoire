<?php if( !defined( 'CORELOADED' ) ) die();
function upload_song($tempFile, $title=null, $artist=null, $genre=null, $rating=null) {
    global $db;
    
    $extension = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
    $id = make_song_id($title, $tempFile);
    if($id === false) {
        return array('success'=>false, 'message'=>"Duplicate File");
    }
    
    if(!$title || !$artist) {
        $result = get_song_info($tempFile, $extension);
        if(!$title ) $title  = $result['title' ];
        if(!$artist) $artist = $result['artist'];
    }
    if(!$rating) $rating=2.5;
    $i = 1;
    $uploadFile = sanitize_file_name($title).'.'.$extension;
    while(file_exists(__FILES__.$uploadFile)) {
       $uploadFile = sanitize_file_name($title).$i.'.'.$extension;
       $i++;
    }
    switch($extension) {
        case 'txt':
        case 'chopro':
        case 'pdf':
            // NO PROCESSING
            if (!copy($tempFile, __FILES__.$uploadFile)) {
                return array('success'=>false, 'message'=>"failed to copy $tempFile to $uploadFile");
            }
            break;
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            // RESIZE
            resize_image($tempFile, __FILES__.$uploadFile, $extension, MAX_FILE_W, MAX_FILE_H);
            break;
        default:
            return array('success'=>false, 'message'=>"file extension '.".$extension."'not recognized");
            return;
    }
    $query  = "INSERT INTO music (songID, location, title, artist, genre, rating, views, uploaded)";
    $query .= " VALUES ( '".$db->escape($id)."','".$db->escape($uploadFile)."','";
    $query .= $db->escape($title)."','".$db->escape($artist)."','".$db->escape($genre)."',".$db->escape($rating).",0,".time()." )";
    $result = $db->query($query);

    unlink($tempFile);
    return array('success'=>true, 'message'=>$title." successfully uploaded!");
}

function get_song_info($path, $ext) {
    $title  = "";
    $artist = "";
    
    if($ext = "chopro") {
        // Hard Mode - get title/artist from chopro tags
        $titlepattern = "/(^{(t|title):(?P<title>.+)})|(^{(a|artist):(?P<artist>.+)})/i";
        //echo "<br/>path on 63 is: ".$path."<br/>";
        $handle  = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $matches = null;
                preg_match($titlepattern, $line, $matches);
                if(isset($matches['title']) && $matches['title']) {
                    $title = $matches['title'];
                }
                if(isset($matches['artist']) && $matches['artist']) {
                    $artist = $matches['artist'];
                }
                if($title && $artist) break;
            }
            fclose($handle);
        }
    }
    if(!$title) {
        // Easy Mode - get title from filename
        $titlearray = explode(".", basename($path));
        $title      = $titlearray[0];
    }
    
    return array('title'=>trim($title), 'artist'=>trim($artist));
}

function edit_song_info($songID) {
    global $db;
    if(!(isset($_POST['title']) && $_POST['title']))
        return array('success'=>false, 'message'=>"Title cannot be blank.");
    $title = $_POST['title'];
    if(isset($_POST['artist']) && $_POST['artist']) {
        $artist = $_POST['artist'];
    } else {
        $artist = "N/A";
    }
    if(isset($_POST['genre']) && $_POST['genre']) {
        $genre = $_POST['genre'];
    } else {
        $genre = "Uncategorized";
    }
    if(isset($_POST['rating']) && $_POST['rating']) {
        $rating = $_POST['rating'];
    } else {
        $rating = "2.5";
    }
    $query  = "UPDATE music SET title='".$db->escape($title)."', artist='".$db->escape($artist)."', genre='".$db->escape($genre)."', rating='".$db->escape($rating)."' WHERE songID='".$db->escape($songID)."'";
    $result = $db->query($query);
    if(!$result)
        return array('success'=>false, 'message'=>"Song could not be updated.");
    return array('success'=>true, 'message'=>"Song was updated successfully.");
}

function delete_song($songID) {
    global $db;
    $query = "SELECT location FROM music WHERE songID='".$db->escape($songID)."'";
    $filepath = $db->get_var($query);
    if(!$filepath) {
        return array('success'=>false, 'message'=>"Could not find song in database.");
    }
    $query = "DELETE FROM music WHERE songID='".$db->escape($songID)."'";
    $result = $db->query($query);
    if(!$result) {
        return array('success'=>false, 'message'=>"Could not find song in database.");
    }
    delete_song_from_set($songID);
    unlink(__FILES__.$filepath);
    return array('success'=>true, 'message'=>"Song has been deleted.");
}
?>