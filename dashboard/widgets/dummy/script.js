
$(document).ready(function () {

    if( dashboard.debug ) {
        //alert('Script for dummy widget loaded!');
    }

    $('#widgets').on('click', '.generateNormal', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '0');
        call.done(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.generateNew', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '1');
        call.done(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.generateProblem', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '2');
        call.done(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.generateError', function () {
        var wIId = $(this).data('wiid');
        var call = dashboard.callWidgetMethod(wIId, 'generateStatus', '3');
        call.done(function (response) {
            dashboard.setWidgetStatus(wIId, response.success);
        });
    });

    $('#widgets').on('click', '.counterButton', function() {
        if( dashboard.debug ) {
            console.log('click on counterButton');
        }
        var wIId            = $(this).data('wiid');
        var counterValue    = $(this).data('counter');
        var call = dashboard.callWidgetMethod(wIId, 'countUp', counterValue);
        call.done(function (response) {
            if( dashboard.debug ) {
                console.log('counterButton response: ' + response.success.counter + '@' + response.success.time);
            }
            $('.' + wIId + '.countUp .counterButton').data('counter', response.success.counter);
            $('.' + wIId + '.countUp .counter').html(response.success.counter);
            $('.' + wIId + '.countUp .counterTime span').html(response.success.time);
        });
    });

});