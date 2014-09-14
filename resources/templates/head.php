<?php if( !defined( 'CORELOADED' ) ) die();?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Repertiore</title>
    <link rel="shortcut icon" href="resources/graphics/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="resources/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/alert-message.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/datatables/css/jquery.datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/datatables-responsive/css/datatables.responsive.css"/>
    <link rel="stylesheet" type="text/css" href="resources/libraries/raty/jquery.raty.css"/>
    <script type="text/javascript" src="/resources/libraries/jquery.js"></script>
</head>
<body>
    <div id="menu" class="clearfix">
        <ul id="links">
            <li><a href="index.php"   ><img src="resources/graphics/menu-home.png"  /><span class="menutext">Home</span></a></li><li><a href="setlists.php"><img src="resources/graphics/menu-sets.png"  /><span class="menutext">Set Lists</span></a></li><li><a href="upload.php"  ><img src="resources/graphics/menu-upload.png"/><span class="menutext">Upload</span></a></li><li><a href="help.php"    ><img src="resources/graphics/menu-help.png"  /><span class="menutext">Help</span></a></li>
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
                    url : "song.php",
                    type: "GET",
                    data : formData
                });
            }
            $(window).load(function() {
                var initialZoom = {
                    img: $('#theSongImg').width(),
                    pdf: $('#theSongPdf').width(),
                    txt: parseFloat($('#theSong').css('font-size'))
                };
                var maxZoom     = $('#main').width();
                var defaultZoom = <?=print_DB(round(intval($song->defaultZoom)))?>;
                $('#theSongImg').width($('#theSongImg').width()*defaultZoom/100);
                $('#theSongPdf').width($('#theSongPdf').width()*defaultZoom/100);
                $(window).trigger('zoomPDF');
                $('#theSong').css('font-size', parseFloat($('#theSong').css('font-size'))*defaultZoom/100);
                $('#zoomreset').text(defaultZoom+'%');
                
                $('#zoomout').click(function() {
                    $('#theSongImg').width($('#theSongImg').width()*.9);
                    $('#theSongPdf').width($('#theSongPdf').width()*.9);
                    $(window).trigger('zoomPDF');
                    var size = parseFloat($('#theSong').css('font-size'));
                    $('#theSong').css('font-size',size*.9);
                    var percent = Math.round(parseFloat($('#zoomreset').text())*.9);
                    $('#zoomreset').text(percent+'%');
                    updateDefaultZoom(percent);
                });
                $('#zoomreset').click(function() {
                    $('#theSongImg').width(initialZoom.img);
                    $('#theSongPdf').width(initialZoom.pdf);
                    $(window).trigger('zoomPDF');
                    $('#theSong').css('font-size',initialZoom.txt);
                    $('#zoomreset').text("100%");
                    updateDefaultZoom(100);
                });
                $('#zoomin').click(function() {
                    if($('#theSongImg').width() >= maxZoom) return;
                    if($('#theSongPdf').width() >= maxZoom) return;
                    $('#theSongImg').width($('#theSongImg').width()*1.111);
                    $('#theSongPdf').width($('#theSongPdf').width()*1.111);
                    $(window).trigger('zoomPDF');
                    var size = parseFloat($('#theSong').css('font-size'));
                    $('#theSong').css('font-size',size*1.111);
                    var percent = Math.round(parseFloat($('#zoomreset').text())*1.111);
                    $('#zoomreset').text(percent+'%');
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