<?php if( !defined( 'CORELOADED' ) ) die();
function print_error_message($message) {
    $messageID="alertmessage".rand();
    switch($message['type']) {
        case "warning" :
            $type = array('name'=>"Warning", 'class'=>"warning");
            break;
        case "error" :
            $type = array('name'=>"Error", 'class'=>"error");
            break;
        case "success" :
            $type = array('name'=>"Success", 'class'=>"success");
            break;
        default :
            $type = array('name'=>"Hint", 'class'=>"info");
    }
?>
    <div id="<?=print_DB($messageID)?>" class="alert-message <?=print_DB($type['class'])?>">
<?php
    if($message['destructs']) {
?>
        <a class="close" href="#">&times;</a>
<?php
    }
?>
        <p><strong><?=print_DB($type['name'])?>: </strong><?=print_DB($message['text'])?></p>
    </div>
<?php
    if($message['destructs']) {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {$('#<?=print_DB($messageID)?>').fadeOut();},5000);
            $('#<?=print_DB($messageID)?> .close').click(function(){
                $(this).parent().fadeOut();
            });
        });
    </script>
<?php
    }
}

if(isset($message) && $message) {
    if(isset($message['type'])) {
        print_error_message($message);
    } else {
        foreach($message as $m) {
            print_error_message($m);
        }
    }
}
?>