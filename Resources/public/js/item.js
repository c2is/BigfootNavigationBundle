$(function() {

    /**
     * Init chosen select and translation fields
     */
    $(".chosen-select").chosen();
    setTranslatableFields();

    /**
     * Update route parameters
     */
    var container = $('#menu-item-route-parameters');

    $('select.menu-item-route-choice').on('change', function (event) {
        var route = $(this).val();

        $.ajax({
            url:   Routing.generate('admin_route_parameter_list', { 'route': route }),
            type:  'GET',
            cache: false,
            success: function (data) {
                container
                    .empty()
                    .append(data);

                $('.chosen-select', container).chosen();
            }
        });
    });

    /**
     * Handle external link
     */
    var
        linkType        = $('#admin_menu_item_linkType'),
        externalLink    = $('#admin_menu_item_externalLink'),
        route           = $('#admin_menu_item_route'),
        routeParameters = $('#menu-item-route-parameters');

    if (externalLink.val() === '') {
        enable(route);
        routeParameters.show();

        disable(externalLink);
    } else {
        disable(route)
        routeParameters.hide();

        enable(externalLink);
    }

    linkType
        .on('click', function (event) {
            if ($(this).is(':checked')) {
                disable(route)
                routeParameters.hide();

                enable(externalLink);
            } else {
                enable(route);
                routeParameters.show();

                disable(externalLink);
            }
    });

    function enable(item)
    {
        item
            .removeAttr('disabled')
            .parent()
                .show();
    }

    function disable(item)
    {
        item
            .attr('disabled','disabled')
            .parent()
                .hide();
    }

    /**
     * Handle modal response
     */
    var options = {
        success: successResponse,
    };

    $('.modal-save')
        .unbind('click')
        .on('click', function (event) {
            var form = $(this)
                .closest('.modal')
                    .find('form');

            var action = form.attr('action');

            form
                .attr('action', action + '?modal=1')
                .ajaxSubmit(options);
        });

    function successResponse(responseText, statusText, xhr) {
        var modal = $('.modal-body').closest('.modal');

        if (responseText.status === true) {
            modal
                .find('.modal-body')
                .empty()
                .prepend("<div class='alert alert-block alert-success'>" + responseText.message + '</div>');

            var
                itemId      = responseText.content.itemId,
                itemName    = responseText.content.itemName,
                idParent    = responseText.content.parent,
                content     = responseText.content.view,
                container   = $('.dd.nestable'),
                currentItem = container.find("[data-id='" + itemId + "']");

<<<<<<< HEAD
            if (currentItem.length == 1) {
=======
            if (currentItem.length) {
>>>>>>> refs/heads/master
                currentItem
                    .find('.dd-handle:first')
                    .html(itemName);
            } else{
                if (container.find('.dd-list').length === 0) {
                    container.append('<ol class="dd-list">' + content + '</ol>');
                } else if (typeof idParent === "undefined" || idParent === null) {
<<<<<<< HEAD
                    container.find('.dd-list:first').append(content);
=======
                    container
                        .find('.dd-list:first')
                        .append(content);
>>>>>>> refs/heads/master
                } else {
                    var li = container.find("[data-id='" + idParent + "']");

                    if (li.find('.dd-list').length === 0) {
                        li.append('<ol class="dd-list">' + content + '</ol>');
                    } else{
<<<<<<< HEAD
                        li.find('.dd-list:first').append(content);
=======
                        li
                            .find('.dd-list:first')
                            .append(content);
>>>>>>> refs/heads/master
                    }
                }
            }

<<<<<<< HEAD

=======
>>>>>>> refs/heads/master
            modal.modal('hide');
        } else {
            modal
                .find('.modal-body')
                .empty()
                .prepend("<div class='alert alert-block alert-danger'>" + responseText.message + '</div>')
                .append(responseText.content);
        }
    }

});
