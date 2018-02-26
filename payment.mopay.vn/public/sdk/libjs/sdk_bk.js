(function($){
    "use strict";
    function ME(opts) {
        if (!(this instanceof ME)) {
            return new ME(opts);
        }
        this.reset.call(this);
        this.init.call(this, opts);
        return this;
    }

    var document = window.document,
        defaults = {

        };
    var localCache = {
            timeout: 300000,
            data: {},
            remove: function (keycache) {
                delete localCache.data[keycache];
            },
            exist: function (keycache) {
                return !!localCache.data[keycache] && ((new Date().getTime() - localCache.data[keycache]._) < localCache.timeout);
            },
            get: function (keycache) {

                console.log(JSON.stringify(localCache.data[keycache] ) );
               return  localCache.data[keycache].data ;
            },
            set: function (keycache, cachedData, callback) {
                localCache.remove(keycache);
                localCache.data[keycache] = {
                    _: new Date().getTime(),
                    data: cachedData
                };
                console.log('Get cache for url' + keycache+' + keycache'+ cachedData.code );
                if ($.isFunction(callback)) callback(cachedData);
            }
        };
    ME.prototype = {
        reset: function () {
            this.opts = null;
            this.$container = null;
            this.container = null;
            this.data = null;
            return this;
        },
        init: function (opts) {
            var self = this;
            this._api_url = 'https://payment.mopay.vn/';
            this._api_url_v3 = 'https://payment.mopay.vn/v3/';
            this._app = 'mobo';
            this._key = 'DZHBZJI7TPOOE6QE';
            this.data = null;
            this.$mycache    ={};
            this.opts = jsME.extend({}, defaults, opts);
            this.container = document.getElementById("payent_zone");
            this.$container = jsME(this.container);
            this.container = "";
            this.eventBack = "";
            this.nowCallBack = "listmopay";
            this.params = {'user_agent':opts.user_agent,'access_token':opts.accessToken,'ip':opts.ip,'app':self._app,
                'dev':1,'version':1,'telco':'vms','channel':opts.channel,'platform':opts.platform,'lang':opts.lang,'info':opts.info};
            self.cancelDefaultAction.call(this);

            self.danhsachcongthanhtoan.call(self);
            this.$container.on("click.hb", "#payexject", function (e) {
                alert('xx');
            }).on("click.hb", "#listmopay", function () {
                var $this = jsME(this);
                self.listmopay.call(self);
            }).on("click.hb","#mol-close",function(){
                var $this = jsME(this);
                self.closet.call(self);
            }).on("click.hb","#item_mopay",function(){
                var $this = jsME(this);
                self.danhsachcongthanhtoan.call(self);
            }).on("click.hb","#item_list_payment",function(){
                var $this = jsME(this);
                self.opts.id_item_payment    =   jsME(this).attr('gdata');
                self.opts.g_identify    =   jsME(this).attr('g-identify');
                self.opts.g_action_root    =   jsME(this).attr('g-action-root');
                self.danhsachlistpayment.call(self);
            }).on("click.hb","#item_list_payment_wallet",function(){
                var $this = jsME(this);

                self.opts.id_item_payment    =   jsME(this).attr('gdata');
                self.opts.g_identify    =   jsME(this).attr('g-identify');
                self.opts.g_action_root    =   jsME(this).attr('g-action-root');
                self.danhsachlistpayment_wallet.call(self);
            }).on("click.hb","#item_list_payment_detail",function(){
                var $this = jsME(this);
                self.opts.id_item_payment    =   jsME(this).attr('gdata');
                self.opts.id_item_payment_detail    =   jsME(this).attr('gdata-detail');
                self.opts.g_identify    =   jsME(this).attr('g-identify');
                var g_action    =   jsME(this).attr('g-action');
                self.opts.g_action= g_action;
                self.frompaymentitem.call(self);
            }).on("click.hb","#item_list_wallet_detail",function(){
                var $this = jsME(this);
                self.opts.id_item_payment    =   jsME(this).attr('gdata');
                self.opts.id_item_payment_detail    =   jsME(this).attr('gdata-detail');
                self.opts.g_identify    =   jsME(this).attr('g-identify');
                var g_action    =   jsME(this).attr('g-action');
                self.opts.g_action= g_action;
                self.fromnapvi.call(self);
            }).on("click.hb","#item_list_payment_wallet_detail",function(){
                var $this = jsME(this);
                self.opts.id_item_payment    =   jsME(this).attr('gdata');
                self.opts.id_item_payment_detail    =   jsME(this).attr('gdata-detail');
                self.opts.g_identify    =   jsME(this).attr('g-identify');
                var g_action    =   jsME(this).attr('g-action');
                self.opts.g_action= g_action;
                self.frompaymentitemwallet.call(self);
            }).on('click.hb','.msdk_btn_orange.sd_vimobay',function() {
                elf.opts.bitcoid=null;
                self.opts.bitcoid   =  jsME('#bitcoid').val();
                self.alertsudungvi.call(self);
            }).on('click.hb','.input-card',function() {
                self.alertcard.call(self);
            }).on('click.hb','.input-bank',function() {
                self.opts.bitcoid=null;
                self.opts.bitcoid   =  jsME('#bitcoid').val();
                alert(self.opts.bitcoid);
            }).on('keyup mouseout','.number_seri',function(){
                var snRegexp = /^[1-9][0-9][0-9][1-2][0-9][0-9][0-9]([0][1-9]|[1][0-2])[0-9]{5}$/;
                var number_seri = jsME(this).val();
                var len = number_seri.length;

                if(len == 14) {
                    var isMatch = snRegexp.test(number_seri);
                    alert(isMatch);
                    if(isMatch){
                        jsME('.msdk_clear.pointer').hide();
                    }else{
                        jsME('.msdk_clear.pointer').show();
                    }
                }else{
                    jsME('.msdk_clear.pointer').show();
                }
            }).on('codepin','.number_seri',function(){
                var snRegexp = /^[1-9][0-9][0-9][1-2][0-9][0-9][0-9]([0][1-9]|[1][0-2])[0-9]{5}$/;
            }).on('click.hb','.callBack',function(){
                var $this =jsME(this);
                var fcn =   jsME(this).attr('call-data');
                if(fcn=='danhsachlistpayment'){ self.danhsachlistpayment.call(self);  }
                if(fcn=='danhsachcongthanhtoan'){  self.danhsachcongthanhtoan.call(self); }
                if(fcn=='danhsachtygia'){ self.danhsachtygia.call(self);}
                if(fcn=='congthanhtoan'){self.danhsachvimobay.call(self); }
                if(fcn=='danhsachvimobay'){ self.danhsachvimobay.call(self);}
                if(fcn=='fromnapvi'){self.fromnapvi.call(self); }
                if(fcn=='danhsachlistpayment_wallet'){self.danhsachlistpayment_wallet.call(self); }

            }).on('click.hb','#cahe',function(){
                self.callCache.call(self);
             });

        },callCache:function(){
            var self = this;
            if(localCache.exist('122')) { alert('1')
            }else {
                var url = self.callApi.call(self);

                jsME.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'jsonp',
                    jsonpCallback: self.params.callback,
                    jsonp: false,
                    cache: "true",
                    timeout: 10000,
                    //data: '',//{'account': account, 'captcha': captcha},
                    beforeSend: self.startLoading.call(self),
                    complete: self.topLoading.call(self)
                }).done(function (Jsondata) {
                    localCache.set('122',Jsondata);
                }).error(function (jqXHR, textStatus, ex) {
                    self.Eerror.call(textStatus, ex, jqXHR);
                });
            }

        },closet:function(){
            var self = this;
            self.$container.html('');
        },Eerror:function(textStatus,ex,jqXHR){
           // alert(textStatus + "," + ex + "," + jqXHR.responseText);
        },callApi:function(){
            var self = this;
            //self.params.otp=getOtp(self._key);
            self.params.control ='adapter';
            self.params.token   = jsME.md5(self.params+''+self._key);
            var params=$.param( self.params );


            // alert(self.params.token);
            var url=self._api_url_v3+"?"+params;
            return url;
        },
        cancelDefaultAction:function (e) {
            var self = this;
            var device=0;
            //alert(jsME.browser.chrome );
            return false;
        },
        checkSDK:function(){
            var self = this;
            if(self.opts.accessToken==='' || self.opts.secret===''){
                window.location.href	=	self.opts.URL_CALLBACK;
            }

        },
        enableButtons: function () {
            this.$container.find("#payexject").prop("disabled", true);

        },
        listmopay: function(){
            var self = this;
            self.opts.loadScript(options.URL_TEMPLATE+"template.js", function () {
                self.opts.nowCallBack   ="listmobay";
                self.opts.eventBack='';


                // CALL API DATA
                self.params.func='payment_type';
                self.params.callback='payment_type';
                var url =   self.callApi.call(self);

                jsME.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'jsonp',
                    jsonpCallback:  self.params.callback,
                    jsonp: false,
                    cache: "false",
                    timeout : 10000,
                    //data: '',//{'account': account, 'captcha': captcha},
                    beforeSend: self.startLoading.call(self),
                    complete: self.topLoading.call(self)
                }).done(function(Jsondata) {
                    self.data=Jsondata;
                    self.opts.title='iWin Online';
                    var _TEMPLATE = new template(self.opts);
                    _TEMPLATE.view('list-mopay',self);

                }).error(function(jqXHR, textStatus, ex) {
                    self.Eerror.call(textStatus,ex,jqXHR);
                });


            })
        },
        danhsachtygia: function(){
            var self = this;
            self.opts.nowCallBack   ="danhsachtygia";
            self.opts.eventBack='listmopay';
            self.params.func='payment_exchange';
            self.params.callback='payment_exchange';
            var url =   self.callApi.call(self);
            jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "false",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata;
                self.opts.eventBack='listmopay';
                var _TEMPLATE = new template(self.opts);
                _TEMPLATE.view('danh-sach-ty-gia',self);

            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });



        },
        danhsachcongthanhtoan: function(){
            var self = this;
                self.opts.loadScript(options.URL_TEMPLATE+"template.js", function () {
                self.opts.nowCallBack   ="danhsachcongthanhtoan";
                self.opts.eventBack='listmopay';

                // CALL API DATA
                self.params.func='payment_list';
                self.params.callback='payment_list';
                var url =   self.callApi.call(self);
                        if(localCache.exist('payment_list')){

                        }else
                jsME.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'jsonp',
                    jsonpCallback:  self.params.callback,
                    jsonp: false,
                    cache: true,
                    timeout : 10000,

                    //data: '',//{'account': account, 'captcha': captcha},
                    beforeSend: self.startLoading.call(self),
                    complete: self.topLoading.call(self)
                }).done(function(Jsondata) {

                    self.data=Jsondata;

                    self.opts.title=Jsondata.data.title;
                    var _TEMPLATE = new template(self.opts);
                    _TEMPLATE.view('danh-sach-cong-thanh-toan',self);

                }).error(function(jqXHR, textStatus, ex) {
                    self.Eerror.call(textStatus,ex,jqXHR);
                });
            })

        },
        danhsachlistpayment: function(){
            var self = this;
            self.opts.eventBack='danhsachcongthanhtoan';
            self.opts.nowCallBack   ="danhsachlistpayment";
            if(localCache.exist('payment_list')){
                 var Jsondata =localCache.get('payment_list');//SON.stringify();
                self.data=Jsondata.data.data[self.opts.id_item_payment];
                self.opts.title=Jsondata.data.title;
                var _TEMPLATE = new template(self.opts);
                if(self.opts.g_identify=='mopay_wallet_category'){
                    _TEMPLATE.view('danh-sach-vi-mopay',self);
                }else{
                    _TEMPLATE.view('danh-sach-list-pay-ment',self);
                }

            }else{


            // CALL API DATA
            self.params.func='payment_list';
            self.params.callback='payment_list';
            var url =   self.callApi.call(self);
            self.callCache.call(self);
            /*jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "true",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata.data.data[self.opts.id_item_payment];
                self.opts.title=self.data.title;
                var _TEMPLATE = new template(self.opts);
                if(self.opts.g_identify=='mopay_wallet_category'){
                    _TEMPLATE.view('danh-sach-vi-mopay',self);
                }else{
                    _TEMPLATE.view('danh-sach-list-pay-ment',self);
                }

            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });*/
            }
        },
        danhsachlistpayment_wallet:function(){
            var self = this;

            self.opts.eventBack='fromnapvi';
            self.opts.nowCallBack   ="danhsachlistpayment";

            // CALL API DATA
            self.params.func='payment_list';
            self.params.callback='payment_list';
            var url =   self.callApi.call(self);

            jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "true",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata.data.data[self.opts.id_item_payment];
                self.opts.title=self.data.title;
                var _TEMPLATE = new template(self.opts);

                 _TEMPLATE.view('danh-sach-list-pay-ment-wallet',self);


            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });
        },
        danhsachvimobay: function(){
            var self = this;

            self.opts.eventBack='danhsachcongthanhtoan';
            self.opts.nowCallBack   ="danhsachlistpayment";

            // CALL API DATA
            self.params.func='payment_list';
            self.params.callback='payment_list';
            var url =   self.callApi.call(self);

            jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "true",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata.data.data[3];//self.opts.id_item_payment]
                self.opts.title=self.data.title;
                var _TEMPLATE = new template(self.opts);

                    _TEMPLATE.view('danh-sach-vi-mopay',self);


            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });

        },
        frompaymentitem: function(){
            var self = this;
            self.opts.eventBack='danhsachlistpayment';

            self.opts.nowCallBack   ="frompaymentcard";

            //alert(self.opts.id_item_payment);
            // CALL API DATA
            self.params.func='payment_list';
            self.params.callback='payment_list';
            var url =   self.callApi.call(self);

            jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "true",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata.data.data[self.opts.id_item_payment].data[self.opts.id_item_payment_detail];
                self.opts.title=self.data.title;
                var _TEMPLATE = new template(self.opts);
                if(self.opts.g_action=='pay_card'){
                    _TEMPLATE.view('from-payment-card',self);
                }else if(self.opts.g_action=='pay_ibanking'){
                    _TEMPLATE.view('from-payment-bank',self);
                }



            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });

        },
        frompaymentitemwallet: function(){
            var self = this;
            self.opts.eventBack='danhsachlistpayment_wallet';

            self.opts.nowCallBack   ="frompaymentitemwallet";

            //alert(self.opts.id_item_payment);
            // CALL API DATA
            self.params.func='payment_list';
            self.params.callback='payment_list';
            var url =   self.callApi.call(self);

            jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "true",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata.data.data[self.opts.id_item_payment].data[self.opts.id_item_payment_detail];
                self.opts.title=self.data.title;
                var _TEMPLATE = new template(self.opts);
                if(self.opts.g_action=='pay_card'){
                    _TEMPLATE.view('from-payment-card',self);
                }else if(self.opts.g_action=='pay_ibanking'){
                    _TEMPLATE.view('from-payment-bank',self);
                }



            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });

        },
        fromnapvi: function(){
            var self = this;
            var self = this;
            self.opts.eventBack='congthanhtoan';
            self.opts.nowCallBack   ="fromnapvi";

            //alert(self.opts.id_item_payment);
            // CALL API DATA

            self.params.func='payment_list';
            self.params.callback='payment_list';
            var url =   self.callApi.call(self);

            jsME.ajax({
                url: url,
                type: 'GET',
                dataType: 'jsonp',
                jsonpCallback:  self.params.callback,
                jsonp: false,
                cache: "true",
                timeout : 10000,
                //data: '',//{'account': account, 'captcha': captcha},
                beforeSend: self.startLoading.call(self),
                complete: self.topLoading.call(self)
            }).done(function(Jsondata) {
                self.data=Jsondata;
                self.opts.title=Jsondata.data.title;
                var _TEMPLATE = new template(self.opts);
                if(self.opts.g_action=='pay_wallet'){
                    _TEMPLATE.view('from-su-dung-vi',self);
                }else{///self.opts.g_action=='mopay_wallet'
                    _TEMPLATE.view('from-nap-vi',self);
                }

            }).error(function(jqXHR, textStatus, ex) {
                self.Eerror.call(textStatus,ex,jqXHR);
            });
        },
        fromsudungvi: function(){
            var self = this;
            self.opts.loadScript(options.URL_TEMPLATE+"template.js", function () {
                var _TEMPLATE = new template(self.opts);
                var data    ={'a':1};
                self.data=data;
                _TEMPLATE.view('from-su-dung-vi',self);
            })
        },
        alertsudungvi: function(){
            var self = this;

                var _TEMPLATE = new template(self.opts);
                var data    ={'a':1};
                self.data=data;
                _TEMPLATE.view('alert-su-dung-vi',self);

        },
        alertcard: function(){
            var self = this;

            var _TEMPLATE = new template(self.opts);
            var data    ={'a':1};
            self.data=data;
            _TEMPLATE.view('alert-card-succes',self);

        },startLoading: function() {

        },
        topLoading: function() {

        },
        loaddb:function(self){
            var  self=this;

            var url =   self.callApi.call(self);

            if(self.opts.cache==true && localCache.exist('1eee')){
                var url =   self.callApi.call(self);
                jsME.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'jsonp',
                    jsonpCallback:  self.params.callback,
                    jsonp: false,
                    cache: true,
                    timeout : 10000,

                    //data: '',//{'account': account, 'captcha': captcha},
                    beforeSend: self.startLoading.call(self),
                    complete: self.topLoading.call(self)
                }).done(function(Jsondata) {

                  self.data=Jsondata;

                    self.opts.title=Jsondata.data.title;
                    var _TEMPLATE = new template(self.opts);
                   _TEMPLATE.view('danh-sach-cong-thanh-toan',self);

                }).error(function(jqXHR, textStatus, ex) {
                    self.Eerror.call(textStatus,ex,jqXHR);
                });
            }else{
                alert(2);
                self.data=localCache.get('1eee');
            }
            return self;
        }
    };

    // expose
    window.ME = ME;
})(jQuery);