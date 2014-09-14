<?php if( !defined( 'CORELOADED' ) ) die();?>
<div id="main">
<h3><?=print_DB($set->name)?></h3>
<p><?=print_DB($set->description)?></p>
<?php
if($songs) {
?>
<ul id="setList">
<?php
    foreach($songs as $song) {
?>
    <li id="<?=print_DB($song['songID'])?>">
        <span class="handle">::</span>
        <a class="title"  href="song.php?setID=<?=print_DB($song['setID' ])?>&songID=<?=print_DB($song['songID'])?>"><?=print_DB($song['title'])?></a>
        <a class="delete" href='set.php?setID=<?=print_DB($song['setID' ])?>&delete=<?=print_DB($song['songID'])?>' class="confirm" data-title="<?=print_DB($song['title'])?>" data-artist="<?=print_DB($song['artist'])?>"><img src="resources/graphics/delete.png" class="icon" alt="delete" title="delete"/></a>
    </li>
<?php
    }
?>
</ul>
<script type="text/javascript" src="/resources/libraries/slip/slip.js"></script>
<script type="text/javascript">
    
    $(document).ready(function(){
        $(document).on('click', 'a.confirm', function(event) {
            var songName  = $(this).data('songName');
            if(!confirm('Are you sure you want to delete the song '+songName+"?  This action cannot be undone.")) {
                event.preventDefault();
            }
        });
        var setList = $('#setList')[0];
        new Slip(setList);
        // stop swiping
        setList.addEventListener('slip:beforeswipe', function(e) {
            e.preventDefault();
        }, false);
        // add drag handle
        setList.addEventListener('slip:beforewait', function(e){
            if (e.target.className.indexOf('handle') > -1) e.preventDefault();
        }, false);
        // handle reorder
        setList.addEventListener('slip:reorder', function(e) {
                e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
                var elements = [];
                $('#setList li').each(function() {
                    elements.push(this.id);
                });
                var setList = JSON.stringify(elements);
                $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: { setID: '<?=print_DB($set->setID)?>', reorder: setList }
                });
                e.preventDefault();
        }, false);
    });
</script>
<?php
} else {
?>
<div id="0000001" class="alert-message info"><p><strong>Info: </strong>You don't have any songs in this set list yet, why not add some?</p></div>
<?php
}
?>
<center><a href="index.php">Add Songs to Set</a></center>
</div>