<?php if( !defined( 'CORELOADED' ) ) die();?>
<div id="main">
    <div class="modal">
        <center><h3>Bulk Upload</h3></center>
        <p><small>Repertoire supports jpeg, gif, png, pdf, txt &amp; chopro files.</small></p>
        <form method="POST" enctype='multipart/form-data' >
            <table class="upload">
                <tr>
                    <td>Files: </td>
                    <td>
                        <input type="hidden" name="MAX_FILE_SIZE" value="15000000" />
                        <input type="file" name="files[]" multiple="1" id="file" accept="image/gif, image/jpeg, image/png, application/pdf, text/plain, .chopro"/>
                    </td>
                </tr>
            </table>
            <hr/>
            <small><b>Upload Status:</b></small>
            <div id="uploadStatus"></div>
        </form>
        <script type="text/javascript">
            function scroll_to_bottom(el) {
                el.scrollTop(el.prop("scrollHeight") - el.height());
            }
            function resetFormElement(e) {
              $(e).wrap('<form>').closest('form').get(0).reset();
              $(e).unwrap();
            }
            function recursively_process_files(files, statusElement) {
                var file = files.shift();
                console.log(file.name+': ');
                var formData = new FormData();
                formData.append('file', file);
                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    dataType: 'json',
                    processData: false, // Don't process the files
                    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                    success: function(data, textStatus, jqXHR) {
                        if( data.type == 'success') {
                            // Success
                            var status   = $('<div />' , {'class': "success" });
                            var filename = $('<span />', {'class': "filename"}).text(file.name);
                            var desc = $('<span />', {'class': "desc"}).text("Uploaded ["+files.length+" remaining]");
                            status.append(filename, desc);
                            statusElement.append(status);
                            scroll_to_bottom(statusElement);
                            console.log(data)
                            if(files.length > 0) {
                                recursively_process_files(files, statusElement);
                            } else {
                                var status   = $('<div />' , {'class': "note" }).text("Processing Complete");
                                statusElement.append(status);
                                scroll_to_bottom(statusElement);
                                console.log("Processing Complete");
                                resetFormElement('#file');
                            }
                        } else {
                            // Failure
                            var status   = $('<div />' , {'class': "error" });
                            var filename = $('<span />', {'class': "filename"}).text(file.name);
                            var desc = $('<span />', {'class': "desc"}).text(data.text);
                            status.append(filename, desc);
                            statusElement.append(status);
                            console.log(data);
                            var status   = $('<div />' , {'class': "note" }).text("Processing Cancelled");
                            statusElement.append(status);
                            scroll_to_bottom(statusElement);
                            console.log("Processing Cancelled");
                            resetFormElement('#file');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        // more failure
                        var status   = $('<div />' , {'class': "error" });
                        var filename = $('<span />', {'class': "filename"}).text(file.name);
                        var desc = $('<span />', {'class': "desc"}).text(textStatus);
                        status.append(filename, desc);
                        statusElement.append(status);
                        scroll_to_bottom(statusElement);
                        console.log('ERRORS: ' + textStatus);
                        var status   = $('<div />' , {'class': "note" }).text("Processing Cancelled");
                        statusElement.append(status);
                        console.log("Processing Cancelled");
                        resetFormElement('#file');
                    }
                });
            }
            $(document).ready(function() {
                var statusElement = $('#uploadStatus');
                $('input[type=file]').on('change', function(event) {
                    var files = event.target.files;
                    console.log(files);
                    var fileArray = new Array();
                    for(var i = 0, f; f = files[i]; i++) {
                        fileArray.push(f);
                    }
                    var status   = $('<div />' , {'class': "note" }).text("Processing Started...");
                    statusElement.append(status);
                    scroll_to_bottom(statusElement);
                    console.log("Processing Started...");
                    recursively_process_files(fileArray, statusElement);
                });
            });
        </script>
    </div>
</div>