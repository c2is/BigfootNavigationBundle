$(function() {

    /**
     * Update route parameters
     */
    $('.chosen-container').css('width', '250px');

    $('.admin_link_routes').on('change', function (event) {
        var
            that      = $(this),
            route     = $(this).val(),
            field     = $(this).data('link'),
            container = $(this).closest('.internal-link-tab');

        $.ajax({
            url:   Routing.generate('admin_route_parameter_list', { 'route': route, 'field': field }),
            type:  'GET',
            cache: false,
            success: function (data) {
                that
                    .closest('.form-group')
                        .siblings()
                        .remove();

                container.append(data);

                $('.chosen-select', container).chosen();
            }
        });
    });

    /**
     * Handle linkType
     */
    $('body').on('click', '.internal-link', function (event) {
        $(this).closest('.nav').next('.tab-content').find('.link-type').val(true);
    });

    $('body').on('click', '.external-link', function (event) {
        $(this).closest('.nav').next('.tab-content').find('.link-type').val(false);
    });

});
