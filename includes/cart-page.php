<?php

if (!defined('ABSPATH')) {
    exit;
}

//Add product page instant buy button

add_action('woocommerce_after_cart_table', 'payapi_add_instant_buy_button_cart_page');

function payapi_add_instant_buy_button_cart_page()
{
	echo '<script type="text/javascript"> var changingShipping=false; $("input:radio[name^=shipping_method]").change(function(){ changingShipping=true; }); $( document.body ).on("updated_cart_totals", function(){ if(!changingShipping) { location.reload(); } else { console.log("UPDATECART"); $("input[name=update_cart]").removeAttr("disabled"); $("input[name=update_cart]").click(); location.reload(); } changingShipping = false;}); </script>';
    echo '<a href="#" style="margin-bottom:10px;width:100%" id="cart-instantbuy-button" class="cart-instantbuy-button checkout-button button">' . __("Instant Buy", "payapi-gateway") . '</a>';
    
    $partialPay = payapi_get_partial_payment_from_cart();
    if($partialPay && $partialPay['code'] == 200) {
    	$strPartialP = str_replace("##", ($partialPay['data']['pricePerMonthInCents'] / 100.0) . ' ' . $partialPay['data']['currency'],__('Starting from ##/month','payapi-gateway'));
    	echo '<a href="#" style="margin-bottom:10px;width:100%" id="cart-instantbuy-button-partial" class="cart-instantbuy-button payapi-partial checkout-button button">' . $strPartialP . '</a>';
    }
}
