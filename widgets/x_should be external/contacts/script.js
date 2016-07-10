
$(document).ready(function () {

    $('#widgets').on('change', '.widget.contacts .contactSearch', function() {
        var value   = $(this).val();
        var wIId    = $(this).data('wiid');
        var posting = dashboard.callWidgetMethod(wIId, 'getContacts', value);
        posting.success(function(res) {
            var contacts = jQuery.parseJSON(res.success);
            var html     = '';
            $('#widgets .widget.contacts .contactSearchResults').html('');
            for( var i = 0; i <= contacts.length; i++) {
                if(contacts[i] != undefined) {

                    html = '<div class="contact" data-contactid="' + contacts[i]['id'] + '"><div class="name">' + contacts[i]['fn'] + '</div>';
                    var n   = 0;
                    for( n = 0; n < contacts[i]['mail'].length; n++) {
                        if(contacts[i]['mail'][n] != undefined) {
                            html += '<span class="icon-mail">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><a href="mailto:' + contacts[i]['mail'][n] + '">' + contacts[i]['mail'][n] + '</a><br>';
                        }
                    }
                    for( n = 0; n < contacts[i]['phone'].length; n++) {
                        if(contacts[i]['phone'][n] != undefined) {
                            html += '<span class="icon-sound">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' + contacts[i]['phone'][n] + '<br>';
                        }
                    }
                    html = html + '</div>';
                    $('#widgets .widget.contacts .contactSearchResults').append(html);
                }
            }
        });
        posting.error(function() {
            dashboard.setWidgetStatus(wIId, 3);
        });

    });
});