if (typeof _payapi == "undefined") {
    var _payapi = {};
}
var __isProductPage = true;
_payapi.checkMandatory = function _checkMandatory(url, silencePopup, msg, partial = false) {
    var isValid = isValidForm(silencePopup);
    
    if (!isValid) {
        return '';
    }
    //Add product options to the currUrl
    var $toCartForm = $("form.cart");
    var opts = decodeURIComponent($toCartForm.find(":input:not(:hidden)").serialize());
    
    if(partial) {
        console.log("Is partial");
          opts = opts + "&partialPayment=1";
    }
    if(url.indexOf("currency")<0) {
        opts = opts +"&currency="+payapiCurrency;
    }
    
    var separator = "&";
    if (url.indexOf("?") < 0) {
        separator = "?";
    }


    if (silencePopup) {
        return url + separator + opts;
    }
    var productUrl = baseUrl;
    productUrl = productUrl + encodeURIComponent(url + separator + opts);
    window.location = productUrl;
};

_payapi.partialPaymentEnabledForProduct = function _partialPayment(url){
      //Returns true if the product received as url has partial payment enabled      
      return $("#payapi-partpay-button").length > 0;
    };

function isValidForm(silencePopup) {
    var msg = "";
    var $toCartForm = $("form.cart");
    var $addToCartBtn = $(".single_add_to_cart_button");
    if ($addToCartBtn.is('.disabled')) {
        if ($addToCartBtn.is('.wc-variation-is-unavailable')) {
            msg = wc_add_to_cart_variation_params.i18n_unavailable_text;
        } else if ($addToCartBtn.is('.wc-variation-selection-needed')) {
            msg = wc_add_to_cart_variation_params.i18n_make_a_selection_text;
        }
    }
    if (msg == "") {
        //Check stock
        var $qtyField = $("input[name='quantity']");
        var minStock = parseInt($qtyField.attr('min'));
        var maxStock = parseInt($qtyField.attr('max'));
        var qty = parseInt($qtyField.val());
        if (minStock > qty || maxStock < qty) {
            msg = "No stock";
            $addToCartBtn.click();
            return false;
        }
    }
    if ($addToCartBtn.length == 0) {
        msg = "Product out of stock";
    }
    if (msg != "" && !silencePopup) {
        alert(msg);
    }
    return (msg == "");
}

function updatePartialPayments(){

    var $qtyField = $("input[name='quantity']");
    var minStock = parseInt($qtyField.attr('min'));
    var maxStock = parseInt($qtyField.attr('max'));
    var qty = parseInt($qtyField.val());
    if (minStock > qty || maxStock < qty) {            
        return false;
    }
    
    var variationId = $("input[name='variation_id']").attr('value');
    var jsonPostData = {'qty' : qty, 'productId': payapiProductId};
    if($("input[name='variation_id']").length == 0 || variationId != ""){
        if(variationId != "") jsonPostData['variationId'] = variationId;    
            $.ajax({
                showLoader: true,
                type: "POST",
                url: "/wp-json/payapi-gateway/v1/checkpartialpayments",
                data: jsonPostData,
                success: function(object) {
                    
                        if(object.code == 200){
                          if($("#payapi-partpay-button").length == 0){
                            $("#partialpay-container").append(
                              $(document.createElement('button'))
                          .addClass('alt button').attr('id','payapi-partpay-button')                          
                          .click(function(evt) {
                            evt.preventDefault();
                            _payapi.checkMandatory(location.href,false,'',true);
                          }).append($(document.createElement('span'))));

                          }                          
                          var strPartial = startingFromPartial.replace("##",(object.data.pricePerMonthInCents/100.0)+' '+object.data.currency);
                          $("#payapi-partpay-button span:first-child").text(strPartial);
                        } else {
                          $("#payapi-partpay-button").remove();
                        }
                }
            });
        }
}

$(document).ready(function() {
    var $btnClicked = $("#payapi-button");
    var $btnPartial = $("#payapi-partpay-button");
    
    if ($("button.single_add_to_cart_button").length > 0 && $btnClicked.closest(".outofstock").length == 0){        
        $(".fngrsharesdk-container").addClass("fngrsharesdk-productpage");
        $btnClicked.click(function(evt) {
            _payapi.checkMandatory(location.href, false);
        });        
        
        var $toCartForm = $("input[name='variation_id'],input[name='quantity']");
        $toCartForm.on('change',function(evt){ 
            updatePartialPayments();
        });

        $btnPartial.click(function(evt) {
            _payapi.checkMandatory(location.href, false,"",true);
        });

    } else {
        $btnClicked.remove();
        $btnPartial.remove();
    }
});