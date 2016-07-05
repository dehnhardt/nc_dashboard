/**
 * ownCloud - dashboard
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Florian Steffens <webmaster@freans.de>
 * @copyright Florian Steffens 2014
 */

(function ($, OC) {

	$(document).ready(function () {
        if( dashboard.debug ) {
            console.log('Start with debugging settings');
        }

        $('#dashboardSettings .widgets .widget .groups .group').on('change', 'input', function () {
            var wIdG = $(this).data('widg');
            dashboard.setWidgetGroup(wIdG, $(this).prop( "checked" ));
        });

    });

})(jQuery, OC);



dashboard = {

    debug: true,

    // mode = true for enable
    setWidgetGroup: function (wIdG, $mode) {
        var url;
        if( $mode === true ) {
            url     = OC.generateUrl('/apps/dashboard/widget/management/enable/' + wIdG,[]);
        } else {
            url     = OC.generateUrl('/apps/dashboard/widget/management/disable/' + wIdG,[]);
        }
        $.ajax({
            url: url,
            method: 'PUT'
        }).done(function (r) {
            if(r.success == 0) {
                if( dashboard.debug ) {
                    console.log('could not disable widget ' + wIdG);
                }
            } else {
                $('.' + wIdG).animate({
                    backgroundColor: '#CDFECD'
                }, 100);
                setTimeout(function() {
                    $('.' + wIdG).animate({
                        backgroundColor: 'white'
                    }, 500);
                }, 500);
            }
        }).fail(function() {
            if( dashboard.debug ) {
                console.log('connection error while setting widget group');
            }
        });
    }

}