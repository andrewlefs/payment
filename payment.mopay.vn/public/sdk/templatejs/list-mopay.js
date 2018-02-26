
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
                            Nạp thông qua cổng thanh toán mopay trên di động\
                          </div>\
                          <div class="msdk_list">';
            jsME.each(arrData.data.data, function() {
                listHTML+='<div class="msdk_item innerFloat" id="item_mopay">\
                <div class="msdk_icon"><img src="'+ self.opts.URL_LIB + 'images/icon-mopay.png" alt="AAA"></div>\
                <div class="msdk_title"><a href="#">'+this.title+'</a></div>\
                <div class="msdk_caret"></div>\
                </div>';
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