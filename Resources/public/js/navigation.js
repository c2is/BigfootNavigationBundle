$(document).ready(function() {
    var $container = $('#menu-item-route-parameters');

    $('select.menu-item-route-choice').on('change', function() {
        displayMenuItemRouteParameters($(this).val());
    });
});

function displayMenuItemRouteParameters(route)
{
    var $container = $('#menu-item-route-parameters');

    $.ajax({
        url: $container.data('url')+'/'+route,
        type: 'GET',
        cache: false,
        success: function(data) {
            var $prototypeContainer = $('#bigfoot_menu_item_parameters', $container);

            $prototypeContainer.empty();
            $prototypeContainer.append(data);
            $('.chosen-select', $prototypeContainer).chosen();
        }
    });
}
