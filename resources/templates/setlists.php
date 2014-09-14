<?php if( !defined( 'CORELOADED' ) ) die();?>
<div id="main">
<?php
if($results) {
?>
<table class="setList">
    <thead>
        <tr>
            <th data-class="expand">Name </th>
            <th data-hide="phone">Description</th>
            <th>Songs </th>
            <th data-hide="phone,tablet">Last Updated </th>
            <th data-hide="phone">Tools </th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach($results as $row) {
?>
        <tr> 
            <td>
<?php
        if(isset($songID) && $songID) {
?>
                <a href='set.php?setID=<?=print_DB($row->setID)?>&addSong=<?=print_DB($songID)?>'><img src="resources/graphics/add.png" class="icon" alt="add to setlist" title="add to setlist"/></a>
<?php
        }
?>
                <a href='set.php?setID=<?=print_DB($row->setID)?>'><?=print_DB($row->name)?></a>
            </td>
            <td><?=print_DB($row->description)?></td>
            <td><?=print_DB($row->size)?></td>
            <td><?=date('Y-m-d', $row->updated)?></td>
            <td>
                <a href='addset.php?setID=<?=print_DB($row->setID)?>'><img src="resources/graphics/edit.png" class="icon" alt="edit" title="edit"/></a>
                <a href='setlists.php?delete=<?=print_DB($row->setID)?>' class="confirm" data-title="<?=print_DB($row->title)?>" data-artist="<?=print_DB($row->artist)?>"><img src="resources/graphics/delete.png" class="icon" alt="delete" title="delete"/></a>
            </td>
        </tr>
<?php 
    }
?>
    </tbody>
</table>
<script type="text/javascript" src="/resources/libraries/datatables/js/jquery.datatables.min.js"></script>
<script type="text/javascript" src="/resources/libraries/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', 'a.confirm', function(event) {
            var setName  = $(this).data('setName');
            if(!confirm('Are you sure you want to delete the set '+setName+"?  This action cannot be undone.")) {
                event.preventDefault();
            }
        });
        var responsiveHelper;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableElement = $('.setList');
        tableElement.dataTable({
            "aoColumns": [ 
                null,
                null,
                null,
                null,
                { "bSortable": false }
            ],
            autoWidth        : false,
            preDrawCallback: function () {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper) {
                    responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                }
            },
            rowCallback    : function (nRow) {
                responsiveHelper.createExpandIcon(nRow);
            },
            drawCallback   : function (oSettings) {
                responsiveHelper.respond();
            }
        });
    });
</script>
<?php
} else {
?>
<div id="0000001" class="alert-message info"><p><strong>Info: </strong>You don't have any setlists yet, why not create one?</p></div>
<?php
}
?>
<center><a href="newset.php<?=(isset($songID)&&$songID)?"?songID=".$songID:""?>">Create New Setlist</a></center>
</div>