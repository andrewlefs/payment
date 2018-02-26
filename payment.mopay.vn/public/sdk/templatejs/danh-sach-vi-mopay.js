
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

            var arrData =self.data;
            var arrItemPay =self.data;//.self.opts.id_item_payment
             var balance = self.opts.balance;

            var arrDataBalan    =  balance.data;
            var identify  =   self.data.identify;
            var url= self.opts.URL_LIB + 'images/avatar.png'   ;
            if(arrDataBalan.account.fullname!=''){
                url =arrDataBalan.account.avatar;
            }
            var listHTML='<div class="msdk_userInfo innerFloat">\
                                <div class="msdk_avatar"><img src="'+url+'" alt="'+arrDataBalan.account.fullname+'"></div>\
                                <div class="msdk_info">\
                                    <div class="msdk_name">'+arrDataBalan.account.fullname+'</div>\
                                    <div class="msdk_coin"><strong>'+arrDataBalan.balance+'</strong> mCoin</div>\
                                </div>\
                                <a class="msdk_config" href="#"></a>\
                                <div class="msdk_caret"></div>\
                            </div>\
                    <div class="msdk_list  msdk_list_no_heading">';

            jsME.each(arrItemPay.data, function(key) {
                listHTML+='<div class="msdk_item innerFloat" id="item_list_wallet_detail" gdata-parent=' + self.opts.id_item_wallet + ' gdata='+self.opts.id_item_payment+' gdata-detail='+key+' g-identify="'+this.identify+'"  g-action="'+this.action+'" >';
                listHTML+='<div class="msdk_icon"><img src="'+this.icon+'" alt="'+this.title+'" height="30px" width="40px"></div>';
                listHTML+='<div class="msdk_title"><a href="#">'+this.title+'</a></div>';
                listHTML+='<div class="msdk_caret"></div>';
                listHTML+='</div>';
            });
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