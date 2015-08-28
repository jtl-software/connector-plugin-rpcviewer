var entries = [];
var editor;
var ajax;
var timer = null;

function getContent(timestamp, pointer) {
    var queryString = {
        'timestamp' : timestamp,
        'pointer': pointer,
        'action': 'run'
    };

    ajax = $.ajax({
        type: 'GET',
        url: 'server.php',
        data: queryString,
        success: function(data){
            $('#entries li.active').stop().css('background-color','#272822').removeClass('active');

            $.each(data.data, function(index, el) {
                var color = '';
                var icon = '';
                var cls = '';
                var results = '';

                switch(el.label) {
                    case 'Result':
                        color = 'label-success';
                        icon = 'glyphicon-open';
                        if(el.data) {
                            results = el.data.length === undefined ? '' : ' <span class="badge">' + el.data.length + '</span>';
                        }
                        break;
                    case 'Error':
                        color = 'label-danger';
                        icon = 'glyphicon-alert';
                        break;
                    default:
                        color = 'label-primary';
                        cls = 'request';

                        if (el.label.indexOf('pull') > -1) {
                            icon = 'glyphicon-arrow-left';
                        } else if (el.label.indexOf('push') > -1) {
                            icon = 'glyphicon-arrow-right';
                        } else if (el.label.indexOf('delete') > -1) {
                            icon = 'glyphicon-trash';
                        } else if (el.label.indexOf('statistic') > -1) {
                            icon = 'glyphicon-stats';
                        } else if (el.label.indexOf('auth') > -1) {
                            icon = 'glyphicon-lock';
                        } else if (el.label.indexOf('identify') > -1) {
                            icon = 'glyphicon-info-sign';
                        } else if (el.label.indexOf('clear') > -1) {
                            icon = 'glyphicon-refresh';
                        } else if (el.label.indexOf('features') > -1) {
                            icon = 'glyphicon glyphicon-cog';
                        } else if (el.label.indexOf('init') > -1) {
                            icon = 'glyphicon glyphicon-flash';
                        } else if (el.label.indexOf('ack') > -1) {
                            icon = 'glyphicon glyphicon-ok';
                        }
                        break;
                }

                var iconStr = '<span class="label '+color+'"><i class="glyphicon '+icon+'"></i></span>&nbsp;&nbsp;';
                var entry = $('<li class="'+cls+'"><a href="#">'+iconStr+el.label+results+'<span class="label label-default pull-right">'+el.timestamp+'</span></a></li>').hide();

                entries.unshift(el);

                $('#entries').prepend(entry);

                entry.slideDown().animate({
                    'background-color': '#272822'
                }, 3000);
            });

            entries = entries.slice(0,100);

            $('#entries').find("li").slice(100).remove();

            if (timer) {
                clearTimeout(timer);
                timer = null;
            }

            timer = setTimeout(ready, 2500);

            getContent(data.timestamp, data.pointer);
        }
    });
}

function ready() {
    if (entries.length > 0) {
        $('#entries li:first-child a').click();
    }
}

$(function() {
    editor = ace.edit("view");
    editor.getSession().setMode("ace/mode/json");
    editor.setTheme("ace/theme/monokai");
    editor.setReadOnly(true);
    editor.setShowPrintMargin(false);
    editor.$blockScrolling = Infinity;

    $('#entries').on('click', 'a', function(e) {
        $('#entries li.active').stop().css('background-color','#272822').removeClass('active');

        editor.setValue(JSON.stringify(entries[$(this).parent().index()].data, null, '\t'), -1);

        $(this).parent().stop().animate({
           'background-color': '#111111'
        }, 500).addClass('active');
    });

    $('#startBtn').click(function() {
        if(ajax) {
            ajax.abort();
        }

        $('#entries').empty();
        entries = [];

        getContent(0, 0);
    });

    $('#resetBtn').click(function() {
        editor.setValue('');

        ajax = $.ajax({
            type: 'GET',
            url: 'server.php',
            data: {'action': 'reset'},
            success: function(data) {
                $('#startBtn').click();
            }
        });
    });
});
