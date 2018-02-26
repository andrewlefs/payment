
(function($){
    "use strict";
    function page(data,template) {
        if (!(this instanceof page)) {
            return new page(data,template);
        }
        this.init.call(this, data,template);
        return this;
    }
    page.prototype = {
        init: function (data,template) {
            var selfPage = this;
            this.data = data;
            this.template = template;
            this.mainContain=selfPage.template.header+
                            selfPage.template.maincontaintHead+
                            '<table class="table table-bordered text-center">\
							<tr class="active bold default">\
								<td>zzz</td>\
								<td>(Tiền game)</td>\
							</tr>\
							<tr>\
								<td>10,000</td>\
								<td>10</td>\
							</tr>\
							<tr>\
								<td>20,000</td>\
								<td>30</td>\
							</tr>\
							<tr>\
								<td>50,000</td>\
								<td>80</td>\
							</tr>\
							<tr>\
								<td>100,000</td>\
								<td>120</td>\
							</tr>\
							<tr>\
								<td>150,000</td>\
								<td>190</td>\
							</tr>\
						</table>'+
                selfPage.template.maincontaintFoot+
                selfPage.template.footer;

        }
    }


window.page = page;
})(jQuery);