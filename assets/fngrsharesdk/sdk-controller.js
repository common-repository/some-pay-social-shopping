if (typeof fngrsharesdkcontroller === 'undefined') {
    var $ = jQuery.noConflict();
    var fngrBrowserCookies = {
        createCookie: function _createCookie(name, value, seconds) {
            if (seconds) {
                var date = new Date();
                date.setTime(date.getTime() + (seconds * 1000));
                var expires = "; expires=" + date.toGMTString();
            } else var expires = "";
            document.cookie = name + "=" + value + expires + "; path=/";
        },
        readCookie: function _readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        eraseCookie: function _eraseCookie(name) {
            fngrBrowserCookies.createCookie(name, "", -1);
        }
    };
    var payapiActions = {
        isStaging: false,
        featureEnablePurchases: true,
        featureEnableProductImageIcons: true,
        featureEnableAddToShoppingCart: true,
        whitelisting: [],
        shortenerClientId: "",
        sharedViaString: "Shared via www.FngrApp.com",
        isAndroidBrowser: function _isAndroidBrowser() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            return (/android/i.test(userAgent));
        },
        checkFngrShareMobile: function _checkFngrShareMobile() {
            var isAndroid = typeof AndroidPayapiInterface !== "undefined" && typeof AndroidPayapiInterface.isFngrShareMobile === 'function' && AndroidPayapiInterface.isFngrShareMobile();
            var isIOS = typeof isFngrShareMobileIOS === "function" && isFngrShareMobileIOS();
            return !isAndroid && !isIOS;
        },
        openPayapi: function _openPayapi(url, isPartialPayment) {
            var finalUrl;
            if (typeof url !== 'undefined' && url.startsWith('http')) {
                finalUrl = url;
            } else {
                finalUrl = location.href;
            }
            if (isPartialPayment) {
                var partialQuery = "";
                if (finalUrl.indexOf("?") == -1) {
                    partialQuery = "?partialPayment=1"
                } else {
                    partialQuery = "&partialPayment=1"
                }
                var urlSpecialChars = finalUrl.split("#");
                if (urlSpecialChars.length > 1) {
                    finalUrl = urlSpecialChars[0] + partialQuery + "#" + urlSpecialChars[1];
                } else {
                    finalUrl = urlSpecialChars[0] + partialQuery;
                }
            }
            var destDomain;
            if (finalUrl.indexOf("payapiwebshop=3") == -1) {
                if (finalUrl.indexOf("payapiwebshop=1") !== -1) {
                    destDomain = 'https://staging-input.payapi.io';
                } else if (finalUrl.indexOf("payapiwebshop=2") !== -1) {
                    destDomain = 'https://input.payapi.io';
                } else {
                    if (payapiActions.isStaging) {
                        destDomain = 'https://staging-input.payapi.io';
                    } else {
                        destDomain = 'https://input.payapi.io';
                    }
                }
                var newWindow = window.open(destDomain + '/v1/webshop/' + encodeURIComponent(finalUrl), "_blank");
                if (newWindow == null || typeof newWindow === 'undefined' || newWindow.window.location.href !== 'about:blank') {
                    location.href = destDomain + '/v1/webshop/' + encodeURIComponent(finalUrl);
                }
            } else {
                var newWindow = window.open('/index.php?route=payapi/demo&mode=confirm&product=' + encodeURIComponent(finalUrl), "_blank");
                if (newWindow == null || typeof newWindow === 'undefined' || newWindow.window.location.href !== 'about:blank') {
                    location.href = '/index.php?route=payapi/demo&mode=confirm&product=' + encodeURIComponent(finalUrl);
                }
            }
        },
        checkMandatoryFields: function _checkMandatoryFields(link, isProductPage) {
            var urlWithOpt = link;
            if (isProductPage && typeof _payapi !== 'undefined' && typeof _payapi.checkMandatory !== 'undefined') {
                urlWithOpt = _payapi.checkMandatory(link, true);
                if (urlWithOpt == "") {
                    alert('Please fill in all of the required fields');
                    return false;
                }
            } else if (link.indexOf("hasMandatory=1") > 0) {
                var urlSpecialChars = link.split("#");
                if (urlSpecialChars.length > 1) {
                    urlWithOpt = urlSpecialChars[0] + "&fngr=1" + "#" + urlSpecialChars[1];
                } else {
                    urlWithOpt = urlSpecialChars[0] + "&fngr=1";
                }
                location.href = urlWithOpt;
                return false;
            }
            return urlWithOpt;
        },
        absolutePath: function _absolutePath(base, relative) {
            if (relative.indexOf("/") == 0) {
                return location.origin + relative;
            } else {
                var stack = base.split("/"),
                    parts = relative.split("/");
                stack.pop(); // remove current file name (or empty string)
                // (omit if "base" is the current folder without trailing slash)
                for (var i = 0; i < parts.length; i++) {
                    if (parts[i] == ".") continue;
                    if (parts[i] == "..") stack.pop();
                    else stack.push(parts[i]);
                }
                return stack.join("/");
            }
        },
        payapiShortener: function _payapiShortener(longUrl, handler, channel) {
            if (longUrl.indexOf("http") == -1) {
                longUrl = payapiActions.absolutePath(location.href, longUrl);
            }
            var domain = (payapiActions.isStaging) ? "https://staging.fngr.io" : "https://fngr.io";
            $("body").addClass("fngrloading");
            var postData = {
                "options": {
                    "getTitle": true
                },
                "longUrl": longUrl,
                "source": document.location.host,
                "userPlatform": "JS",
                "userDeviceInfo": navigator.userAgent,
                "clientId": payapiActions.shortenerClientId,
                "sharedToChannel": channel.replace("sh_", "")
            };
            var myNewWin = false;
            if(!fngrtouch.isMobile() || payapiActions.isAndroidBrowser()){
                myNewWin = window.open('','_blank','toolbar=no,status=no,menubar=no,scrollbars=no,top=200,left=400,width=800, height=400, visible=none','');
            }   

            console.log(JSON.stringify(postData));            
            var req = $.ajax({
                url: domain + "/2/shorten", //servlet URL that gets first option as parameter and returns JSON of to-be-populated options
                type: "POST", //request type, can be GET
                cache: false, //do not cache returned data
                data: postData,
                dataType: "json" //type of data returned
            }).done(function(data) {
                title = data.pageTitle || "";
                shortLink = data.shortUrl;
                handler(myNewWin,shortLink, title);                
            }).fail(function(){
                if(myNewWin) myNewWin.close();
                alert(fngrsharesdkcontroller.i18n('maintenance'));                
            });
        },
        shareLink: function _shareLink(channel, link, title) {
            console.log(channel);
            switch (channel) {
                case 'sh_facebook':
                    return 'https://www.facebook.com/sharer.php?p[title]=' + title + '&u=' + encodeURIComponent(link);
                case 'sh_twitter':
                    var sharingMax = Math.min(80, payapiActions.sharedViaString.length);
                    var titleMax = 140 - link.length - sharingMax - 2;
                    if (title.length > titleMax) {
                        title = title.substring(0, titleMax - 3) + "...";
                    }
                    if (payapiActions.sharedViaString.length > sharingMax) {
                        payapiActions.sharedViaString = payapiActions.sharedViaString.substring(0, sharingMax - 3) + "...";
                    }
                    return 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(link) + '&text=' + encodeURIComponent(title + ' ' + payapiActions.sharedViaString);
                case 'sh_whatsapp':
                    return 'whatsapp://send?text=' + encodeURIComponent(title + " | " + link + "\n" + payapiActions.sharedViaString);
                case 'sh_fngrapp':
                    return 'fngrapp://urlComponents?text=' + encodeURIComponent(link);
                case 'sh_gplus':
                    return 'https://plus.google.com/share?url=' + encodeURIComponent(link);
                case 'sh_mailto':
                    return "mailto:" + "?subject=" + escape(payapiActions.sharedViaString) + "&body=" + encodeURIComponent(title + " | " + link + "\n" + payapiActions.sharedViaString);
                case 'sh_linkedin':
                    return "https://www.linkedin.com/shareArticle?mini=true&url=" + encodeURIComponent(link) + "&title=" + escape(title) + "&source=LinkedIn";
                case 'sh_reddit':
                    return "http://www.reddit.com/submit?url=" + encodeURIComponent(link) + "&title=" + escape(title);
                case 'sh_messenger':
                    return "fb-messenger://share?link=" + link + "&title=" + escape(title);
                case 'sh_pinterest':
                    return "https://www.pinterest.com/pin/create/button/?url=" + encodeURIComponent(link) + "&description=" + escape(title);
            }
        },
        getChannelName: function _getChannelName(channel) {
            switch (channel) {
                case 'sh_facebook':
                    return 'Facebook';
                case 'sh_twitter':
                    return 'Twitter';
                case 'sh_whatsapp':
                    return 'WhatsApp';
                case 'sh_fngrapp':
                    return 'FngrApp';
                case 'sh_gplus':
                    return 'Google+';
                case 'sh_mailto':
                    return 'Email';
                case 'sh_linkedin':
                    return "LinkedIn";
                case 'sh_reddit':
                    return "Reddit";
                case 'sh_webshopicon':
                    return (($('#sh_webshopicon').css('visibility') != 'hidden') ? fngrsharesdkcontroller.i18n('instant_buy') : "");
                case 'sh_addtocart':
                    return (($('#sh_addtocart').css('visibility') != 'hidden') ? fngrsharesdkcontroller.i18n('add_to_cart') : "");
                case 'sh_partialpayment':
                    return (($('#sh_partialpayment').css('visibility') != 'hidden') ? fngrsharesdkcontroller.i18n('partial_payment') : "");
                case 'sh_messenger':
                    return 'Messenger';
                case 'sh_pinterest':
                    return 'Pinterest';
            }
        },
        getStoreLink: function _getStoreLink(channel) {
            switch (channel) {
                case 'sh_whatsapp':
                    if (payapiActions.isAndroidBrowser()) {
                        return 'https://play.google.com/store/apps/details?id=com.whatsapp';
                    } else {
                        return 'itms-apps://geo.itunes.apple.com/us/app/whatsapp-messenger/id310633997?mt=8';
                    }
                case 'sh_fngrapp':
                    if (payapiActions.isAndroidBrowser()) {
                        return 'https://play.google.com/store/apps/details?id=fi.fastfingers.eesee.fngrapp';
                    } else {
                        return 'itms-apps://geo.itunes.apple.com/us/app/fngrapp/id1087338111?mt=8';
                    }
                default:
                    return '';
            }
        },
        openPageLink: function _openPageLink(newWindow,href, alternativeUrl) {
            if(!newWindow){
                if (href !== undefined && href !== '') {
                    if (alternativeUrl !== '') {
                        payapiActions.alternativeUrl = alternativeUrl;
                        timer = setTimeout(function() {
                            if (location.href !== href && payapiActions.alternativeUrl != '') {
                                location.href = payapiActions.alternativeUrl;
                            }
                        }, 500);
                    }
                    location.href = href;
                }
            } else {
                newWindow.top.location.href=href;
            }
        }
    };
    var fngrsharesdkcontroller = {
        $draggingItem: null,
        $drpItems: null,
        defaultLang: [],
        localeLang: [],
        backgroundColor: false,
        init: function _init() {
            this.$draggingItem = null;
            this.$drpItems = null;
            fngrsharesdkuiclass.init(fngrtouch.isMobile());
            if (!payapiActions.featureEnablePurchases) { //$(".fngrsharesdk-product").length == 0 && $(".fngrsharesdk-productpage").length == 0) {
                fngrsharesdkuiclass.showWebshopPopup(true, 1000);
            } else {
                var payapiRetCons = fngrBrowserCookies.readCookie('fngrPayapiReturningConsumer');
                if (payapiRetCons != null) {
                    if (payapiRetCons == '1') {
                        $('.webshopicon-small,#sh_webshopicon').addClass('returningconsumer');
                    }
                    fngrsharesdkuiclass.showWebshopPopup(true, 1000);
                } else {
                    fngrsharesdkuiclass.isReturningConsumer();
                }
            }
            fngrsharesdkcontroller.dragItems("a");
            fngrsharesdkcontroller.dragContainer(".fngrsharesdk-container");
            fngrsharesdkcontroller.dragContainer("div[fngrsharesdk-product-container]");
            fngrsharesdkcontroller.droppableItems(".socialCircle-item,#sh_webshopicon,#sh_addtocart,#sh_partialpayment");
            fngrsharesdkcontroller.configureEventsCapture();
            fngrsharesdkuiclass.generatePayapiProductIcons();
            fngrtouch.init();
        },
        dragItems: function _dragItems(items) {
            $(items).addClass("fngrtouchdrag");
            $(items).each(function() {
                $(this).addClass("non-selectable");
                $(this).on("fngr:hold", function(e, pageX, pageY) {
                    fngrsharesdkuiclass.disableScroll();
                    fngrsharesdkcontroller.$draggingItem = $(this);
                    fngrsharesdkuiclass.startLongPress(fngrsharesdkcontroller.$draggingItem);
                    fngrsharesdkuiclass.updateThumbnail(pageX, pageY);
                });
            });
        },
        dragContainer: function _dragContainer(items) {
            $(items).addClass("fngrtouchdrag");
            $(items).each(function() {
                $(this).addClass("non-selectable");
                $(this).on("fngr:hold", function(e, pageX, pageY) {
                    var $tempItem = $(this);
                    setTimeout(function() {
                        fngrsharesdkuiclass.disableScroll();
                        if (fngrsharesdkcontroller.$draggingItem == null) {
                            fngrsharesdkcontroller.$draggingItem = $tempItem;
                            fngrsharesdkuiclass.startLongPress(fngrsharesdkcontroller.$draggingItem);
                            console.log("hold " + pageX + " -- " + pageY);
                            fngrsharesdkuiclass.updateThumbnail(pageX, pageY);
                        }
                    }, 100);
                });
            });
        },
        droppableItems: function _droppableItems(items) {
            fngrsharesdkcontroller.$drpItems = $(items);
            $(".socialCircle-container").click(function() {
                console.log("circle container");
                fngrtouch.enabled = true;
                fngrsharesdkuiclass.resetZoom();
                fngrsharesdkuiclass.closeCircle();
                fngrsharesdkuiclass.clearThumbnail();
                fngrsharesdkuiclass.enableScroll();
                fngrsharesdkcontroller.$draggingItem = null;
            });
            $(items).each(function() {
                $(this).click(function(e) {
                    console.log("dropped click!");
                    fngrsharesdkcontroller.dropAction($(this));
                    fngrtouch.enabled = true;
                    e.preventDefault();
                });
            });
        },
        configureEventsCapture: function _configureEventsCapture() {
            {
                $(document).on("fngr:end", function(e, pageX, pageY) {
                    if (pageX !== null && pageY != null && fngrsharesdkcontroller.$draggingItem != null && fngrsharesdkcontroller.$drpItems != null) {
                        var $selectedDropItem = null;
                        fngrsharesdkcontroller.$drpItems.each(function() {
                            var position = $(this).offset();
                            if (pageX >= position.left && pageX <= position.left + $(this).width() && pageY >= position.top && pageY <= position.top + $(this).height()) {
                                $selectedDropItem = $(this);
                                console.log("dropped!");
                                fngrsharesdkcontroller.dropAction($selectedDropItem);
                            }
                        });
                    }
                    fngrtouch.enabled = true;
                    fngrsharesdkuiclass.resetZoom();
                    fngrsharesdkuiclass.closeCircle();
                    fngrsharesdkuiclass.clearThumbnail();
                    fngrsharesdkuiclass.enableScroll();
                    fngrsharesdkcontroller.$draggingItem = null;
                });
                $(document).on("fngr:move", function(e, pageX, pageY, event) {
                    if (fngrsharesdkcontroller.$draggingItem != null) {
                        event.preventDefault();
                        fngrsharesdkuiclass.updateThumbnail(pageX, pageY);
                    }
                });
            }
        },
        dropAction: function _dropAction($selectedDropItem) {
            var link = fngrsharesdkcontroller.getDraggingLink();
            channel = $selectedDropItem.attr('id');
            if (channel == 'sh_webshopicon') {
                if ($('#sh_webshopicon').css('visibility') != 'hidden') {
                    if ($(".fngrsharesdk-productpage").length > 0 && !fngrsharesdkcontroller.$draggingItem.hasClass('fngrsharesdk-product') && !(typeof hrefContainer !== 'undefined' && hrefContainer !== false)) {
                        link = location.href;
                        isProductPage = true;
                    }
                    fngrBrowserCookies.eraseCookie('fngrPayapiReturningConsumer');
                    var urlWithOpt = payapiActions.checkMandatoryFields(link, isProductPage);
                    if (urlWithOpt) {
                        payapiActions.openPayapi(urlWithOpt, false);
                    }
                }
            } else if (channel == 'sh_addtocart') {
                if ($('#sh_addtocart').css('visibility') != 'hidden') {
                    if ($(".fngrsharesdk-productpage").length > 0 && !fngrsharesdkcontroller.$draggingItem.hasClass('fngrsharesdk-product') && !(typeof hrefContainer !== 'undefined' && hrefContainer !== false)) {
                        link = location.href;
                        isProductPage = true;
                    }
                    var urlWithOpt = payapiActions.checkMandatoryFields(link, isProductPage);
                    if (urlWithOpt) {
                        if (typeof _payapi !== 'undefined' && typeof _payapi.addtocart !== 'undefined') {
                            _payapi.addtocart(link);
                        } else {
                            console.error("_payapi.addtocart(url) js method must be defined by merchant");
                        }
                    }
                }
            } else if (channel == 'sh_partialpayment') {
                if ($('#sh_partialpayment').css('visibility') != 'hidden') {
                    var isProductPage = false;
                    if ($(".fngrsharesdk-productpage").length > 0 && !fngrsharesdkcontroller.$draggingItem.hasClass('fngrsharesdk-product') && !(typeof hrefContainer !== 'undefined' && hrefContainer !== false)) {
                        link = location.href;
                        isProductPage = true;
                    }
                    fngrBrowserCookies.eraseCookie('fngrPayapiReturningConsumer');
                    var urlWithOpt = payapiActions.checkMandatoryFields(link, isProductPage);
                    if (urlWithOpt) {
                        payapiActions.openPayapi(urlWithOpt, true);
                    }
                }
            } else {
                if ($(".fngrsharesdk-productpage").length > 0 && !fngrsharesdkcontroller.$draggingItem.hasClass('fngrsharesdk-product') && !(typeof hrefContainer !== 'undefined' && hrefContainer !== false)) {
                    link = location.href;
                }
                payapiActions.payapiShortener(link, function(newWin,shortLink, title) {
                    payapiActions.openPageLink(newWin,payapiActions.shareLink(channel, shortLink, title), payapiActions.getStoreLink(channel));
                }, channel);
            }
        },
        findSdkPath: function _findSdkPath() {
            var scripts = document.getElementsByTagName('script');
            for (i = 0; i < scripts.length; i++) {
                var path = scripts[i].src;
                if (path && path.indexOf("sdk-controller") >= 0) {
                    var mydir = path.split('/').slice(0, -1).join('/') + '/'; // remove last filename part of path
                    console.log(mydir);
                    return mydir;
                }
            }
            return null;
        },
        loadLanguages: function _loadLanguages() {
            var sdkPath = fngrsharesdkcontroller.findSdkPath();
            if (sdkPath) {
                $.getJSON(sdkPath + "locale/en.locale", function(json) {
                    fngrsharesdkcontroller.defaultLang = json;
                    console.log("loaded default");
                    var userLang = document.documentElement.lang || navigator.language || navigator.userLanguage;
                    console.log("The language is:" + userLang);
                    if (userLang.indexOf("fi") == 0 || userLang.indexOf("es") == 0 || userLang.indexOf("en") == 0 || userLang.indexOf("da") == 0 || userLang.indexOf("nb") == 0 || userLang.indexOf("pt") == 0 || userLang.indexOf("sv") == 0) {
                        try {
                            $.getJSON(sdkPath + "locale/" + userLang.substring(0, 2) + ".locale", function(json) {
                                fngrsharesdkcontroller.localeLang = json;
                                console.log("loaded locale");
                                fngrsharesdkcontroller.init();
                            });
                        } catch (ex) {
                            fngrsharesdkcontroller.init();
                        }
                    } else {
                        console.log("using default language: EN");
                        fngrsharesdkcontroller.localeLang = json;
                        fngrsharesdkcontroller.init();
                    }
                });
            }
        },
        getDynamicConfig: function _getDynamicConfig() {
            if (typeof _fngrshareConfig !== 'undefined') {
                if (typeof _fngrshareConfig.isStaging !== 'undefined') {
                    payapiActions.isStaging = _fngrshareConfig.isStaging();
                }
                if (typeof _fngrshareConfig.enablePurchases !== 'undefined') {
                    payapiActions.featureEnablePurchases = _fngrshareConfig.enablePurchases();
                }
                if (typeof _fngrshareConfig.shortenerClientId !== 'undefined') {
                    payapiActions.shortenerClientId = _fngrshareConfig.shortenerClientId();
                }
            }
            console.log("IS STAGING: " + payapiActions.isStaging);
            console.log("PURCHASES ENABLED: " + payapiActions.featureEnablePurchases);
        },
        i18n: function _i18n(key) {
            if (key in this.localeLang) {
                return this.localeLang[key];
            } else if (key in this.defaultLang) {
                return this.defaultLang[key];
            }
            return key;
        },
        validateSharingItems: function _validateSharingItems(items) {
            var result = true;
            if (items.constructor === Array && items.length > 0) {
                items.forEach(function(item) {
                    var itemLower = item.toLowerCase();
                    if (!(((itemLower === "whatsapp" || itemLower === "messenger") && fngrtouch.isMobile()) || itemLower === "twitter" || itemLower === "facebook" || itemLower === "reddit" || itemLower === "linkedin" || itemLower === "mailto" || itemLower === "gplus" || itemLower === "pinterest")) {
                        result = false;
                    }
                });
                return result;
            }
            return false;
        },
        validateBackgroundColor: function _validateBackgroundColor(items) {
            if (items.constructor === Array && items.length == 4) {
                var n = [];
                for (var i = 0; i < 3; i++) {
                    n[i] = Math.floor(Number(items[i]));
                    if (!(n[i] >= 0 && n[i] <= 255)) return false;
                }
                if (isNaN(items[3])) {
                    return false;
                } else {
                    n[3] = Number(items[3]);
                    if (n[3] >= 0 && n[3] <= 1) {
                        return n;
                    }
                }
            }
            return false;
        },
        getDraggingLink: function _getDraggingLink() {
            var link = "";
            var attr = fngrsharesdkcontroller.$draggingItem.attr('href');
            var hrefContainer = fngrsharesdkcontroller.$draggingItem.attr('fngrsharesdk-product-container');
            if (typeof hrefContainer !== 'undefined' && hrefContainer !== false) {
                link = hrefContainer;
            } else if (typeof attr !== 'undefined' && attr !== false) {
                var ancestorWithContainer = fngrsharesdkcontroller.$draggingItem.closest("[fngrsharesdk-product-container]");
                console.log("ANCESTOR " + ancestorWithContainer.length);
                if (ancestorWithContainer.length > 0) {
                    attr = ancestorWithContainer.attr('fngrsharesdk-product-container');
                    console.log("ancestor container: " + attr);
                }
                link = attr;
            } else {
                link = location.href;
            }
            return link;
        }
    };
    window.onbeforeunload = function() {
        payapiActions.alternativeUrl = '';
    };
    //Initial Configuration
    $(document).ready(function() {
        if (payapiActions.checkFngrShareMobile()) {
            var sdkPath = fngrsharesdkcontroller.findSdkPath();
            if (sdkPath) {
                $.getJSON(sdkPath + "config", function(data) {
                    console.log("config file found!");
                    $.each(data, function(key, val) {
                        if (key == 'staging') {
                            payapiActions.isStaging = val;
                        } else if (key == 'sharedVia') {
                            payapiActions.sharedViaString = val;
                        } else if (key == 'featureEnablePurchases') {
                            payapiActions.featureEnablePurchases = val;
                        } else if (key == 'featureEnableAddToShoppingCart') {
                            payapiActions.featureEnableAddToShoppingCart = val;
                        } else if (key == 'featureEnableProductImageIcons') {
                            payapiActions.featureEnableProductImageIcons = val;
                        } else if (key == 'featureCircleBackgroundColor') {
                            fngrsharesdkcontroller.backgroundColor = fngrsharesdkcontroller.validateBackgroundColor(val);
                        } else if (key == 'shareCircleWhitelist') {
                            if (fngrtouch.isMobile()) {
                                if (val.mobile && fngrsharesdkcontroller.validateSharingItems(val.mobile)) {
                                    console.log("mobile CONFIG whitelisting SUCCESS!");
                                    payapiActions.whitelisting = val.mobile;
                                } else {
                                    console.log("mobile CONFIG whitelisting NOT VALID. USING defaults");
                                    payapiActions.whitelisting = ["whatsapp", "twitter", "facebook", "messenger", "pinterest", "mailto"];
                                }
                            } else {
                                if (val.desktop && fngrsharesdkcontroller.validateSharingItems(val.desktop)) {
                                    console.log("desktop CONFIG whitelisting SUCCESS!");
                                    payapiActions.whitelisting = val.desktop;
                                } else {
                                    console.log("desktop CONFIG whitelisting NOT VALID. USING defaults");
                                    payapiActions.whitelisting = ["twitter", "facebook", "reddit", "linkedin", "pinterest", "mailto"];
                                }
                            }
                        } else if (key == 'shortenerClientId') {
                            payapiActions.shortenerClientId = val;
                        }
                    });
                    fngrsharesdkcontroller.getDynamicConfig();
                    fngrsharesdkcontroller.loadLanguages();
                }).fail(function(x, y, z) {
                    console.log("IS STAGING: " + payapiActions.isStaging);
                    console.log("Error loading config file: " + y + "  " + z);
                    console.log("Using default config settings");
                    if (fngrtouch.isMobile()) {
                        payapiActions.whitelisting = ["whatsapp", "twitter", "facebook", "messenger", "pinterest", "mailto"];
                    } else {
                        payapiActions.whitelisting = ["twitter", "facebook", "reddit", "linkedin", "pinterest", "mailto"];
                    }
                    fngrsharesdkcontroller.getDynamicConfig();
                    fngrsharesdkcontroller.loadLanguages();
                });
            } else {
                fngrsharesdkcontroller.getDynamicConfig();
                fngrsharesdkcontroller.loadLanguages();
            }
        } else {
            fngrtouch.enabled = false;
        }
    });
} else {
    console.warn("FngrShareSDK: Controller loaded multiple times");
}