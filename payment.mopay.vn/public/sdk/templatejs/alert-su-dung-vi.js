
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
            if(arrData.code==110) {
                var listHTML = '<div class="msdk_msg_center text-center">\
                                <span class="text-upper">CHÚC MỪNG BẠN !!!!</span>\
                                 <br><br>\
                                 <span>Bạn đã dùng <b>'+arrData.data.credit+' mCoin</b> mua thành công <b>vật phẩm</b></span>\
                               </div>\
                            <div>\
                                <a href="#" call-data="' + self.opts.eventBack + '" class="callBack msdk_btn msdk_btn_orange text-center ds">Tiếp tục</a>\
                            </div>';
            }else{
                var listHTML = '<div class="msdk_msg_center text-center">\
                                ' + arrData.data.message + '\
                               </div>\
                            <div>\
                                <a href="#" call-data="' + self.opts.eventBack + '" class="callBack msdk_btn msdk_btn_orange text-center ds">Tiếp tục</a>\
                            </div>';
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