
$(document).ready(function () {

    $('#widgets').on('click', '.generateNormal', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '0');
        call.success(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.generateNew', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '1');
        call.success(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.generateProblem', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '2');
        call.success(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.generateError', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '3');
        call.success(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

});