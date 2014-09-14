<?php if( !defined( 'CORELOADED' ) ) die();
if(isset($row) && $row) {
?>
<div id="main">
    <div style="position: relative;margin:auto;padding: 5px;width:350px;border: 1px solid black;">
        <center><h3>Editing: <?=print_DB($row->title)?></h3></center>
        <form method="POST" enctype='multipart/form-data' >
            <table class="edit">
                <tr>
                    <td>Title: </td>
                    <td>
                        <input type="text" name="title" id="title" value="<?=print_DB($row->title)?>"/>
                    </td>
                </tr><tr>
                    <td>Artist: </td>
                    <td>
                        <input type="text" name="artist" id="artist" value="<?=print_DB($row->artist)?>"/>
                    </td>
                </tr><tr>
                    <td>Genre: </td>
                    <td>
                        <select name="genre">
                            <option value="Uncategorized">Please Select ... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
<?php
foreach($genres as $genre) {
    $selected="";
    if( $row->genre == $genre) $selected=" selected=1";
?>
                            <option value="<?=print_DB($genre)?>"<?=$selected?>><?=print_DB($genre)?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr><tr>
                    <td>Rating: </td>
                    <td>
                        <select name="rating">
                            <option value="2.5">---</option>
<?php
foreach($ratings as $rating) {
    $selected="";
    if(floatval($row->rating) == floatval($rating)) $selected=" selected=1";
?>
                            <option value="<?=print_DB($rating)?>"<?=$selected?>><?=print_DB($rating)?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="submit" value="Save Changes"/>
        </form>
        <br/>
    </div>
</div>
<?php
}
?>