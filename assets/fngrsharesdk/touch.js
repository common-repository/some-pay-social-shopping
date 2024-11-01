if (typeof fngrtouch === 'undefined') {
    var fngrtouch = {
        fngrpresstimer: false,
        $selection: null,
        lastEvent: null,
        enabled: true,
        isSelect: false,
        startedTouch: false,
        isScrollStopped: true,
        ignoreMouseUp: false,
        deviceIsBlackListed: false,
        init: function _init() {
            $(document).contextmenu(function(e) {
                fngrtouch.ignoreMouseUp = true;
                e.preventDefault();
                e.stopImmediatePropagation();
                e.returnValue = false;
                return false;
            });
            if (fngrtouch.isMobile()) {
                fngrtouch.deviceIsBlackListed = navigator.userAgent.toLowerCase().indexOf('firefox') > -1 || navigator.userAgent.toLowerCase().indexOf('miuibrowser') > -1 || navigator.userAgent.toLowerCase().indexOf('fxios') > -1 || navigator.userAgent.toLowerCase().indexOf('opr') > -1;
                //IGNORE 3D TOUCH…....IS IGNORING ALSO THE CLICK THIS WAY, TRY NOT TO PREVENT IF CLICK
                $(document).on('touchforcechange', function(e) {
                    if (fngrtouch.startedTouch && e.originalEvent.touches[0].force > 0.15) {
                        e.preventDefault();
                    }
                });
                //Do not allow to open the sharing circle while scrolling
                $(window).scroll(function() {
                    fngrtouch.isScrollStopped = false;
                    clearTimeout($.data(this, "scrollCheck"));
                    $.data(this, "scrollCheck", setTimeout(function() {
                        fngrtouch.isScrollStopped = true;
                    }, 600));
                });
                $(".fngrtouchdrag").on("touchstart", function(e) {
                    console.log(e);
                    fngrtouch.startedTouch = true;
                    fngrtouch.$selection = $(e.target);
                    if (!fngrtouch.deviceIsBlackListed || (!fngrtouch.$selection.is("a") && !fngrtouch.$selection.is("img"))) {
                        fngrtouch.initTimer(e);
                        fngrtouch.lastEvent = e;
                        if (this.enabled) {
                            $(e.target).trigger("fngr:start", e);
                        }
                    }
                });
                $(document).on("touchend", function(e) {
                    console.log("touch end");
                    fngrtouch.clearTimer();
                    fngrtouch.startedTouch = false;
                    if (fngrtouch.enabled) {
                        $(document).trigger("fngr:end", [fngrtouch.lastEvent.originalEvent.touches[0].pageX, fngrtouch.lastEvent.originalEvent.touches[0].pageY]);
                    }
                });
                $(document).on("touchmove", function(e) {
                    console.log("touchmove");
                    fngrtouch.clearTimer();
                    fngrtouch.lastEvent = e;
                    if (fngrtouch.enabled) {
                        $(document).trigger("fngr:move", [e.originalEvent.touches[0].pageX, e.originalEvent.touches[0].pageY, e]);
                    }
                });
                $(document).on("touchcancel", function(e) {
                    console.log(e);
                });
            } else {
                //IGNORE 3DTOUCH
                $(document).on('webkitmouseforcewillbegin', function(e) {
                    e.preventDefault();
                });
                $("select").on('mousedown', function(e) {
                    console.log("select mouse down");
                    fngrtouch.isSelect = true;
                });
                $(".fngrtouchdrag").on("mousedown", function(e) {
                    if (!fngrtouch.isSelect) {
                        console.log("mouse down");
                        fngrtouch.$selection = $(e.target);
                        fngrtouch.initTimer(e);
                        if (fngrtouch.enabled) {
                            //e.preventDefault();
                            $(e.target).trigger("fngr:start", e);
                        }
                    }
                    fngrtouch.isSelect = false;
                });
                $(document).on("mousemove", function(e) {
                    fngrtouch.clearTimer();
                    if (fngrtouch.enabled) {
                        $(document).trigger("fngr:move", [e.pageX, e.pageY, e]);
                    }
                });
                $(document).on("mouseup", function(e) {
                    if (!fngrtouch.ignoreMouseUp) {
                        console.log("mouseup");
                        fngrtouch.clearTimer();
                        if (fngrtouch.enabled) {
                            $(document).trigger("fngr:end", [e.pageX, e.pageY]);
                        }
                    }
                    fngrtouch.ignoreMouseUp = false;
                });
            }
        },
        isMobile: function _isMobile() {
            return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
        },
        initTimer: function _initTimer(ev) {
            if (fngrtouch.fngrpresstimer) {
                clearTimeout(fngrtouch.fngrpresstimer);
            }
            fngrtouch.fngrpresstimer = setTimeout(function() {
                console.log("Tap hold!");
                if (fngrtouch.enabled) {
                    if (fngrtouch.isMobile()) {
                        if (fngrtouch.isScrollStopped) {
                            if (fngrtouch.deviceIsBlackListed) {
                                fngrtouch.enabled = false;
                            }
                            fngrtouch.$selection.trigger("fngr:hold", [fngrtouch.lastEvent.originalEvent.touches[0].pageX, fngrtouch.lastEvent.originalEvent.touches[0].pageY]);
                        }
                    } else {
                        fngrtouch.$selection.trigger("fngr:hold", [ev.pageX, ev.pageY]);
                    }
                }
            }, 400);
        },
        clearTimer: function _clearTimer() {
            clearTimeout(fngrtouch.fngrpresstimer);
        }
    };
} else {
    console.warn("FngrShareSDK: touch.js loaded multiple times");
}