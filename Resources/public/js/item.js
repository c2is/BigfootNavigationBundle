$(function () {

    /**
     * Handle modal response
     */
    var
        modal   = $('#ajax-modal'),
        options = {
            success: successResponse,
        };

    $('.modal-save')
        .unbind('click')
        .on('click', function (event) {
            var form = $(this)
                .closest('.modal')
                    .find('form');

            var action = form.attr('action');
            action = setUrlParameter(action, 'parent', idParent);

            form
                .attr('action', action)
                .ajaxSubmit(options);
        });

    function successResponse(responseText, statusText, xhr) {
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

function setUrlParameter(url, parameterName, parameterValue)
{
    var newAdditionalURL = "";
    var tempArray        = url.split("?");
    var baseUrl          = tempArray[0];
    var additionalUrl    = tempArray[1];
    var temp             = "";

    if (additionalUrl) {
        tempArray = additionalUrl.split("&");
        for (var i in tempArray) {
            if (tempArray[i].indexOf(parameterName) == -1) {
                newAdditionalURL += temp+tempArray[i];
                temp = "&";
            }
        }
    }

    return baseUrl + "?" + newAdditionalURL + temp + parameterName + "=" + parameterValue;
}
