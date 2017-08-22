$(function () {

    /**
     * Update route parameters
     */
    $('.bigfoot-link .chosen-container').css('width', '250px');

    $('body').on('change', '.bigfoot_link_routes', function (event) {
        var
            that      = $(this),
            route     = $(this).val(),
            formName  = $(this).data('parent-form-link'),
            fieldName = $(this).data('parent-form-field'),
            container = $(this).closest('.internal-link-tab');
console.log(fieldName);
console.log({ 'route': route, 'formName': formName, 'fieldName': fieldName });
console.log(Routing.generate('bigfoot_route_parameter_list', { 'route': route, 'formName': formName, 'fieldName': fieldName }));
        $.ajax({
            url:   Routing.generate('bigfoot_route_parameter_list', { 'route': route, 'formName': formName, 'fieldName': fieldName }),
            type:  'GET',
            cache: false,
            success: function (data) {
                that
                    .closest('.form-group')
                        .siblings()
                        .remove();

                container.append(data);

                $('.chosen-select', container).chosen({width: '300px'});
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
