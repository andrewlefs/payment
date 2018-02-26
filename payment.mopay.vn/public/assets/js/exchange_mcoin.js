$(document).ready(function() {
    ACTION.init();
    $('.close').click(function() {
        location.reload();
    });
    $('.button-close').click(function() {
        location.reload();
    })

});

var arrCat = [];
var ACTION = {
    OBJ_GRID: null,
    init: function() {
        $('.loading').hide();
        data = '';
        //Chọn game
        value_game = $('#game_text').attr('title');
        if (value_game != '') {
            ACTION.getListServer(value_game);
        }
        ;

        //Chọn Server -> Tên Nhân Vật
        $('#server_select').change(function() {
            server = $(this).val();
            if (server != '') {
                ACTION.getListCharacter(server, value_game);
            } else {
                $('#char_select').html('');
            }
        });

        $('.generate').click(function() {
            ACTION.ReLoadCaptcha();
        });

        $('.form_withdraw').submit(function(e) {
            e.preventDefault();
            $data = $(this).serialize();
            ACTION.SendAjaxWithdraw('./payment_withdraw', $data, $(this))
        });

        //WAP
            value_game_wap = $('.select-game').val();
            if (value_game_wap != '' && value_game_wap != undefined ) {
                ACTION.getListServer(value_game_wap);
            };
       
    },
    SendAjaxWithdraw: function(url, data, element) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            data: data,
            beforeSend: ACTION.startLoading('.loadingwithdraw'),
//            complete: ACTION.topLoading('.loading'),
        }).done(function(response) {
            ACTION.topLoading('.loadingwithdraw');
            if (response.code == 1000) {
                element.trigger("reset");
                ACTION.ReLoadCaptcha();
//                $('.ajax-content').html(response.message);
                $('.background_shadow').fadeIn();
                $('.content-popup').html("<div class='text_alert'>Bạn Đã Đổi Mcoin Thành Công</div>");
//                alert(response.message);
//                location.reload();
            } else {
                $('.ajax-content').html(response.message);
            }
        });

    },
    getListServer: function(game) {
        $.ajax({
            url: '/server_list',
            type: 'GET',
            dataType: 'JSON',
            data: {'game': game},
            beforeSend: ACTION.startLoading(".serverloading"),
//            complete: ACTION.topLoading(".serverloading"),
        }).done(function(response) {
            if (response.code == 1000) {
                ACTION.topLoading(".serverloading");
//                alert(response.code);
                $('#server_select').html('<option selected="selected" value="">Chọn Server</option>');
                $.each(response.data, function(i, val) {
                    $('#server_select').append('<option value="' + val.server_id + '">' + val.server_name + '</option>');
                });

            } else {
                $('.ajax-content').html(response.message);
            }
        });
    },
    getListCharacter: function(server_id, game) {
        $.ajax({
            url: '/character_list',
            type: 'GET',
            dataType: 'JSON',
            data: {'server_id': server_id, 'game': game},
            beforeSend: ACTION.startLoading('.characterloading'),
//            complete: ACTION.topLoading('.characterloading'),
        }).done(function(response) {
            ACTION.topLoading('.characterloading')
            if (response.code == 1000) {
//                alert(response.code);
                $('#char_select').html('<option selected="selected" value="">Chọn Nhân Vật</option>');
                $.each(response.data, function(i, val) {
                    $('#char_select').append('<option value="' + val.character_id + '">' + val.character_name + '</option>');
                });

            } else {
                $('.ajax-content').html(response.message);
            }
        });
    },
    startLoading: function($class) {
        $($class).show();

    },
    topLoading: function($class) {
        $($class).hide();
    },
    ReLoadCaptcha: function() {
        $('img.captcha').attr('src', './captcha');
    },
    number_format: function(cla) {
        $(cla).formatCurrency({
            roundToDecimalPlace: 0,
            symbol: '',
            positiveFormat: '%n',
            negativeFormat: '-%n',
            decimalSymbol: ',',
            digitGroupSymbol: '.',
            groupDigits: true
        });
    }
}