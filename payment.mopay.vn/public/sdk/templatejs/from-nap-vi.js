
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

            var listHTML='<div class="msdk_msg_center msdk_msg_top text-center">\
                             Xin mời chọn phương thức nạp\
                            </div>\
                            <div class="msdk_list">';
            jsME.each(arrData.data.data, function(key) {
                if(this.identify!='mopay_wallet_category'){
                listHTML+='<div class="msdk_item innerFloat" id="item_list_payment_wallet" gdata='+key+' g-identify="'+this.identify+'" g-action-root="mopay_wallet_category">';
                listHTML+='<div class="msdk_icon"><img src="'+this.icon+'" alt="'+this.title+'" height="30px" width="40px"></div>';
                listHTML+='<div class="msdk_title"><a href="#">'+this.title+'</a></div>';
                listHTML+='<div class="msdk_caret"></div>';
                listHTML+='</div>';
                }
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