<?php

if (!defined('ABSPATH')) {
    exit;
}

function payapi_check_partial_payments($request)
{
    $productId   = isset($request['productId']) ? $request['productId'] : false;
    $variationId = isset($request['variationId']) ? $request['variationId'] : false;
    $qty         = isset($request['qty']) ? $request['qty'] : 1;

    if (!$productId) {
        return false;
    }
    
    $gateway = Payapi_Gateway::single();

    $cart  = WC()->cart;
    $items = WC()->cart->get_cart();
    $cart->empty_cart();

    $cart->add_to_cart($productId, $qty, $variationId);

    WC()->shipping->reset_shipping();
    WC()->session->set('payapi_generate_metas',1);
    $cart->calculate_shipping();
    WC()->session->__unset('payapi_generate_metas');

    $shippingExcVat = $cart->shipping_total;
    $baseExclTax    = $cart->subtotal_ex_tax;
    $taxAmount      = $cart->get_taxes_total();
    $total          = $baseExclTax + $taxAmount + $shippingExcVat;
    //Restore cart
    $cart->empty_cart();
    foreach ($items as $item => $values) {
        $cart->add_to_cart($values['product_id'], $values['quantity'], $values['variation_id'], $values['variation'], $values);
    }

    $partialPay = $gateway->getPhpSdk()->partialPayment(round($total * 100), get_woocommerce_currency());
    return $partialPay;
}
