
var ME_SDK;
(function () {
	"use strict";
    function MEE(opts) {
        if (!(this instanceof ME)) {
            return new ME(opts);
        }
        this.reset.call(this);
        this.init.call(this, opts);
        return this;
    }
	var  isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
   MEE.prototype = {

       init:function(){



        isMSIE = function () {
        var ua = window.navigator.userAgent,
            msie = ua.indexOf("MSIE ");

        if (msie !== -1) {
            return true;
        }

        return false;
    },
        user_agent = function () {
            return window.navigator.userAgent;
        },
        loadCssHack = function (url, callback) {
            var link = document.createElement('link');
            link.type = 'text/css';
            link.rel = 'stylesheet';
            link.href = url;

            document.getElementsByTagName('head')[0].appendChild(link);

            var img = document.createElement('img');
            img.onerror = function () {
                if (callback && typeof callback === "function") {
                    callback();
                }
            };
            img.src = url;
        },
        loadRemote = function (url, type, callback) {
            if (type === "css" && isSafari) {
                loadCssHack(url, callback);
                return;
            }
            var _element, _type, _attr, scr, s, element;

            switch (type) {
                case 'css':
                    _element = "link";
                    _type = "text/css";
                    _attr = "href";
                    break;
                case 'js':
                    _element = "script";
                    _type = "text/javascript";
                    _attr = "src";
                    break;
            }

            scr = document.getElementsByTagName(_element);
            s = scr[scr.length - 1];
            element = document.createElement(_element);
            element.type = _type;
            if (type == "css") {
                element.rel = "stylesheet";
            }
            if (element.readyState) {
                element.onreadystatechange = function () {
                    if (element.readyState == "loaded" || element.readyState == "complete") {
                        element.onreadystatechange = null;
                        if (callback && typeof callback === "function") {
                            callback();
                        }
                    }
                };
            } else {
                element.onload = function () {
                    if (callback && typeof callback === "function") {
                        callback();
                    }
                };
            }
            element[_attr] = url;
            s.parentNode.insertBefore(element, s.nextSibling);
        },
        loadScript = function (url, callback, f) {

            loadRemote(url, "js", callback);
        },
        loadCss = function (url, callback) {
            loadRemote(url, "css", callback);
        }
       },
    }
    var options={lang: 'vi'};
    options.URL_LIBJS="http://sdk.mopay.mobo.dev.10.8.17.103.xip.io/libjs/";
    options.URL_LIB="http://sdk.mopay.mobo.dev.10.8.17.103.xip.io/static/";
    options.URL_TEMPLATE="http://sdk.mopay.mobo.dev.10.8.17.103.xip.io/templatejs/";
    options.URL_CALLBACK="http://sdk.mopay.mobo.dev.10.8.17.103.xip.io";
    options.cache=true;
    options.loadScript=loadScript;
    options.loadCss=loadCss;
    options.user_agent=user_agent();

	loadScript(options.URL_LIBJS+"jquery-1.11.1.min.js", function () {
		jsME.browser = {
			msie: isMSIE()
		};
		//options = jsME.extend({}, config, options);
        loadScript(options.URL_LIB+"js/custom.js", function () {
            loadScript(options.URL_LIBJS + "sha.js", function () {
                loadScript(options.URL_LIBJS + "otp.js", function () {
                    loadScript(options.URL_LIBJS + "sdk.js", function () {
                        ME_SDK = new ME(options);

                        if (options.accessToken === '' || options.secret === '') {
                            ME_SDK.$container.html('Valid access token');
                        }
                    });
                });
            });
        });
		
	});
	
	
		
})();