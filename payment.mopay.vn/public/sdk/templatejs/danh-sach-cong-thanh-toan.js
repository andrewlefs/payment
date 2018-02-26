
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


            var listHTML='<div class="msdk_msg_center msdk_msg_top text-center" id="cahe">\
                             Xin mời chọn phương thức nạp\
                            </div>';
            if(!!self.data) {
                var arrData = self.data;
                listHTML += '<div class="msdk_list">';
                jsME.each(arrData.data.data, function (key) {
                    listHTML += '<div class="msdk_item innerFloat" id="item_list_payment" gdata=' + key + ' g-identify="' + this.identify + '" g-action-root="' + this.identify + '"  g-action="' + this.action + '">';
                    listHTML += '<div class="msdk_icon"><img src="' + this.icon + '" alt="' + this.title + '" height="30px" width="40px"></div>';
                    listHTML += '<div class="msdk_title"><a href="#">' + this.title + '</a></div>';
                    listHTML += '<div class="msdk_caret"></div>';
                    listHTML += '</div>';
                });
                listHTML += '</div>';
            }
            this.mainContain=selfPage.template.header+
            selfPage.template.maincontaintHead+
            listHTML+
            selfPage.template.maincontaintFoot+
            selfPage.template.footer;

        }
    }


    window.page = page;
})(jQuery);