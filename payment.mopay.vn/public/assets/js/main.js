
$(document).ready(function() {
    /*
     * TOP MENU HIGHLIGHT
     */
    $(".nav-content").hide();
    $('.myMenu > li').bind('mouseover', openSubMenu);
    $('.myMenu > li').bind('mouseout', closeSubMenu);

    function openSubMenu() {
        $(this).find('ul').css('visibility', 'visible');
    }
    ;

    function closeSubMenu() {
        $(this).find('ul').css('visibility', 'hidden');
    }
    ;
    $("div.nav").hover(
            function() {
                $('div.nav').addClass("nav-active");
                $('div.image').addClass("nav-title-active");
            },
            function() {
                $('div.nav').removeClass("nav-active");
                $('div.image').removeClass("nav-title-active");
            }
    )
    $('select[name=prices]').change(function() {
        val = $(this).attr('prices');
        alert(val);
    });

    var url = location.pathname;

    var filename = (url.substring(url.lastIndexOf('/') + 1)).replace(".php", "");
    if (filename.indexOf("promotion") == 0) {
        filename = "promotion";
    }
    $('#' + filename).attr('class', 'active');

    /*
     * TABS
     */
    $('.tabs .tab-links a').on('click', function(e) {
        var currentAttrValue = $(this).attr('href');

        // Show/Hide Tabs
        $('.tabs ' + currentAttrValue).show().siblings().hide();

        // Change/remove current tab to active
        $(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });

    $('.tabs-2 .tab-2-links a').on('click', function(e) {
        var currentAttrValue = $(this).attr('href');

        // Show/Hide Tabs
        $('.tabs-2 ' + currentAttrValue).show().siblings().hide();

        // Change/remove current tab to active
        $(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });

    $('.tabs-3 .tab-3-links a').on('click', function(e) {
        var currentAttrValue = $(this).attr('href');
        
        // Show/Hide Tabs
        $('.tabs-3 ' + currentAttrValue).show().siblings().hide();
        
        $('.tabs-3 .tab-3-links li').removeClass('active');
        // Change/remove current tab to active
        $(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });

    /*
     * RADIO BUTTON EVENT
     */
    $('.radio').change(function(e) {
        $('.card_element').attr('class', 'card_element');
        $(this).parent().attr('class', 'card_element active');
    });
    $('.radio_bank').change(function(e) {
        $('.bank_element').attr('class', 'bank_element');
        $(this).parent().attr('class', 'bank_element active');
    });

    /*
     * Date picker
     */
    $('.date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    /*
     * Custom dropdown with image
     */
    $(".dropdown_trigger").click(function() {
        var id = $(this).attr('id');
        var prefix = id.replace("_trigger", "");
        var list = "#" + prefix + "_ul";

        var display = $(list).css('display');
        if (display === "none") {
            $(list).attr('style', 'display:true');
        }
        else
        {
            $(list).attr('style', 'display:none');
        }

    });
    $(".dropdown").click(function() {
        var id = $(this).attr('id');
        var prefix = id.replace("_trigger", "");
        var list = "#" + prefix + "_ul";

        var display = $(list).css('display');
        if (display === "none") {
            $(list).attr('style', 'display:true');
        }
        else
        {
            $(list).attr('style', 'display:none');
        }

    });

    $(".dropdown_item").click(function() {
        var value = $(this).text();
        var text = $(this).parent().attr("id");
        var prefix = text.replace("_ul", "");
        $("#" + prefix + "_text").val(value);
        $("#" + prefix + "_ul").attr('style', 'display:none');

    });

    $(".dropdown_item").mouseover(function() {
        var value = $(this).children("label").text();
        var imagesrc = $(this).children("img").attr("src");
        var bg_image = 'background: url("' + imagesrc + '") no-repeat 10px center';
        // var value = $(this).text();
        var text = $(this).parent().attr("id");
        var prefix = text.replace("_ul", "");
        $("#" + prefix + "_text").val(value);
        $("#" + prefix + "_text").attr('style', bg_image);
    });

    $(".dropdown_trigger").focusout(function() {
        var id = $(this).attr('id');
        var prefix = id.replace("_trigger", "");
        var list = "#" + prefix + "_ul";

        var display = $(list).css('display');
        if (display !== "none") {
            $(list).attr('style', 'display:none');
        }
    });
    $(".dropdown").focusout(function() {
        var id = $(this).attr('id');
        var prefix = id.replace("_trigger", "");
        var list = "#" + prefix + "_ul";

        var display = $(list).css('display');
        if (display !== "none") {
            $(list).attr('style', 'display:none');
        }
    });

    /*
     * DataTable
     */
    //$('#myTable').DataTable();
    $('#recharge_history').DataTable({
        "info": false,
        "searching": false,
        "bSort": true,
        "pagingType": "full_numbers",
        "bFilter": false,
        "bLengthChange": false,
        "bAutoWidth": false,
        "language": {
            "emptyTable": "Không có dữ liệu",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "infoPostFix": "",
            "thousands": ",",
            "loadingRecords": "Đang tải...",
            "processing": "Đang xử lý...",
            "search": "Tìm kiếm:",
            "zeroRecords": "Không có dữ liệu phù hợp",
            "paginate": {
                "first": "Đầu trang",
                "last": "Trang cuối",
                "next": "Tiếp theo",
                "previous": "Trước đó",
                "sPrevious": false,
                "sNext": false
            }
        }
    });

    $('#exchange_history').DataTable({
        "info": false,
        "searching": false,
        "bSort": true,
        "pagingType": "full_numbers",
        "bFilter": false,
        "bLengthChange": false,
        "bAutoWidth": false,
        "language": {
            "emptyTable": "Không có dữ liệu",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "infoPostFix": "",
            "thousands": ",",
            "loadingRecords": "Đang tải...",
            "processing": "Đang xử lý...",
            "search": "Tìm kiếm:",
            "zeroRecords": "Không có dữ liệu phù hợp",
            "paginate": {
                "first": "Đầu trang",
                "last": "Trang cuối",
                "next": "Tiếp theo",
                "previous": "Trước đó",
                "sPrevious": false,
                "sNext": false
            }
        }
    });
    $.ajax({
        url: './get_cross_sale',
        type: 'GET',
        dataType: 'JSON',
        data: '',
    }).done(function(response) {
        $.each(response, function(index, value) {
            $('.nav-sub').append('<li><a href="' + value.link + '"><img src="' + value.icon + '"/><span>' + value.appname + '</span></a></li>');
        });
    });
});


function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}