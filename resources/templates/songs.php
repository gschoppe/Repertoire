<?php if( !defined( 'CORELOADED' ) ) die();?>
<div id="main">
<?php
if($areSongs) {
?>
<table class="songList">
    <thead>
        <tr>
            <th data-class="expand">Title </th>
            <th data-hide="phone,tablet">Artist</th>
            <th data-hide="phone,tablet">Genre </th>
            <th data-hide="phone">Views </th>
            <th data-hide="phone">Rating</th>
            <th data-hide="phone,tablet">Added </th>
            <th data-hide="phone,tablet">Tools </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script type="text/javascript" src="/resources/libraries/datatables/js/jquery.datatables.min.js"></script>
<script type="text/javascript" src="/resources/libraries/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="/resources/libraries/raty/jquery.raty.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', 'a.confirm', function(event) {
            var title  = $(this).data('title');
            var artist = $(this).data('artist');
            if(!confirm('Are you sure you want to delete '+title+" by "+artist+"?  This action cannot be undone.")) {
                event.preventDefault();
            }
        });
        var responsiveHelper;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableElement = $('.songList');
        tableElement.dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url : "ajax.php",
                type: "POST"
            },
            "columns": [
                { "data": "title"                    },
                { "data": "artist"                   },
                { "data": "genre"                    },
                { "data": "views"                    },
                { "data": "rating"                   },
                { "data": "added"                    },
                { "data": "tools", "sortable": false}
            ],
            autoWidth        : false,
            fnPreDrawCallback: function () {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper) {
                    responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                }
            },
            fnRowCallback    : function (nRow) {
                responsiveHelper.createExpandIcon(nRow);
            },
            fnDrawCallback   : function (oSettings) {
                responsiveHelper.respond();
                $('.rating').each(function(){
                    $(this).raty({
                        width: 100,
                        score: $(this).find('.hidden').text(),
                        half:true,
                        space:false,
                        starOff:'resources/libraries/raty/images/star-off.png',
                        starHalf:'resources/libraries/raty/images/star-half.png',
                        starOn:'resources/libraries/raty/images/star-on.png',
                        readOnly: true
                    });
                });
            },
            stateSave: true,
            bPaginate: <?=(PAGINATE_SONG_LIST)?'true':'false'?>
        });
    });
</script>
<?php
} else {
?>
<div id="0000001" class="alert-message info"><p><strong>Info: </strong>You haven't added any songs yet!  Use the Upload link to get started.</p></div>
<?php
}
?>
</div>