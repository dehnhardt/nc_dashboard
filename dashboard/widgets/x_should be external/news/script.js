
$(document).ready(function () {

    dashboardNews.initialize();

});

dashboardNews = {

    initialize: function () {

        $('#widgets').on('click', '.newsitem .markAsRead', function () {
            dashboardNews.markNewsAsRead(this);
        });

        dashboard.widgetCallback['news'] = function() {
            dashboardNews.resizeContentImg();
        };

        dashboardNews.resizeContentImg();

    },


    // send ajax request for mark as read action
    markNewsAsRead: function (e) {
        dashboard.showWaitSymbol();
        var newsId  = $(e).data('newsid');
        var wIId    = $(e).data('wiid');

        var call = dashboard.callWidgetMethod(wIId, 'markAsRead', newsId);
        call.success(function (response) {
            if( response.success == '0' ) {
                dashboard.setWidgetStatus(wIId, 2);
            }
            dashboard.refreshWidget(wIId);
        });
        dashboard.hideWaitSymbol();
    },


    // resize big images
    resizeContentImg: function () {
        $('#widgets .widget.news .content img').each(function() {
            var maxWidth    = $('#widgets .widget.news .content').width() - 20;
            var ratio       = 0;
            var width       = $(this).width();
            var height      = $(this).height();

            // Check if width is larger than maxWidth
            if(width > maxWidth){
                ratio   = maxWidth / width;
                $(this).css("width", maxWidth);
                $(this).css("height", height * ratio);
                height  = height * ratio;
                width   = width  * ratio;
            }
        });
    }

}