$(document).ready(function() {
    ACTION.init();
    var card_chose;
    var bank_chose;
    $('.close').click(function() {
        location.reload();
    });
    $('.button-close').click(function() {
        location.reload();
    })
    
    
    
    $('img.card_icon').click(function() {
        $(".radio-card span").removeClass('radio-checked');
        $(this).next().children().addClass('radio-checked');
        card_chose = $(this).next().attr("data");
        $('.radio').val(card_chose);
        $(".card_element").removeClass('active');
        $(this).parent().addClass('active');
    })
    $('div.radio-card').click(function() {
        $(".radio-card span").removeClass('radio-checked');
        $(this).children().addClass('radio-checked');
        card_chose = $(this).attr("data");
        $('.radio').val(card_chose);
    });
    $(".bank_element img").click(function() {
        $(".radio-card span").removeClass('radio-checked');
        $(this).next().children().addClass('radio-checked');
        bank_chose = $(this).next().attr("data");
        $('.bank_code').val(bank_chose);
        $(".bank_element").removeClass('active');
        $(this).parent().addClass('active');
    });
    $('.ajax-content').hide();
    
});

var arrCat = [];
var ACTION = {
    OBJ_GRID: null,
    init: function() {
        $('.loading').hide();
        data = '';
        $('.card-form').submit(function(e) {
            e.preventDefault();
            $data = $(this).serializeArray();
            ACTION.SendAjax('./payment_card', $data, $(this))
        });
        $('.bank-form').submit(function(e) {
            e.preventDefault();
            $data = $(this).serializeArray();
            ACTION.SendAjax('./payment_banking', $data, $(this))
        });
        $('.form_withdraw').submit(function(e) {
            e.preventDefault();
            $data = $(this).serialize();
            ACTION.SendAjaxWithdraw('./payment_withdraw', $data, $(this))
        });

        $('.step-2').hide();
        $('.step-2-bank').hide();
        $('img.card_icon , div.radio-card').click(function() {
            $('.step-2').slideDown();
        })
        $('.bank_element img , div.radio_bank').click(function() {
            $('.step-2-bank').slideDown();
        })
        $('.generate').click(function() {
            ACTION.ReLoadCaptcha();
        });

    },
    SendAjax: function(url, data, element) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            data: data,
            beforeSend: ACTION.startLoading,
            complete: ACTION.topLoading,
        }).done(function(response) {

            if (response.code == 1000) {
                if (response.message == 'PAYMENT_BANK_SUCCESS') {
                    window.location.href = response.data.link;
                } else {
                    $('.step-2').slideUp();
                    element.trigger("reset");
                    $('.background_shadow').fadeIn();
                    $('.money').html(response.data.money);
                    ACTION.number_format('.money');
                    $('.balance_value').html(response.data.balance);
                    ACTION.number_format('.balance_value');
                    ACTION.ReLoadCaptcha();
                }
            } else {
                $('.ajax-content').html(response.message);
                $('.ajax-content').show();
            }
        });

    },
    SendAjaxWithdraw: function(url, data, element) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            data: data,
            beforeSend: ACTION.startLoading,
            complete: ACTION.topLoading,
        }).done(function(response) {

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
                $('.ajax-content').show();
            }
        });

    },
    getListServer: function(game) {
        $.ajax({
            url: '/server_list',
            type: 'GET',
            dataType: 'JSON',
            data: {'game': game},
            beforeSend: ACTION.startLoading,
            complete: ACTION.topLoading,
        }).done(function(response) {
            if (response.code == 1000) {
//                alert(response.code);
                $('#server_select').html('<option selected="selected" value="">Chọn Server</option>');
                $.each(response.data, function(i, val) {
                    $('#server_select').append('<option value="' + val.server_id + '">' + val.server_name + '</option>');
                });

            } else {
                $('.ajax-content').html(response.message);
                $('.ajax-content').show();
            }
        });

    },
    startLoading: function() {
        $('.loading').show();

    },
    topLoading: function() {
        $('.loading').hide();
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
    },
 
}