<?php if( !defined( 'CORELOADED' ) ) die();?>
<div id="main">
    <div class="modal">
        <center><h3>Upload Songsheet</h3></center>
        <p><small>Repertoire supports jpeg, gif, png, pdf, txt &amp; chopro files.</small></p>
        <form method="POST" enctype='multipart/form-data' >
            <table class="upload">
                <tr>
                    <td>File: </td>
                    <td>
                        <input type="hidden" name="MAX_FILE_SIZE" value="15000000" />
                        <input type="file" name="file" id="file" onchange="updateTitle();"  accept="image/gif, image/jpeg, image/png, application/pdf, text/plain, .chopro"/>
                    </td>
                </tr><tr>
                    <td>Title: </td>
                    <td>
                        <input type="text" name="title" id="title" />
                    </td>
                </tr><tr>
                    <td>Artist: </td>
                    <td>
                        <input type="text" name="artist" id="artist" />
                    </td>
                </tr><tr>
                    <td>Genre: </td>
                    <td>
                        <select name="genre">
                            <option value="Uncategorized">Please Select ... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
<?php
foreach($genres as $genre) {
?>
                            <option value="<?=print_DB($genre)?>"><?=print_DB($genre)?></option>
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
?>
                            <option value="<?=print_DB($rating)?>"><?=print_DB($rating)?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="submit" value="Upload"/> <input type="reset" value="Clear"/>
        </form>
        <br/>
        <div style="text-align:center;">
            <a href="bulkupload.php">Bulk Upload</a>
        </div>
    </div>
    <script language="javascript"  type="text/javascript">
        function updateTitle() {
            var filename = document.getElementById("file").value;
            var namearray = filename.split('\\');
            var basename = namearray[namearray.length - 1];
            var namearray = basename.split('.');
            var title = namearray[0];
            document.getElementById('title').value = title;
        }
    </script>
</div>