<?php if( !defined( 'CORELOADED' ) ) die();
$menu = array(
    'Home'      => array('href'=>"./"            , 'icon'=>'fa-home'           , 'active'=>""),
    'Set Lists' => array('href'=>"./setlists.php", 'icon'=>'fa-list'           , 'active'=>""),
    'Upload'    => array('href'=>"./upload.php"  , 'icon'=>'fa-upload'         , 'active'=>""),
    'Help'      => array('href'=>"./help.php"    , 'icon'=>'fa-question-circle', 'active'=>"")
);
if(isset($activeMenuItem) && $activeMenuItem) {
    $menu[$activeMenuItem]['active'] = "active";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Repertiore</title>
    <link rel="shortcut icon" href="resources/graphics/icon.gif"/>
    <link rel="stylesheet" type="text/css" href="resources/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/alert-message.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/Font-Awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/datatables/css/jquery.datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/datatables-responsive/css/datatables.responsive.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/raty/jquery.raty.css"/>
    <script type="text/javascript" src="/resources/libraries/jquery.js"></script>
</head>
<body>
    <div id="menu" class="clearfix">
        <ul id="links">
<?php
foreach($menu as $itemName=>$itemDetails) {
?>
            <li><a href="<?=$itemDetails['href']?>" class="<?=$itemDetails['active']?>"><i class="fa <?=$itemDetails['icon']?>"></i><span class="menutext"><?=$itemName?></span></a></li>
<?php
}
?>
        </ul>
<?php
if(isset($song) && $song) {
?>
        <ul id="zoomcontrols">
            <li><a id="zoomout">&ndash;</a></li><li><a id="zoomreset"></a></li><li><a id="zoomin">+</a></li>
        </ul>
        <script type="text/javascript">
            function updateDefaultZoom(percent) {
                var formData = {
                    songID: "<?=print_DB($song->songID)?>",
                    zoom:   percent
                };
                $.ajax({
                    url : "ajax.php",
                    type: "POST",
                    data : formData
                });
            }
            $(window).load(function() {
                var defaultZoom = <?=print_DB(round(intval($song->defaultZoom)))?>;
                $('#theSongImg').css('width', defaultZoom+'%');
                $('#theSongPdf').css('width', defaultZoom+'%');
                $('#theSong').css('font-size', defaultZoom+'%');
                $('#zoomreset').text(defaultZoom+'%');
                $(window).trigger('resize');
                
                $('#zoomout').click(function() {
                    var percent = Math.round(parseFloat($('#zoomreset').text())*.9);
                    $('#theSongImg').css('width', percent+'%');
                    $('#theSongPdf').css('width', percent+'%');
                    $('#theSong').css('font-size',percent+'%');
                    $('#zoomreset').text(percent+'%');
                    $(window).trigger('resize');
                    updateDefaultZoom(percent);
                });
                $('#zoomreset').click(function() {
                    $('#theSongImg').css('width', '100%');
                    $('#theSongPdf').css('width', '100%');
                    $('#theSong').css('font-size','100%');
                    $('#zoomreset').text("100%");
                    $(window).trigger('resize');
                    updateDefaultZoom(100);
                });
                $('#zoomin').click(function() {
                    var percent = Math.round(parseFloat($('#zoomreset').text())*1.111);
                    $('#theSongImg').css('width', percent+'%');
                    $('#theSongPdf').css('width', percent+'%');
                    $('#theSong').css('font-size',percent+'%');
                    $('#zoomreset').text(percent+'%');
                    $(window).trigger('resize');
                    updateDefaultZoom(percent);
                });
            });
        </script>
<?php
}
?>
<?php
if(isset($setID) && isset($setControls) && $setControls) {
?>
        <ul id="setcontrols">
            <li><a id="setPrev" href="song.php?setID=<?=print_DB($setID)?>&songID=<?=print_DB($setControls['prev'])?>">&laquo;</a></li><li><a id="setPos"  href="set.php?setID=<?=print_DB($setID)?>"><?=print_DB($setControls['position'])?>/<?=print_DB($setControls['count'])?></a></li><li><a id="setNext" href="song.php?setID=<?=print_DB($setID)?>&songID=<?=print_DB($setControls['next'])?>">&raquo;</a></li>
        </ul>
<?php
}
?>
    </div>
<?php
    include('message.php');
?>