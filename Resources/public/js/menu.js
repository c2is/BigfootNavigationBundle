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
                success: function (data) {

                }
            });
        }
    });

    /**
     * Delete menu item tree
     */
<<<<<<< HEAD
    $('.delete-menu-item-tree').on('click', function (event) {
=======
    $(this).on('click', '.delete-menu-item-tree', function (event) {
>>>>>>> refs/heads/master
        event.preventDefault();

        var link = $(this);

        $.ajax({
            url:   Routing.generate('admin_menu_item_delete', { 'id': link.parent().data('id') }),
            type:  'GET',
            cache: false,
            success: function (data) {
                if (data.status === true) {
                    var parent = link.parent();

                    parent.fadeOut(300, function () {
                        parent.remove();
                    });
                }
            }
        });
    });

});
