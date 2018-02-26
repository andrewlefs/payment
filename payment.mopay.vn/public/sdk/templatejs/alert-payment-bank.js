
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

            if(!!self.databank && !!self.databank.code){
                var arrData = self.databank;

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
            if(!!self.databank && !!arrData.code){
                var message =   "Thất bại";
                var bttname =   'Nạp lại';
                var callback    = 'closeconfirim';
                var link='';
                if(arrData.code==110){
                    message=    'Nạp thành công';
                    bttname =   'Đóng';
                    link =   '<a href="'+arrData.data.link+'" target="_blank">Click vào đây để tiếp tục thanh toán</a>';
                    callback    =  self.opts.eventBack;
                   /* var url = arrData.data.link;
                    setTimeout(function() {
                        window.open(url);
                    var form = document.createElement("form");
                    form.method = "GET";
                    form.action = url;
                    form.target = "_blank";
                    document.body.appendChild(form);
                    //form.submit();
                    },200);
                    return false;*/
                }else{
                    link =arrData.data.message;
                }
                listHTML_popup='<div id="msdk_myModal_confirm" class="pop_confirm">\
                            <div class="msdk_innerModal text-center">\
                                <div class="msdk_heading"></div>';
                listHTML_popup+=' <div class="msdk_msg">\
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