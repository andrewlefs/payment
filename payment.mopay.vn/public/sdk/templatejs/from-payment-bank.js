
(function($){
    "use strict";
    function page(self,template) {
        if (!(this instanceof page)) {
            return new page(self,template);
        }
        this.init.call(this, self,template);
        return this;
    }
    page.prototype = {
        init: function (self,template) {
            var selfPage = this;
            // this.data = self;
            this.template = template;

            if(!!self.data){
                var arrItemPay_detail =self.data;
                 var price   =arrItemPay_detail.options.prices
            }
            var listHTML='<div class="msdk_form">';
            listHTML+='\
                            <div class="msdk_input_section">\
                                <div class="msdk_input_cover">\
                                    <span class="msdk_arrow_down pointer"><img src="'+ self.opts.URL_LIB + 'images/icon-arrow-down.png" alt="AAA"></span>\
                                    <input placeholder="Chọn số tiền" readonly text="text" name="aaa" class="msdk_input_text_chooser pointer">\
                                 </div>\
                            </div>';
                     listHTML+='<div id="us_bank" class="msdk_dropdown_list ds">\
                                <div class="msdk_outer">';
                                if(!!price) {
                                    jsME.each(price, function (key) {
                                        listHTML += '<a href="#" class="msdk_item" data-cost="' + this.message + '">' + this.description + '</a>';
                                    });
                                }
                    listHTML+='</div>\
                            </div>\
                            <div>\
                            <input type="hidden" value="" id="bitcoid"> \
                            <input type="hidden" value="'+arrItemPay_detail.options.type+'" id="bank_type"> \
                            <input type="hidden" value="'+self.opts.g_direct+'" id="direct_type"> \
                            <input type="hidden" value="'+arrItemPay_detail.options.code+'" id="bank_code"> \
                                <input type="submit" value="Thanh toán" class="msdk_btn msdk_btn_orange input-bank">\
                            </div>\
                       ';
            listHTML+='</div>';
            this.mainContain=selfPage.template.header+
            selfPage.template.maincontaintHead+
            listHTML+
            selfPage.template.maincontaintFoot+
            selfPage.template.footer;

        }
    }


    window.page = page;
})(jQuery);