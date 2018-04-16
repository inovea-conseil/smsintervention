/* SMS Intervention - Send SMS since FICHINTER card
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

$('#smsSendBtn').on('click', function(){
    var sendBtn = $( "#dialog-confirm" ).data('btnSendTxt');
    var cancelBtn = $( "#dialog-confirm" ).data('btnCancelTxt');
    
    $( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: 
      [{
        text: $( "#dialog-confirm" ).attr('data-btnSendTxt'),
        "id": "btnSendSmsOk",
        click: function () {
            $( "#btnSendSmsOk" ).button({
                disabled: true
            });
            ajaxSendSms();
        },

    }, {
        text: $( "#dialog-confirm" ).attr('data-btnCancelTxt'),
        click: function () {
            $( this ).dialog( "close" );
        },
    }],
    });
});

function ajaxSendSms() {
    if (typeof fichinterId != 'undefined') {
        $.ajax({
            type: "POST",
            url: ajaxFile,
            data: {id: fichinterId},
            dataType: 'html'
        }).done(function( data ) {
            location.reload();
        });
    }
}

