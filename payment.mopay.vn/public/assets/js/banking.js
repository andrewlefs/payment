$(document).ready(function() {
    ACTION.init();

    $('.ajax-content').hide();
    $('.result-bank-success').hide();

    setTimeout(function() {

        ACTION.verifyBanking();

    }, 2000);

});

var arrCat = [];
var ACTION = {
    OBJ_GRID: null,
    init: function() {
        $('.loading').hide();
    },
    SendAjax: function(url, data, element) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            data: data,
            beforeSend: ACTION.startLoading(),
            error: function() {
                ACTION.topLoading();
                $('.result-bank-success').slideDown();
                $('.result-bank-success').html('<div class="alert-fail"><img src="./assets/images/alert.jpg" width="35px" /><div class="alert-title">Giao Dịch Thất Bại! Vui lòng liên hệ 19006611</div></div>');
            }
        }).done(function(response) {
            ACTION.topLoading();
            if (response.code == 1000) {
                $('.result-bank-success').slideDown();
                $('.result-bank-success .money').html(response.data.money);
                ACTION.number_format('.money');
                $('.result-bank-success .credit_value').html(response.data.credit);
                ACTION.number_format('.credit_value');
                $('.result-bank-success .balance_value').html(response.data.balance);
                ACTION.number_format('.balance_value');
                $('.sidebar-balance .value-real').html(response.data.balance);
                ACTION.number_format('.sidebar-balance .value-real');
            } else {
                if (response.code == 1002) {
//                    window.location.href = "./";
                    $('.result-bank-success').slideDown();
                    $('.result-bank-success').html('<div class="alert-fail"><img src="./assets/images/alert.jpg" width="35px" /><div class="alert-title">Giao Dịch Thất Bại! Vui lòng liên hệ 19006611</div></div>');
                }
            }
        });

    },
    startLoading: function() {

    },
    topLoading: function() {
        $('.loading-banking, .waitting').slideUp();
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
    verifyBanking: function() {
        url = "./verify_banking";
        data = '';
        element = '';
        ACTION.SendAjax(url, data, element);
    }
}