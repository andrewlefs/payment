
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


            var listHTML='<div class="msdk_list">';
            if(!!self.data){
                var arrData =self.data;
                var arrItemPay =self.data;//.self.opts.id_item_payment
                var identify  =   self.data.identify;
                if(!!arrItemPay.data) {
                    jsME.each(arrItemPay.data, function (key) {
                        var service_number = '';
                        if (this.action == 'pay_sms') {
                            service_number = this.options.phone;
                        }
                        listHTML += '<div class="msdk_item innerFloat" id="item_list_payment_detail" gdata=' + self.opts.id_item_payment + ' gdata-detail=' + key + ' g-identify="' + identify + '" g-action-root="' + self.opts.g_action_root + '"  g-action="' + this.action + '" g-service="' + service_number + '" g-direct="1" >';
                        listHTML += '<div class="msdk_icon"><img src="' + this.icon + '" alt="' + this.title + '" height="30px" width="40px"></div>';
                        listHTML += '<div class="msdk_title"><a href="#">' + this.title + '</a></div>';
                        listHTML += '<div class="msdk_caret"></div>';
                        listHTML += '</div>';
                    });
                }
            }
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