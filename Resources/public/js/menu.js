$(function() {

    /**
     * Init chosen select and translation fields
     */
    $(".chosen-select").chosen();
    setTranslatableFields();

    /**
     * Handle menu treeview
     */
    $('.dd').nestable({
        dropCallback: function (details) {
            var
                id     = details.sourceId,
                parent = details.destParent.closest('li').data('id');
                index  = details.sourceEl.index();

            if (typeof parent === "undefined" || parent === null) {
                parent = false;
            }

            $.ajax({
                url:   Routing.generate('admin_menu_item_edit_tree_position', { 'id': id, 'parent': parent, 'position': index }),
                type:  'GET',
                cache: false,
            });
        }
    });

    /**
     * Delete menu item tree
     */
    $(this).on('click', '.delete-menu-item-tree', function (event) {
        event.preventDefault();

        var link = $(this);

        $.ajax({
            url:   link.attr('href'),
            type:  'GET',
            cache: false,
            success: function (data) {
                if (data.status === true) {
                    var parent = link.closest('li');

                    parent.fadeOut(300, function () {
                        parent.remove();
                    });
                }
            }
        });
    });

});
