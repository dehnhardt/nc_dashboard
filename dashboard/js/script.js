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
            console.log('Start with debugging');
        }

        dashboard.addEnabledWidgets();

        dashboard.initControl();

        $('#widgets').on('hover', '.widget', function () {
            var wIId = $(this).data('wiid');
            dashboard.setHoverWIId(wIId);
        });

        $('#widgets').on('change', '.widget .settings .setting', function () {
            dashboard.setConfig($(this).data('wiid'),$(this).attr('name'), $(this).val(),this);
        });

        $('#widgets').on('click', '.removeWidget', function () {
            var wIId = $(this).data('wiid');
            dashboard.removeWidget(wIId);
        });

    });

})(jQuery, OC);



dashboard = {

    // holds all enabled widgets as array
    // enabled widgets are the widgets,
    // that a user added for its dashboard
    enabledWidgets: [],

    // holds all wIIds that are not yet added
    setToAdd: [],

    // if the cursor hovers a widget, the hovered wIId is set here
    hoverWIId: null,

    // array of interval for refreshing the widgets
    refreshIntervals: [],

    // callback that will be called after a widget refresh
    widgetCallback: [],

    // switch on or off the debug messages
    debug: true,


    // load available widgets
    loadAvailableWidgets: function () {
        if( dashboard.debug ) {
            console.log('start to load available widgets');
        }
        var url     = OC.generateUrl('/apps/dashboard/widget/management/available',[]);
        $.ajax({
            url: url,
            method: 'GET'
        }).done(function (wIds) {
            if( dashboard.debug ) {
                console.log($(wIds));
                console.log('available widgets: ' + wIds.length);
            }
            var wId;
            for(var i = 0; i < wIds.length; i++) {
                wId = wIds[i];
                dashboard.addWidgetToWidgetchoice(wId);
            }

            $('.widgetChoice').on('click', '.widget', function () {
                var wId = $(this).data('wid');
                dashboard.addNewWidget(wId);
            });

        }).fail(function () {
            alert('Could not load available widgets.');
        });
    },

    addWidgetToWidgetchoice: function (wId) {
        var url     = OC.generateUrl('/apps/dashboard/widget/management/basicConf/' + wId,[]);
        $.ajax({
            url: url,
            method: 'GET'
        }).done(function (basicValues) {
            if( dashboard.debug ) {
                console.log($(basicValues));
            }
            
            var html = '<div class="widget" data-wid="' + basicValues['wId'] + '">' +
                '<h2>' + basicValues['name'] + '</h2>' +
                '<div class="icon">' +
                '<img src="' + basicValues['icon'] + '" alt="' + basicValues['wId'] + ' icon">' +
                '</div>' +
                '</div>';
            $('.widgetChoice').append(html);

        }).fail(function() {
            alert('Could not load basic values.');
        });
    },

    // set or unset wIId from hovered widget
    setHoverWIId: function(wIId) {
        if( dashboard.hoverWIId == wIId ) {
            dashboard.hoverWIId = null;
        } else {
            dashboard.hoverWIId = wIId;
        }
        dashboard.hideOrShowWidgetInformation();
    },

    // removes a widget
    removeWidget: function (wIId) {
        if( dashboard.debug ) {
            console.log('remove widget: ' + wIId);
        }
        dashboard.showWaitSymbol();
        var url = OC.generateUrl('/apps/dashboard/widget/management/remove/' + wIId);
        var data = {
            wIId: wIId
        };

        $.ajax({
            url: url,
            method: 'DELETE',
            data: data
        }).done(function() {
            $('#widgets .widget.' + wIId).fadeOut();
        });
        dashboard.hideWaitSymbol();
    },

    // on-mouse-over a widget, show all div with class hoverInfo
    // on-mouse-out hide the divs
    // you can set data-opacityhover to a numeric value
    // you can set data-opacitynormal to a numeric value
    hideOrShowWidgetInformation: function () {
        if( dashboard.hoverWIId == null) {
            var opacity;
            $('#widgets .widget .hoverInfo').each( function () {
                if( typeof $(this).data('opacitynormal') === 'undefined' ) {
                    opacity = jQuery.parseJSON( '{ "opacity": "0"}' );
                } else {
                    opacity = jQuery.parseJSON( '{ "opacity": "' + $(this).data('opacitynormal') + '"}' );
                }

                $(this).animate(
                    opacity,
                    100
                );
            });
        } else {
            $('#widgets .widget.' + dashboard.hoverWIId + ' .hoverInfo').each( function () {
                var opacity;
                if( typeof $(this).data('opacityhover') === 'undefined' ) {
                    opacity = jQuery.parseJSON( '{ "opacity": "1"}' );
                } else {
                    opacity = jQuery.parseJSON( '{ "opacity": "' + $(this).data('opacityhover') + '"}' );
                }

                $(this).animate(
                    opacity,
                    100
                );
            });
        }
    },

    // get enabled widgets as array
    addEnabledWidgets: function () {
        if( dashboard.debug ) {
            console.log('start method addEnabledWidgets');
        }
        dashboard.showWaitSymbol();

        var url = OC.generateUrl('/apps/dashboard/widget/management/enabled',[]);
        if( dashboard.debug ) {
            console.log('ajax get: ' + url);
        }
        $.ajax({
            url: url,
            method: 'GET'
        }).done(function (r) {
            if( dashboard.debug ) {
                console.log('enabled widgets - response: ' + $(r));
                console.log($(r));
            }
            dashboard.enabledWidgets = r;
            if(dashboard.enabledWidgets.length == 0) {
                dashboard.hideWaitSymbol();
                dashboard.showOverlay();
            } else {
                dashboard.setToAdd = dashboard.enabledWidgets;
                dashboard.addOneFromSetToAdd();
            }
        }).fail(function () {
            if( dashboard.debug ) {
                console.log('Could not load enabled widget instances');
            }
        });
    },

    // add the first index wId from setToAdd array
    addOneFromSetToAdd: function() {
        if( dashboard.debug ) {
            console.log('start method addOneFromSetToAdd');
        }

        if( dashboard.debug ) {
            console.log('number of remaining widget instances: ' + dashboard.setToAdd.length);
        }

        if( dashboard.setToAdd.length != 0 ) {
            dashboard.addCompleteWidget(dashboard.setToAdd[0], dashboard.addOneFromSetToAdd);
            dashboard.setToAdd.splice(0,1);
        } else {
            dashboard.hideWaitSymbol();
        }
    },

    // fetch the html from a enabled widget and append it
    addCompleteWidget: function (wIId, callback) {
        if( dashboard.debug ) {
            console.log('start method addCompleteWidget');
        }

        dashboard.showWaitSymbol();
        var url = OC.generateUrl('/apps/dashboard/widget/content/getComplete/' + wIId, []);
        if( dashboard.debug ) {
            console.log('ajax get: ' + url);
        }

        var data = {
            wIId: wIId
        };

        $.ajax({
            url:        url,
            method:     'GET',
            data:       data
        }).done(function (response) {
            if( dashboard.debug ) {
                console.log('response: ' + $(response));
            }

            // complete widget html; append it
            var html =  '<div class="widget ' + response.wId + ' ' + wIId + ' status-' + response.status + ' dimension-' + response.dimension + '" data-refresh="' + response.refresh + '" data-wiid="' + wIId + '" data-mode="content">' +
                response.widgetHtml +
                '</div>';
            $('#widgets').append( html );

            // bind event for widget content reloading
            $('#widgets .widget.' + wIId + ' .heading h1 span.iconReload').on('click', function () {
                $(this).removeClass('icon-play');
                $(this).addClass('icon-loading-small');
                dashboard.refreshWidget(wIId);
            });

            // bind event for showing settings
            $('#widgets .widget.' + wIId + ' .heading h1 span.iconSettings').on('click', function () {
                if( $( '#widgets .widget.' + wIId ).data('mode') == 'content' ) {
                    $('#widgets .widget.' + wIId + ' .content').fadeOut();
                    $('#widgets .widget.' + wIId + ' .settings').fadeIn();
                    $('#widgets .widget.' + wIId).data('mode', 'settings');
                } else {
                    $('#widgets .widget.' + wIId + ' .content').fadeIn();
                    $('#widgets .widget.' + wIId + ' .settings').fadeOut();
                    $('#widgets .widget.' + wIId).data('mode', 'content');
                }
            });

            // create a timer for reloading if necessary
            var refresh = response.refresh * 1000;
            if( refresh != 0 ) {
                dashboard.refreshIntervals[wIId] = setInterval(
                    function() {
                        if(dashboard.hoverWIId != wIId) {
                            dashboard.refreshWidget(wIId);
                        }
                    },
                    refresh
                );
            }
            dashboard.hideWaitSymbol();

            if( callback ) {
                callback();
            }

            dashboard.hideOrShowWidgetInformation();

            var split = wIId.split('-');
            var wId   = split[0];
            if( dashboard.widgetCallback[wId] !== undefined ) {
                dashboard.widgetCallback[wId]();
            }

        }).fail(function () {
            //alert('Could not load complete widget.');
            if( dashboard.debug ) {
                console.log('Clould not load complete widget.');
            }
            dashboard.setWidgetStatus(wIId, 3);
        });
    },

    // fetch the content-html and change the actual one
    refreshWidget: function (wIId) {
        if( dashboard.debug ) {
            console.log('start method refreshWidget (wIId = ' + wIId + ')');
        }

        dashboard.showWaitSymbol();
        var url = OC.generateUrl('/apps/dashboard/widget/content/getContent/' + wIId, []);
        if( dashboard.debug ) {
            console.log('ajax get: ' + url);
        }

        var data = {
            wIId: wIId
        };
        $.ajax({
            url:        url,
            method:     'GET',
            data:       data
        }).done(function (response) {
            if( dashboard.debug ) {
                console.log('response html: ' + response.widgetHtml);
            }

            dashboard.setWidgetStatus(wIId, response.status);
            $('#widgets .widget.' + wIId + ' .content').html( response.widgetHtml );
            $('#widgets .widget.' + wIId + ' .heading h1 span.iconReload').removeClass('icon-loading-small');
            $('#widgets .widget.' + wIId + ' .heading h1 span.iconReload').addClass('icon-play');
            dashboard.hideOrShowWidgetInformation();

            var split = wIId.split('-');
            var wId   = split[0];
            if(  wId in dashboard.widgetCallback && typeof dashboard.widgetCallback[wId] == 'function' ) {
                dashboard.widgetCallback[wId]();
            }
            dashboard.hideWaitSymbol();
        }).fail(function () {
            //alert('Could not refresh widget.');
            if( dashboard.debug ) {
                console.log('Clould not refresh widget.');
            }
            dashboard.setWidgetStatus(wIId, 3);
            dashboard.hideWaitSymbol();
        });
    },

    // call a specific method in the widgetController
    callWidgetMethod: function (wIId, method, value) {
        if( dashboard.debug ) {
            console.log('call widget method: ' + wIId + ' - ' + method + ' - ' + value);
        }
        var url = OC.generateUrl('/apps/dashboard/widget/content/callMethod');
        var data = {
            wIId: wIId,
            method: method,
            value: value
        };
        return $.ajax({
            url:        url,
            method:     'POST',
            data:       data
        });
    },

    // change the css class depending by the status for the widget
    setWidgetStatus: function (wIId, status) {
        $('#widgets .widget.' + wIId).removeClass('status-0');
        $('#widgets .widget.' + wIId).removeClass('status-1');
        $('#widgets .widget.' + wIId).removeClass('status-2');
        $('#widgets .widget.' + wIId).removeClass('status-3');
        $('#widgets .widget.' + wIId).addClass('status-' + status );
    },

    // change the css class to the new dimension
    setWidgetDimension: function(wIId, dimension) {
        $('#widgets .widget.' + wIId).removeClass('dimension-1x1');
        $('#widgets .widget.' + wIId).removeClass('dimension-1x2');
        $('#widgets .widget.' + wIId).removeClass('dimension-1x3');
        $('#widgets .widget.' + wIId).removeClass('dimension-2x1');
        $('#widgets .widget.' + wIId).removeClass('dimension-2x2');
        $('#widgets .widget.' + wIId).removeClass('dimension-2x3');
        $('#widgets .widget.' + wIId).removeClass('dimension-3x1');
        $('#widgets .widget.' + wIId).removeClass('dimension-3x2');
        $('#widgets .widget.' + wIId).removeClass('dimension-3x3');
        $('#widgets .widget.' + wIId).addClass('dimension-' + dimension );
    },

    // save a config value in the db
    setConfig: function (wIId, key, value, e) {
        var url = OC.generateUrl('/apps/dashboard/widget/settings/setConfig');
        var data = {
            wIId: wIId,
            key: key,
            value: value
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: data
        }).done(function () {
            $(e).animate({
                backgroundColor: '#CDFECD'
            }, 100);
            setTimeout(function() {
                $(e).animate({
                    backgroundColor: 'white'
                }, 500);
            }, 500);
            if( key == 'dimension' ) {
                dashboard.setWidgetDimension(wIId, value);
            }
        });
    },

    // set bindings for the widget control center
    initControl: function () {
        dashboard.loadAvailableWidgets();
        $('.controlPlus').on('click', dashboard.showOverlay);
        $('.overlay').on('click', dashboard.hideOverlay);
        $('.overlayArea .close').on('click', dashboard.hideOverlay);
    },

    showOverlay: function () {
        $('.overlayArea').fadeIn();
        $('.overlay').fadeIn();
    },

    hideOverlay: function () {
        $('.overlayArea').fadeOut();
        $('.overlay').fadeOut();
    },

    addNewWidget: function (wId) {
        dashboard.showWaitSymbol();
        dashboard.hideOverlay();

        var url = OC.generateUrl('/apps/dashboard/widget/management/add/' + wId, []);
        var data = {
            wId: wId
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: data
        }).done(function (response) {
            if( dashboard.debug ) {
                console.log('Add widget with wIId: ' + response.wIId);
            }
            dashboard.addCompleteWidget(response.wIId);
        });
    },

    hideWaitSymbol: function() {
        $('#app-content .wait').fadeOut();
    },

    showWaitSymbol: function() {
        $('#app-content .wait').fadeIn();
    },

    testShowEnabledWidgets: function() {
        dashboard.enabledWidgets.forEach(function(item){
            alert(item);
        });
    },

    testShowAvailableWidgets: function() {
        dashboard.availableWidgets.forEach(function(item){
            alert(item);
        });
    }
}
