
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

            var arrData =   self.data.data;
            var arrItem =   arrData.items;
          //  var arrItemPay =self.data;//.self.opts.id_item_payment
          //  var identify  =   self.data.identify;
            var listHTML='<div class="msdk_form" >';


                listHTML+='\
                                <div class="msdk_input_section"  id="choose_item">\
                                    <div class="msdk_input_cover">\
                                         <span class="msdk_arrow_down pointer"><img src="'+ self.opts.URL_LIB + 'images/icon-arrow-down.png" alt="AAA"></span>\
                                        <input placeholder="'+arrData.options.place_holder+'" readonly text="text" name="aaa" class="msdk_input_text_chooser pointer">\
                                    </div>\
                                </div>\
                                <div id="us_vimobay" class="msdk_dropdown_list ds">\
                                    <div class="msdk_outer">';
                                   jsME.each(arrItem, function(key) {
                                        listHTML+='<a href="#" class="msdk_item" data-cost="'+this.credit+'" data-id="'+this.itemId+'">'+this.itemName+'</a>';
                                    });

            listHTML+=' </div>\
                                </div>\
                                <div class="msdk_hint text-center">Giao dịch trị giá <strong></strong>\
                               \
                                </div>\
                                <div>\
                                <input type="hidden" value="" id="bitcoid" data-code-id="" > \
                                     <input type="submit" value="Đồng ý mua" class="msdk_btn msdk_btn_orange sd_vimobay">\
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