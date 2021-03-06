<script type="text/javascript" src="resources/libraries/TouchSwipe/jquery.TouchSwipe.min.js"></script>
<script type="text/javascript">
    function goBack() {
        if($(document).scrollTop() > 0) {
            var newPos = $(document).scrollTop()-0.9*$(window).height();
            if(newPos < 0) newPos = 0;
            $(document).scrollTop(newPos);
        } else {
            $('#setPrev')[0].click();
        }
    }
    function goForward() {
        if($(document).scrollTop()+$(window).height() < $(document).height()) {
            var newPos = $(document).scrollTop()+0.9*$(window).height();
            if(newPos > $(document).height()) newPos = $(document).height();
            $(document).scrollTop(newPos);
        } else {
            $('#setNext')[0].click();
        }
    }
    $(document).ready(function() {
        $(document).on( "keydown", function( event ) {
            if(event.which == 37  || event.which == 52 || event.which == 100) {
                goBack();
            } else if(event.which == 39 || event.which == 54 || event.which == 102) {
                goForward();
            }
        });
        
        $('body').swipe({
            swipeLeft:function(event, direction, distance, duration, fingerCount) {
                $('#setNext')[0].click();
            },
            swipeRight:function(event, direction, distance, duration, fingerCount) {
                $('#setPrev')[0].click();
            },
            threshold:150
        });
    });
</script>
<div id="main">
<?php
if($song) {
    $extension = strtolower(pathinfo($song->location, PATHINFO_EXTENSION));
    if($extension == 'txt') {
?>
    <pre><?=print_DB(file_get_contents(__FILES__.$song->location))?></pre>
<?php
    } elseif($extension == 'chopro') {
?>
    <center><h2><?=$song->title?></h2></center>
    <div id="theSong"><?=print_DB(file_get_contents(__FILES__.$song->location))?></div>
    <script type="text/javascript" src="resources/libraries/chordpro.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var raw       = $('#theSong').text();
            var formatted = parseChordPro(raw);
            $('#theSong').html(formatted);
        });
    </script>
<?php
    } elseif($extension == 'pdf') {
?>
    <div id="pdfControls">
        <a class="pdf-layout-single"     >Single Page  </a>
        <a class="pdf-layout-double-odd" >2 Page (Odd) </a>
        <a class="pdf-layout-double-even">2 Page (Even)</a>
        &nbsp;&nbsp;
        <a class="pdf-page-prev" href="">&lt;</a>
        <span class="pdf-page-num">&nbsp;/&nbsp;</span>
        <a class="pdf-page-next" href="">&gt;</a>
        
    </div>
    <div id="theSongPdf"></div>
    <script type="text/javascript" src="resources/libraries/stickyjs/jquery.sticky.js"></script>
    <script type="text/javascript" src="resources/libraries/pdf.js/build/pdf.min.js"></script>
    <script type="text/javascript" src="resources/libraries/drawpdf.js"></script>
    <script type="text/javascript">
        function updateDefaultLayout(number) {
            var formData = {
                songID: "<?=print_DB($song->songID)?>",
                layout:   number
            };
            $.ajax({
                url : "ajax.php",
                type: "POST",
                data : formData
            });
        }
        
        PDFJS.workerSrc = 'resources/libraries/pdf.js/build/pdf.worker.js';
        $(document).ready(function() {
            var controlWidthRef = $('#pdfControls').before($('<div/>')).prev();
            $('#pdfControls').sticky({
                getWidthFrom: controlWidthRef,
                responsiveWidth: true
            });
            initializePDF("#theSongPdf", '#pdfControls', "<?=print_DB(__FILES__.$song->location)?>", <?=intVal($song->defaultLayout)?>);
        });
    </script>
<?php
    } else {
        echo "<img src='".print_DB(__FILES__.$song->location)."' alt='".$song->title."' id='theSongImg'/>";
    }
} else {
?>
<?php
}
?>
</div>