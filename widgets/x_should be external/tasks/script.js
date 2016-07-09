
$(document).ready(function () {

    $('#widgets').on('click', '.task .markAsDone', function () {
        var taskId  = $(this).data('taskid');
        var wIId    = $(this).data('wiid');

        var call = dashboard.callWidgetMethod(wIId, 'markAsDone', taskId);
        call.success(function (response) {
            alert(response.success);
        });
    });

});