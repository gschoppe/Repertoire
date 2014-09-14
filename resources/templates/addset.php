<?php if( !defined( 'CORELOADED' ) ) die();?>
<div id="main">
    <div class="modal">
        <center><h3>Create Setlist</h3></center>
        <form method="POST">
            <table class="addset">
                <tr>
                    <td>Name: </td>
                    <td>
                        <input type="text" name="name"/>
                    </td>
                </tr><tr>
                    <td>Desc: </td>
                    <td>
                        <textarea name="desc"></textarea>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="songID" value="<?=(isset($_GET['songID']) && $songID)?$songID:""?>">
            <input type="submit" value="Create Set"/>
        </form>
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