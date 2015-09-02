var entries = [];
var editor;
var ajax;
var timer = null;

var actionIcons = {
    pull: 'glyphicon-arrow-left',
    push: 'glyphicon-arrow-right',
    delete: 'glyphicon-trash',
    statistic: 'glyphicon-stats',
    auth: 'glyphicon-user',
    identify: 'glyphicon-info-sign',
    clear: 'glyphicon-refresh',
    features: 'glyphicon glyphicon-cog',
    init: 'glyphicon glyphicon-flash',
    ack: 'glyphicon glyphicon-ok'
};

var controllerIcons = {
    connector: 'glyphicon-transfer',
    customer: 'glyphicon-user',
    product: 'glyphicon-th-large',
    category: 'glyphicon-folder-open',
    customer_order: 'glyphicon-shopping-cart',
    delivery_note: 'glyphicon-file',
    global_data: 'glyphicon-asterisk',
    image: 'glyphicon-picture',
    manufacturer: 'glyphicon-tags',
    status_change: 'glyphicon-flag',
    product_stock_level: 'glyphicon-inbox',
    product_price: 'glyphicon-euro',
    payment: 'glyphicon-briefcase',
    cross_selling: 'glyphicon-random',
    specific: 'glyphicon-align-left'
};

function getContent(timestamp, pointer) {
    var queryString = {
        'timestamp' : timestamp,
        'pointer': pointer,
        'action': 'run'
    };

    ajax = $.ajax({
        type: 'GET',
        url: 'api.php',
        data: queryString,
        success: function(data){
            $('#entries li.active').stop().css('background-color','#272822').removeClass('active');

            $.each(data.data, function(index, el) {
                var color = '';
                var icon = '';
                var ctrIcon = 'glyphicon glyphicon-unchecked';
                var results = '';

                if(actionIcons[el.action]) {
                    icon = actionIcons[el.action];
                }

                if(controllerIcons[el.controller]) {
                    ctrIcon = controllerIcons[el.controller];
                }

                switch(el.type) {
                    case 'result':
                        color = 'text-success';

                        if(el.data.result) {
                            results = el.data.result.length === undefined ? '' : '<span class="badge bg-info pull-right">' + el.data.result.length + '</span>';

                            if(el.data.result.available) {
                                results = el.data.result.available === undefined ? '' : '<span class="badge bg-info pull-right">' + el.data.result.available + '</span>';
                            }
                        }

                        if(el.data.error) {
                            color = 'text-danger';
                            icon = 'glyphicon-alert';
                        }

                        break;
                    case 'request':
                        if (el.action.match(/(pull|push|delete|statistic|ack)/)) {
                            color = 'text-primary';
                        }

                        break;
                }

                var iconStr = '<span class="'+color+'"><i class="glyphicon '+icon+'"></i></span>&nbsp;&nbsp;';
                var ctrIconStr = '<span class="glyphicon '+ctrIcon+'"></span>&nbsp;&nbsp;';
                var entry = $('<li class="'+el.type+'"><a href="#">'+iconStr+'<b class="text-uppercase text-muted">'+ctrIconStr+el.controller+'</b>'+' <span class="glyphicon glyphicon-menu-right text-muted"></span> <span class="text-capitalize">'+el.action+'</span><span class="label label-default pull-right">'+el.timestamp+'</span>'+results+'</a></li>').hide();

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

        //$('#entries').empty();
        //entries = [];

        getContent(0, 0);

        $('#startBtn,#clearBtn').attr('disabled', 'disabled');
        $('#stopBtn').attr('disabled', null);
        $('#active').fadeIn();
    });

    $('#stopBtn').click(function() {
        if(ajax) {
            ajax.abort();
        }

        ajax = $.ajax({
            type: 'GET',
            url: 'api.php',
            data: {'action': 'clear'},
            success: function(data) {
                $('#stopBtn').attr('disabled', 'disabled');
                $('#startBtn,#clearBtn').attr('disabled', null);
                $('#active').fadeOut();
            }
        });
    });

    $('#clearBtn').click(function() {
        editor.setValue('');

        ajax = $.ajax({
            type: 'GET',
            url: 'api.php',
            data: {'action': 'clear'},
            success: function(data) {
                $('#entries').empty();
                entries = [];
            }
        });
    });

    $('#stopBtn').click();
});
