// JavaScript Document
$(document).ready(function(e) {
    var winHei = $(window).height()-30;
    var headerHei = $('.header-sdk').outerHeight();
    var footerHei = $('.footer-sdk').outerHeight();
   function fitToCcreen(){
       console.log(footerHei);
       $('.content-sdk').css({height:winHei - (headerHei+footerHei)});
   }
    fitToCcreen();
    $(window).resize(function(){
        winHei = $(window).height()-30;
        headerHei = $('.header-sdk').outerHeight();
        footerHei = $('.footer-sdk').outerHeight();
        fitToCcreen();
    });
    
    $('.cart-row').click(function(){
        $(this).find('input,select').focus();
        $(this).find('select').first().focus();
    });
    
    $('.empty-input').click(function(){
        $(this).parents('.cart-row').find('input').val('');
    });
    $('.sdk-row').attr('tabindex','-1');
    $('.sdk-row a').click(function(){
        $(this).parents('.sdk-row').focus();
    }); 
});