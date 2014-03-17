$(function() {

    /**
     * Init chosen select and translation fields
     */
    $(".chosen-select").chosen();
    setTranslatableFields();

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
                .attr('action', action + '?blank=1')
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

            if (currentItem.length) {
                currentItem
                    .find('.dd-handle:first')
                    .closest('li')
                        .empty()
                        .html(content);
            } else {
                if (container.find('.dd-list').length === 0) {
                    container.append('<ol class="dd-list">' + content + '</ol>');
                } else if (typeof idParent === "undefined" || idParent === null) {
                    container
                        .find('.dd-list:first')
                        .append(content);
                } else {
                    var li = container.find("[data-id='" + idParent + "']");

                    if (li.find('.dd-list').length === 0) {
                        li.append('<ol class="dd-list">' + content + '</ol>');
                    } else {
                        li
                            .find('.dd-list:first')
                            .append(content);
                    }
                }
            }

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
