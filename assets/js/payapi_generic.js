(function($) {
    $(document).ready(function() {
        var mainContentSelector = "#page,#main,#primary";
        if(typeof payapi_selector_main_container !== 'undefined' && payapi_selector_main_container != "") mainContentSelector = mainContentSelector + ","+payapi_selector_main_container;
        $(mainContentSelector).addClass("fngrsharesdk-container");

        var containerSelector = ".product.type-product,.animate";        
        if(typeof payapi_selector_product_container_listings !== 'undefined' && payapi_selector_product_container_listings != "") containerSelector = containerSelector + ","+payapi_selector_product_container_listings;
        $(containerSelector).find("a").each(function() {            
            var href = $(this).attr('href');
            $container = $(this).closest(containerSelector);  
            var hasPartialPayment = $container.find('meta[name="payapi-partial-payment"]').length > 0;          
            $parents = $(this).parents(containerSelector);            
            var isSingleProduct = $parents.length <= 1;

            if ((typeof __isProductPage == "undefined" || !__isProductPage || (__isProductPage && !isSingleProduct)) && $container.find(".instant_buy_button").length == 0) {
                $(this).addClass("fngrsharesdk-product");

                var separator = "&";
                    if (href.indexOf("?") < 0) {
                        separator = "?";
                    }

                if ($container.hasClass("outofstock") || $container.hasClass("product-type-variable") || $container.find(".outofstock").length > 0 
                    || $container.find(".product-type-variable").length > 0 || $container.find(".product_type_variable").length > 0 || $container.hasClass("product-type-external")) {
                    
                    $container.attr("fngrsharesdk-product-container", href + separator + "hasMandatory=1" + ((hasPartialPayment)?"&partialPaymentsEnabled=1":"") + "&currency="+payapiCurrency);
                    $container.append("<p><a href='" + href + "' class='instant_buy_button alt button'>"+instantbuy_string+"</a></p>");
                } else {
                    $container.attr("fngrsharesdk-product-container", href + separator + "partialPaymentsEnabled=" +((hasPartialPayment)? "1":"0") + "&currency="+payapiCurrency);
                    var domain = (_fngrshareConfig.isStaging()) ? "staging-input.payapi.io" : "input.payapi.io";
                    var secureFormUrl = 'https://' + domain + '/v1/webshop/' + encodeURIComponent(href+ separator +"currency="+payapiCurrency);
                    $container.append("<p><a href='" + secureFormUrl + "' class='instant_buy_button alt button'>"+instantbuy_string+"</a></p>");                    
                }
            }
        });
    });
})(jQuery);