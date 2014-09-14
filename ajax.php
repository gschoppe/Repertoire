<?php
require_once("resources/core.php");
if(!(isset($_POST) && $_POST || isset($_FILES) && $_FILES)) {
    echo "this file can only be accessed by valid AJAX requests";
    die();
}
// Process SetList Request
if(isset($_POST['setID']) && isset($_POST['reorder']) && $_POST['setID'] && $_POST['reorder']) {
    $setID   = $_POST['setID'];
    $query   = "SELECT * FROM sets WHERE setID='".$db->escape($setID)."'";
    $results = $db->get_row($query);
    if($results) {
        //json request
        $newOrder = json_decode($_POST['reorder']);
        $oldOrder = get_set_contents($setID);
        if(count($oldOrder) != count($newOrder)) die;
        $position = 1;
        $newID    = null;
        foreach($oldOrder as $oldID=>$val) {
            $newID = array_shift($newOrder);
            if($oldID != $newID) break;
            $position++;
        }
        move_song_in_set($newID, $setID, $position);
    }
    die;
}

// Process BulkUpload Request
if(isset($_FILES) && $_FILES) {
    $message    = array('type'=>'success', 'text'=>"File Uploaded", 'destructs'=>true);
    $file       = $_FILES['file'];
    $simplename = basename($file['name']);
    $title      = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension  = strtolower(pathinfo($simplename, PATHINFO_EXTENSION));
    $uploadFile = __ROOT__.'/'.__TEMP__.md5($simplename).'.'.$extension;
    if(!is_uploaded_file($file['tmp_name'])) {
        $message = array('type'=>'error', 'text'=>$file['tmp_name']." was not uploaded correctly, try again", 'destructs'=>false);
    } elseif(!@move_uploaded_file($file['tmp_name'], $uploadFile)) {
        $message = array('type'=>'error', 'text'=>"error moving file from ".$file["tmp_name"]." to $uploadFile", 'destructs'=>false);
    } else {
        $result = upload_song($uploadFile, $title);
        if(!$result['success']) {
            $message = array('type'=>'error', 'text'=>$result['message'], 'destructs'=>false);
        }
    }
    echo json_encode($message);
    die();
}

// Process Datatables Request
if(isset($_POST['draw']) && ($_POST['draw'] || $_POST['draw']===0)) {
    $tableMap = array(
        'songID'   => 'songID',
        'location' => 'location',
        'title'    => 'title',
        'artist'   => 'artist',
        'genre'    => 'genre',
        'rating'   => 'rating',
        'views'    => 'views',
        'added'    => 'uploaded'
    );
    $return                 = array();
    $return['draw']         = intval($_POST['draw']);
    $query                  = "SELECT count(*) FROM music";
    $return['recordsTotal'] = intval($db->get_var($query));
    $search                 = "1";
    $order                  = "";
    $limit                  = "";
    // get the requested columns
    $columns = array();
    $colList = "*";
    if(isset($_POST['columns']) && $_POST['columns']) {
        $columns    = $_POST['columns'];
        $colNames   = array();
        $searchCols = array();
        $orderCols  = array();
        foreach($columns as $col) {
            $name = "";
            if(isset($tableMap[$col['data']]))
                $name = $db->escape($tableMap[$col['data']]);
            if($name) {
                $colNames[$col['data']] = $name;
                if($col['searchable'])$searchCols[$col['data']] = $name;
                if($col['orderable' ])$orderCols[ $col['data']] = $name;
                if($col['search']['value']) {
                    $search .= " AND ".$name." LIKE '%".$db->escape($col['search']['value'])."%'";
                }
            }
        }
        if($colNames) {
            $colList = implode(', ', $colNames);
        }
    }
    // build the global search
    if(isset($_POST['search']['value']) && $_POST['search']['value']) {
        $searchArray = array();
        foreach($searchCols as $name) {
            $searchArray[] = $name." LIKE '%".$db->escape($_POST['search']['value'])."%'";
        }
        $search .= " AND (".implode(' OR ', $searchArray).')';
    }
    //build order
    if(isset($_POST['order']) && $_POST['order']) {
        $orderArray = array();
        foreach($_POST['order'] as $ordering) {
            if(isset($columns[$ordering['column']]) && $columns[$ordering['column']]['orderable']) {
                $orderArray[] = $db->escape($tableMap[$columns[$ordering['column']]['data']])." ".$db->escape($ordering['dir']);
            }
        }
        $order = " ORDER BY ".implode(', ', $orderArray);
    }
    // build limits
    if(isset($_POST['length']) && isset($_POST['start'])) {
        $pageSize = intval($_POST['length']);
        $offset   = intval($_POST['start' ]);
        if($pageSize != -1)
            $limit = " LIMIT ".$pageSize." OFFSET ".$offset;
    }
    $query   = "SELECT count(*) FROM music WHERE ".$search;
    $return['recordsFiltered'] = $db->get_var($query);
    $query   = "SELECT * FROM music WHERE ".$search.$order.$limit;
    $results = $db->get_results($query, ARRAY_A);
    
    $return['data'] = array();
    foreach($results as $row) {
        // preprocess results for Repertoire
        $rrow = array();
        $rrow = $row;
        $rrow['title' ]  = "<a href='song.php?songID=".print_DB($row['songID'])."'>".print_DB($row['title'])."</a>";
        $rrow['rating']  = "<div id='".print_DB($row['songID'])."' class='rating'><span class='hidden'>".print_DB($row['rating'])."</span></div>";
        $rrow['added' ]  = date('Y-m-d', $row['uploaded']);
        $rrow['tools' ]  = "<a href='edit.php?songID=".print_DB($row['songID'])."'><img src='resources/graphics/edit.png' class='icon' alt='edit' title='edit'/></a>";
        $rrow['tools' ] .= "<a href='setlists.php?songID=".print_DB($row['songID'])."'><img src='resources/graphics/add.png' class='icon' alt='add to setlist' title='add to setlist'/></a>";
        $rrow['tools' ] .= "<a href='index.php?delete=".print_DB($row['songID'])."' class='confirm' data-title='".print_DB($row['title'])."' data-artist='".print_DB($row['artist'])."'><img src='resources/graphics/delete.png' class='icon' alt='delete' title='delete'/></a>";
        $return['data'][] = $rrow;
    }
    
    echo json_encode($return);
    die();
}