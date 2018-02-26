
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
            var seri  ='';
            var pin  =''; 
            if(!!self.data && !!self.data.code){
                var arrData = self.data;
                 seri    =   self.serial;
                 pin    =   self.pin;
            }
			var listHTML='<div class="msdk_msg_center msdk_msg_top text-center">\
                            Mời bạn nhập thông tin thẻ\
                            </div><div class="msdk_form">';

            listHTML+='\
                <div class="msdk_input_section">\
                    <div class="msdk_input_cover">\
                        <span class="msdk_lbl">Số Seri :</span>\
                        <span class="msdk_clear pointer" id="clear_seri"><img src="'+ self.opts.URL_LIB + 'images/btn-close-circle.png" alt="AAA"></span>\
                        <input text="text" value="'+seri+'"  name="aaa" class="msdk_input_text number_seri">\
                    </div>\
                    <div class="msdk_input_cover">\
                        <span class="msdk_lbl">Mã Pin :</span>\
                        <span class="msdk_clear pointer" id="clear_codepin"><img src="'+ self.opts.URL_LIB + 'images/btn-close-circle.png" alt="AAA"></span>\
                        <input text="text" value="'+pin+'" name="aaa" class="msdk_input_text codepin">\
						 <input type="hidden" value="'+self.opts.telco+'" name="type_telco" id="type_telco" class="msdk_input_text codepin">\
                    </div>\
                </div>\
                <div>\
                 <input type="hidden" value="'+self.opts.g_direct+'" id="direct_type"> \
                      <input type="submit" value="Nạp card" class="msdk_btn msdk_btn_orange input-card">\
                </div>\
            ';
			var listHTML_popup='';
			var message =   "Nạp thất bại";
			var bttname =   'Nạp lại';
			var callback    = 'closeconfirim';
			if(arrData.code==110){
				message=    'Nạp thành công';
				 bttname =   'Đóng';
				callback    =  self.opts.eventBack;
			}
			 listHTML_popup='<div id="msdk_myModal_confirm" class="pop_confirm">\
                            <div class="msdk_innerModal text-center">\
                                <div class="msdk_heading">'+message+'</div>';
            listHTML_popup+=' <div class="msdk_msg">\
                                   '+arrData.data.message+'\
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
				
            listHTML+='</div>';
            this.mainContain=selfPage.template.header+
            selfPage.template.maincontaintHead+
            listHTML+
            selfPage.template.maincontaintFoot+
            selfPage.template.footer+
            listHTML_popup;

        }
    }


    window.page = page;
})(jQuery);