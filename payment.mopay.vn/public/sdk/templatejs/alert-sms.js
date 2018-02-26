
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

            if(!!self.datasms && !!self.datasms.code){
                var arrData = self.datasms;

            }

            var listHTML='<div class="msdk_form">';
            listHTML+='\
                            <div class="msdk_input_section">\
                                <div class="msdk_input_cover">\
                                    <span class="msdk_arrow_down pointer"><img src="'+ self.opts.URL_LIB + 'images/icon-arrow-down.png" alt="AAA"></span>\
                                    <input placeholder="Chọn số tiền" readonly text="text" name="aaa" class="msdk_input_text_chooser pointer">\
                                 </div>\
                            </div>\
                            <div id="us_bank" class="msdk_dropdown_list ds">\
                                <div class="msdk_outer">';

            listHTML+='</div>\
                            </div>\
                            <div>\
                            <input type="hidden" value="" id="bitcoid"> \
                                <input type="submit" value="Thanh toán" class="msdk_btn msdk_btn_orange input-bank">\
                            </div>\
                       ';
            listHTML+='</div>';
            var listHTML_popup='';
            if(!!self.datasms && !!arrData.code){
                var message =   "Thất bại";
                var bttname =   'Đóng';
                var callback    = self.opts.eventBack;
                var link='';
                if(arrData.code==110){
                    message=    'Nạp thành công';
                    bttname =   'Đóng';
                    link =  '<p>Soạn tin nhắn theo cú pháp sau: </p>' +
                    '<p style="font-weight: bold">'+arrData.data.content+'</p><p>Gửi đến</p>' +
                    '<p> <b>'+arrData.data.phone+'</b></p>';

                }else{
                    link ='Lỗi';
                }
                listHTML_popup='<div id="msdk_myModal_confirm" class="pop_confirm">\
                            <div class="msdk_innerModal text-center">\
                                <div class="msdk_heading"></div>';
                listHTML_popup+=' <div class="msdk_msg"  style="font-size: 15px;">\
                                   '+link+'\
                                </div>'
                listHTML_popup+='<div class="row ds">\
                                    <table>\
                                    <tr>\
                                    <td><a href="#" call-data="'+callback+'" class="callBack msdk_accept ib">'+bttname+'</a></td>\
                                    </tr>\
                                    </table>\
                                </div>\
                            </div>\
                        </div>';
            }

            this.mainContain=selfPage.template.header+
            selfPage.template.maincontaintHead+

            selfPage.template.maincontaintFoot+
            selfPage.template.footer+
            listHTML_popup;

        }
    }


    window.page = page;
})(jQuery);