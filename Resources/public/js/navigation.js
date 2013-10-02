$(document).ready(function() {
    $("input.treeView").each(function() {
        var base  = $(this);
        var table = $('<table class="treeview"></table>');
        var form  = base.closest('form');

        base.after(table);
        table.tree({
            dragAndDrop: true,
            autoOpen   : 0,
            data       : JSON.parse(base.val())
        });

        table.bind('tree.move', function(event) {
            console.log('moved_node', event.move_info.moved_node);
            console.log('target_node', event.move_info.target_node);
            console.log('position', event.move_info.position);
        });

        form.on('submit', function () {
            var data = table.tree('toJson');
            base.val(data);
        });
    });
});
