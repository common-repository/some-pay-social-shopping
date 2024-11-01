if (typeof fngrsharesdkuiclass === 'undefined') {
    var centerCircle, rotate, container, width, height;
    var settings = {
        rotate: 270,
        radius: 200,
        circleSize: 1,
        speed: 500
    };
    var fngrsharesdkuiclass = {
        wOrig: [],
        topOri: [],
        leftOri: [],
        isExpanded: false,
        $thumbnail: null,
        isMobile: false,
        closedPopup: true,
        itemsDistance: 0,
        init: function _init(isMobile) {
            fngrsharesdkuiclass.isMobile = isMobile;
            if (isMobile) {
                console.log("Mobile");
                fngrsharesdkuiclass.drawSocialCircle(true);
            } else {
                console.log("Desktop");
                fngrsharesdkuiclass.drawSocialCircle(false);
            }
            $(window).on("orientationchange", function(event) {
                fngrsharesdkuiclass.closeCircle();
                fngrsharesdkuiclass.resetZoom();
                fngrsharesdkuiclass.wOrig = [];
                fngrsharesdkuiclass.topOri = [];
                fngrsharesdkuiclass.leftOri = [];
                $(document).trigger('fngr:end');
            });
            $(window).resize(function() {
                fngrsharesdkuiclass.closeCircle();
                fngrsharesdkuiclass.resetZoom();
                fngrsharesdkuiclass.wOrig = [];
                fngrsharesdkuiclass.topOri = [];
                fngrsharesdkuiclass.leftOri = [];
                $(document).trigger('fngr:end');
                width = $(window).width(),
                    height = $(window).height();
            });
            $body = $("body");
            $body.append('<div id="fngrholder"></div>');
            $body.append("<div class='fngrmodal'></div>")
            $(document).on({
                ajaxStop: function() {
                    $body.removeClass("fngrloading");
                }
            });
            this.$thumbnail = $("#fngrholder");
            centerCircle = $(".socialCircle-center");
            container = centerCircle.parent(),
                width = $(window).width(),
                height = $(window).height();
            fngrsharesdkuiclass.closeCircle();
            if (settings.rotate == 0) {
                rotate = 0;
            } else {
                rotate = (Math.PI) * 2 * settings.rotate / 360;
            }
            if (!payapiActions.featureEnablePurchases) {
                $('.webshopicon-small').addClass('sharepage');
            }
            $("a.fngrsharesdk-share").click(function(e) {
                e.preventDefault();
                $draggingItem = $(this);
                console.log("click... open sharing circle");
                if (fngrsharesdkuiclass.isMobile) {
                    fngrtouch.enabled = false;
                    $draggingItem.trigger("fngr:hold", [e.pageX, e.pageY]);
                } else {
                    $draggingItem.trigger("fngr:hold", [e.pageX, e.pageY]);
                }
            });
            //----- CLOSE
            $('[data-popup-close]').on('click', function(e) {
                var targeted_popup_class = jQuery(this).attr('data-popup-close');
                $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
                fngrsharesdkuiclass.closedPopup = true;
                e.preventDefault();
            });
            //Prevent shadow in drag and drop safari and firefox
            $("img, a").attr('onmousedown', "if (event.preventDefault) event.preventDefault()");
        },
        startLongPress: function _startLongPress(target) {
            fngrsharesdkuiclass.disableZoom();
            if (centerCircle.hasClass("closed")) {
                $(".socialCircle-item").removeClass("landscape");
                $(".socialCircle-item").removeClass("portrait");
                if (window.innerHeight > window.innerWidth) {
                    $(".socialCircle-item").addClass("portrait");
                } else {
                    $(".socialCircle-item").addClass("landscape");
                }
                var productContainerAttr = $(target).attr('fngrsharesdk-product-container');
                var isProductContainer = (typeof productContainerAttr !== typeof undefined && productContainerAttr !== false) || $(target).closest("[fngrsharesdk-product-container]").length > 0;
                if (payapiActions.featureEnablePurchases && ($(target).hasClass("fngrsharesdk-product") || $(".fngrsharesdk-productpage").length > 0 || isProductContainer)) {
                    var ppEnabled = fngrsharesdkcontroller.getDraggingLink();
                    var hasPartialPaymentEnabled = ppEnabled.indexOf("partialPaymentsEnabled=1") > 0;
                    $("#sh_webshopicon").css("visibility", "visible");
                    $("#targetNameWebshop").css("visibility", "visible");
                    if (hasPartialPaymentEnabled || ($(".fngrsharesdk-productpage").length > 0 && !isProductContainer && !$(target).hasClass("fngrsharesdk-product") && typeof _payapi !== 'undefined' && typeof _payapi.partialPaymentEnabledForProduct !== 'undefined' && _payapi.partialPaymentEnabledForProduct(location.href))) {
                        $("#sh_partialpayment").css("visibility", "visible");
                    } else {
                        $("#sh_partialpayment").css("visibility", "hidden");
                    }
                } else {
                    $("#sh_webshopicon").css("visibility", "hidden");
                    $("#targetNameWebshop").css("visibility", "hidden");
                    $("#sh_partialpayment").css("visibility", "hidden");
                }
                if (payapiActions.featureEnableAddToShoppingCart && ($(target).hasClass("fngrsharesdk-product") || $(".fngrsharesdk-productpage").length > 0 || isProductContainer)) {
                    $("#sh_addtocart").css("visibility", "visible");
                } else {
                    $("#sh_addtocart").css("visibility", "hidden");
                }
                var scale = 100.0 / Math.max($(target).width(), $(target).height());
                console.log($(target).html().length);
                if ($(target).html().length < 10000) {
                    $("#fngrholder").append($(target).clone().css("zoom", scale).addClass("centered"));
                } else {
                    $("#fngrholder").append('<div class="fullpageImage centered"/>');
                }
                settings.radius = Math.min(window.innerWidth, window.innerHeight) * 0.38;
                centerCircle.removeClass("closed").addClass("open");
                $(".fngrsharesdk-container").addClass("fngrblur");
                centerCircle.parent().css("visibility", "visible");
                $(".webshopicon-small").css("visibility", "hidden");
                fngrsharesdkuiclass.expand();
                $("#targetName").css("visibility", "visible");
                $("#targetName").text("");
            } else {
                $(".fngrsharesdk-container").removeClass("fngrblur");
                centerCircle.removeClass("open").addClass("closed");
                fngrsharesdkuiclass.retract();
            }
        },
        closeCircle: function _closeCircle() {
            if (centerCircle.hasClass("open")) {
                var icons = centerCircle.siblings();
                $("#targetName").css("visibility", "hidden");
                $("#targetNameWebshop").css("visibility", "hidden");
                $(".fngrsharesdk-container").removeClass("fngrblur");
                centerCircle.removeClass("open").addClass("closed");
                icons.each(function() {
                    $(this).addClass("openinganim");
                });
                fngrsharesdkuiclass.retract();
            }
        },
        expand: function _expand() {
            // variables for expand function  
            var radius = settings.radius,
                icons = centerCircle.siblings(),
                step = (2 * Math.PI) / icons.length / settings.circleSize,
                angle = rotate + (step / 2);
            // Determine placement of icons 
            var firstItem = false;
            var iconWidht = 0;
            icons.each(function() {
                var x = Math.round(width / 2 + radius * Math.cos(angle) - $(this).width() / 2);
                var y = Math.round(height / 2 + radius * Math.sin(angle) - $(this).height() / 2);
                if (!firstItem) {
                    firstItem = [x, y];
                } else {
                    if (!fngrsharesdkuiclass.itemsDistance) {
                        fngrsharesdkuiclass.itemsDistance = Math.hypot(firstItem[0] - x, firstItem[1] - y);
                    }
                }
                // Animate Expansion
                $(this).css({
                    left: x + 'px',
                    top: y + 'px',
                    margin: '0px'
                });
                var id = $(this).attr('id');
                fngrsharesdkuiclass.wOrig[id] = $(this).width();
                fngrsharesdkuiclass.topOri[id] = y;
                fngrsharesdkuiclass.leftOri[id] = x;
                iconWidht = fngrsharesdkuiclass.wOrig[id];
                angle += step;
            });
            //Remove text if intersects with sharing circle
            var textLeft = $("#targetNameWebshop").position().left;
            var iconTop = icons.first().position().top;
            var textBottom = $("#targetNameWebshop").position().top + $("#targetNameWebshop").height();
            var iconRight = icons.first().position().left + icons.first().width();
            if (iconTop < textBottom && iconRight > textLeft) {
                $("#targetNameWebshop").css("visibility", "hidden");
            }
            fngrsharesdkuiclass.isExpanded = true;
            setTimeout(function() {
                icons.each(function() {
                    $(this).removeClass("openinganim");
                });
            }, 400);
            fngrsharesdkuiclass.wOrig['sh_webshopicon'] = iconWidht * 1.2;
            fngrsharesdkuiclass.topOri['sh_webshopicon'] = 20; //$("#sh_webshopicon").position().top;
            fngrsharesdkuiclass.leftOri['sh_webshopicon'] = $(".socialCircle-container").width() - fngrsharesdkuiclass.wOrig['sh_webshopicon'] - 20; //$("#sh_webshopicon").position().left;
            fngrsharesdkuiclass.wOrig['sh_addtocart'] = iconWidht;
            fngrsharesdkuiclass.topOri['sh_addtocart'] = 20;
            fngrsharesdkuiclass.leftOri['sh_addtocart'] = 20;
            fngrsharesdkuiclass.wOrig['sh_partialpayment'] = iconWidht;
            fngrsharesdkuiclass.topOri['sh_partialpayment'] = $(".socialCircle-container").height() - fngrsharesdkuiclass.wOrig['sh_partialpayment'] - 20;
            fngrsharesdkuiclass.leftOri['sh_partialpayment'] = $(".socialCircle-container").width() - fngrsharesdkuiclass.wOrig['sh_partialpayment'] - 20;
            $('#sh_partialpayment').css({
                "width": iconWidht,
                "max-width": iconWidht,
                "top": fngrsharesdkuiclass.topOri['sh_partialpayment'],
                "left": fngrsharesdkuiclass.leftOri['sh_partialpayment']
            });
            fngrsharesdkuiclass.startRotationAnim();
        },
        retract: function _retract() {
            var radius = 0,
                icons = centerCircle.siblings(),
                angle = rotate,
                step = (2 * Math.PI) / icons.length / settings.circleSize;
            // Determine placement of icons 
            $("#targetName").text("");
            fngrsharesdkuiclass.applyScale($("#sh_webshopicon"), 0, 0);
            fngrsharesdkuiclass.applyScale($("#sh_addtocart"), 0, 0);
            fngrsharesdkuiclass.applyScale($("#sh_partialpayment"), 0, 0);
            icons.each(function() {
                fngrsharesdkuiclass.applyScale($(this), 0, 0);
                var x = Math.round(width / 2 + radius * Math.cos(angle) - $(this).width() / 2);
                var y = Math.round(height / 2 + radius * Math.sin(angle) - $(this).height() / 2);
                // Animate Retractions  
                $(this).animate({
                    left: x + 'px',
                    top: y + 'px',
                    margin: '0px',
                }, {
                    duration: 1,
                    queue: false,
                    complete: function() {
                        fngrsharesdkuiclass.isExpanded = false;
                        $("#sh_webshopicon").css("visibility", "hidden");
                        $("#sh_addtocart").css("visibility", "hidden");
                        $("#sh_partialpayment").css("visibility", "hidden");
                        $("#targetNameWebshop").css("visibility", "hidden");
                        $(this).parent().css("visibility", "hidden");
                        $(".webshopicon-small").css("visibility", "visible");
                    }
                });
                angle += step;
            });
            fngrsharesdkuiclass.clearRotationAnim();
        },
        drawSocialCircle: function _drawSocialCircle(isMobile) {
            var orientation = "";
            if (window.innerHeight > window.innerWidth) {
                orientation = "portrait";
            } else {
                orientation = "landscape";
            }
            var html = '<div id="fngrBackground"><div class="socialCircle-container">';
            payapiActions.whitelisting.forEach(function(name) {
                html += '<div class="openinganim socialCircle-item ' + orientation + '" id="sh_' + name.toLowerCase() + '"></div>';
            });
            html += '<div id="socialCircle-center" class="socialCircle-center closed"></div></div></div>';
            html += '<div><div id="sh_addtocart" class="webshopicon-item-cart ' + orientation + '" style="visibility:hidden"></div></div>';
            html += '<div><div id="sh_partialpayment" class="webshopicon-item-partial ' + orientation + '" style="visibility:hidden"></div></div>';
            html += '<div><div id="sh_webshopicon" class="webshopicon-item ' + orientation + '" style="visibility:hidden"></div></div>';
            html += '<div class="non-selectable webshopicon-small ' + orientation + '"></div>';
            html += '<div id="targetName" class="non-selectable"></div>';
            html += '<div id="targetNameWebshop" class="non-selectable"><span>' + fngrsharesdkcontroller.i18n('instant_buy') + '</span><div id="fngrarrow"></div></div>';
            html += '<div class="fngrpopup-inner" data-popup="popup-1"><div></div><div style="margin-top:10px; margin-bottom:-20px" id="fngr_intro_img"></div><table width="100%"><tr><td style="text-align: center" width="48%">1. ' + fngrsharesdkcontroller.i18n('intro_step1') + '</td><td></td><td style="text-align: center" width="48%">2. ' + fngrsharesdkcontroller.i18n('intro_step2') + '</td></tr></table><div class="fngrpopup-close" data-popup-close="popup-1" href="#">x</div></div>';
            $("body").prepend(html);
            $(".webshopicon-small").click(function() {
                fngrsharesdkuiclass.showWebshopPopup(false, 0);
            });
            if (fngrsharesdkcontroller.backgroundColor) {
                var bg = fngrsharesdkcontroller.backgroundColor;
                $("#fngrBackground").css("background", "rgba(" + bg[0] + "," + bg[1] + "," + bg[2] + "," + bg[3] + ")");
            }
        },
        disableZoom: function _disableZoom() {
            var viewportmeta = document.querySelector('meta[name="viewport"]');
            if (viewportmeta) {
                viewportmeta.content = 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0';
            }
        },
        resetZoom: function _resetZoom() {
            var viewportmeta = document.querySelector('meta[name="viewport"]');
            if (viewportmeta) {
                viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=2.0';
            }
        },
        generatePayapiProductIcons: function _generatePayapiProductIcons() {
            if (payapiActions.featureEnableProductImageIcons) {
                $items = $(".fngrsharesdk-product");
                $items.each(function() {
                    if (payapiActions.featureEnablePurchases) {
                        $(this).prepend("<div class='payapiProductIcon'></div>");
                    } else {
                        $(this).prepend("<div class='payapiProductIcon payapiProductShareIcon'></div>");
                    }
                });
                $(".payapiProductIcon,.payapiOpenSharingScreen").click(function(e) {
                    fngrsharesdkuiclass.disableScroll();
                    e.preventDefault();
                    $draggingItem = $(this).parent();
                    console.log("click... open sharing circle");
                    e.stopPropagation();
                    if (fngrsharesdkuiclass.isMobile) {
                        fngrtouch.enabled = false;
                        $draggingItem.trigger("fngr:hold", [e.pageX, e.pageY]);
                    } else {
                        $draggingItem.trigger("fngr:hold", [e.pageX, e.pageY]);
                    }
                });
            }
        },
        isReturningConsumer: function _isReturningConsumer() {
            var domain = (payapiActions.isStaging) ? "https://staging-input.payapi.io" : "https://input.payapi.io";
            console.log("isReturningConsumer " + domain);
            $.get(domain + '/v1/sdk/consumer/returning', '', function(data) {
                console.log("isReturning " + data.isReturning);
                var seconds = 600; // 10 minutes
                if (data != null && data.isReturning) {
                    fngrBrowserCookies.createCookie('fngrPayapiReturningConsumer', '1', seconds);
                    $('.webshopicon-small,#sh_webshopicon').addClass('returningconsumer');
                } else {
                    fngrBrowserCookies.createCookie('fngrPayapiReturningConsumer', '0', seconds);
                    $('.webshopicon-small,#sh_webshopicon').removeClass('returningconsumer');
                }
                fngrsharesdkuiclass.showWebshopPopup(true, 0);
            }, 'jsonp');
        },
        applyScale: function _applyScale(element, cursorX, cursorY) {
            var id = $(element).attr('id');
            var lft = fngrsharesdkuiclass.leftOri[id],
                top = fngrsharesdkuiclass.topOri[id],
                wdt = fngrsharesdkuiclass.wOrig[id];
            var itemX = lft + wdt / 2.0;
            var itemY = top + wdt / 2.0;
            var distance = Math.hypot(itemX - (cursorX - $(document).scrollLeft()), itemY - (cursorY - $(document).scrollTop()));
            var scale;
            var minDistance = wdt;
            var maxDistance = fngrsharesdkuiclass.itemsDistance;
            if (distance <= minDistance) {
                scale = 1.5;
                $("#targetName").text(payapiActions.getChannelName(id));
            } else if (distance >= maxDistance) {
                scale = 1.0;
            } else {
                scale = 1.0 + (maxDistance - distance) / (2.0 * (maxDistance - minDistance));
            }
            if (wdt != scale * $(element).width()) {
                var wNew = scale * wdt;
                var topNew = top - (wNew - wdt) / 2.0;
                var leftNew = lft - (wNew - wdt) / 2.0;
                $(element).css({
                    "width": wNew,
                    "max-width": wNew,
                    "top": topNew,
                    "left": leftNew
                });
            }
        },
        updateThumbnail: function _updateThumbnail(pageX, pageY) {
            if (fngrtouch.enabled && fngrsharesdkuiclass.isExpanded) {
                var xPos = pageX - fngrsharesdkuiclass.$thumbnail.width() / 2 - $(document).scrollLeft();
                var yPos = pageY - fngrsharesdkuiclass.$thumbnail.height() / 2 - $(document).scrollTop();
                fngrsharesdkuiclass.$thumbnail.css({
                    "display": "block",
                    "top": yPos + "px",
                    "left": xPos + "px"
                });
                if (fngrsharesdkcontroller.$drpItems != null) {
                    $("#targetName").text("");
                    fngrsharesdkcontroller.$drpItems.each(function() {
                        var position = $(this).offset();
                        fngrsharesdkuiclass.applyScale($(this), pageX, pageY);
                    });
                }
            }
        },
        clearThumbnail: function _clearThumbnail() {
            fngrsharesdkuiclass.wOrig = [];
            fngrsharesdkuiclass.topOri = [];
            fngrsharesdkuiclass.leftOri = [];
            fngrsharesdkuiclass.$thumbnail.css({
                "display": "none"
            });
            fngrsharesdkuiclass.$thumbnail.empty();
        },
        showWebshopPopup: function _showWebshopPopup(isAuto, delay) {
            var text, closeAfter;
            var payapiRetConsCookie = fngrBrowserCookies.readCookie('fngrPayapiReturningConsumer'),
                internalReturningConsumerCookie = fngrBrowserCookies.readCookie('fngrInternalReturningConsumer');
            if (payapiRetConsCookie != null && payapiRetConsCookie == '1') {
                if (!isAuto || internalReturningConsumerCookie == null) {
                    text = fngrsharesdkcontroller.i18n('intro_again');
                    closeAfter = 20000;
                    var seconds = 15.0 * 60; // 15 minutes
                    if (isAuto) {
                        fngrBrowserCookies.createCookie('fngrInternalReturningConsumer', '1', seconds);
                    }
                } else {
                    closeAfter = 0;
                }
            } else {
                closeAfter = 30000;
                if ($(".webshopicon-small").hasClass("sharepage")) {
                    text = fngrsharesdkcontroller.i18n("intro_social");
                } else {
                    text = fngrsharesdkcontroller.i18n("intro_full");
                }
                var returningCookie = fngrBrowserCookies.readCookie('fngrReturningUser');
                if (returningCookie != null && returningCookie == '1' && isAuto) {
                    closeAfter = 0;
                }
            }
            if (closeAfter > 0 && fngrsharesdkuiclass.closedPopup) {
                setTimeout(function() {
                    $element = $('[data-popup="popup-1"]');
                    $('[data-popup="popup-1"] > div:eq(0)').text(text);
                    var seconds = 365 * (24 * 60 * 60); // 365 days
                    fngrBrowserCookies.createCookie('fngrReturningUser', '1', seconds);
                    $element.fadeIn(350);
                    fngrsharesdkuiclass.closedPopup = false;
                    setTimeout(function() {
                        if (!fngrsharesdkuiclass.closedPopup) {
                            $element.fadeOut(350);
                            console.log("closing popup");
                            fngrsharesdkuiclass.closedPopup = true;
                        }
                    }, closeAfter);
                }, delay);
            }
        },
        preventDefault: function _preventDefault(e) {
            e = e || window.event;
            if (e.preventDefault) e.preventDefault();
            e.returnValue = false;
        },
        disableScroll: function _disableScroll() {
            if (window.addEventListener) // older FF
                window.addEventListener('DOMMouseScroll', fngrsharesdkuiclass.preventDefault, false);
            window.onwheel = fngrsharesdkuiclass.preventDefault; // modern standard
            window.onmousewheel = document.onmousewheel = fngrsharesdkuiclass.preventDefault; // older browsers, IE
            window.ontouchmove = fngrsharesdkuiclass.preventDefault; // mobile    
        },
        enableScroll: function _enableScroll() {
            if (window.removeEventListener) window.removeEventListener('DOMMouseScroll', fngrsharesdkuiclass.preventDefault, false);
            window.onmousewheel = document.onmousewheel = null;
            window.onwheel = null;
            window.ontouchmove = null;
            document.onkeydown = null;
        },
        startRotationAnim: function _rotationAnim() {
            $("#fngrBackground").css("visibility", "visible");
            $(".socialCircle-container").addClass("fngrCircleAnimation");
        },
        clearRotationAnim: function _clearRotationAnim() {
            $(".socialCircle-container").removeClass("fngrCircleAnimation");
            $("#fngrBackground").css("visibility", "hidden");
        }
    };
} else {
    console.warn("FngrShareSDK: UI loaded multiple times");
}