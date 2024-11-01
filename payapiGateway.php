<?php
/**
 * Plugin Name: SoMe Pay Social Shopping 
 * Plugin URI: https://wordpress.org/plugins/some-pay-social-shopping/
 * Description: SoMe Pay Social Shopping by Nets includes a new payment gateway for the checkout process through the Secure-Form
 * Version: 1.1.0
 * Author: PayApi
 * Author URI: https://somepay.fi
 * Developer: Your Name
 * Developer URI: http://payapi.io/
 * Text Domain: woocommerce-extension
 * Domain Path: /lang
 *
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<?php

require_once __DIR__ . "/vendor/autoload.php";

if (!defined('ABSPATH')) {
    exit;
}

use \Firebase\JWT\JWT;
/**
 * Payapi_Gateway init
 */

add_action('plugins_loaded', 'payapi_woocommerce_init', 0);

function payapi_woocommerce_init()
{

    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    load_plugin_textdomain('payapi-gateway', false, dirname(plugin_basename(__FILE__)) . '/lang/');

    function payapi_get_active_shipping_methods()
    {
        global $woocommerce;
        $active_methods   = array();
        $shipping_methods = $woocommerce->shipping->get_shipping_methods();
        foreach ($shipping_methods as $id => $shipping_method) {
            if (isset($shipping_method->enabled) && 'yes' === $shipping_method->enabled) {
                $active_methods[$id] = $shipping_method->method_title;
            }
        }

        return $active_methods;
    }

    require_once 'includes/Payapi_Gateway.php';
    /**
     * Add the Gateway to WooCommerce
     **/
    function payapi_add_gateway($methods)
    {
        $methods[] = 'Payapi_Gateway';
        return $methods;
    }

    function payapi_generate_fngr_config()
    {
        $payapigateway = Payapi_Gateway::single();
        echo '<script type="text/javascript"> ' .
        'var payapi_selector_product_container_listings ="' . $payapigateway->getProductContainerListingsSelector() . '"; '
        . 'var payapi_selector_main_container ="' . $payapigateway->getMainContainerSelector() . '"; '
        . 'var payapiCurrency = "' . get_woocommerce_currency() . '"; '
        . 'var instantbuy_string = "' . __("Instant Buy", 'payapi-gateway') . '"; '
        . 'if (typeof _payapi == "undefined") { '
        . ' var _payapi = {}; '
        . '}; '
        . '_payapi.addtocart = function _addtocart(url){ '
        . '    $dataContainer = $("[fngrsharesdk-product-container=\""+url+"\"]").find("[data-product-id]"); '
        . '    if($dataContainer.length > 0){ '
        . '        var productId = $dataContainer.attr("data-product-id"); '
        . '        var addCartUrl = window.location.href + ((window.location.href.indexOf("?")<0)?"?":"&") +"add-to-cart="+productId; '
        . '        $.post({ url: "/wp-json/payapi-gateway/v1/addtocart", data: {"productId": productId} }).done(function(){location.reload();}); '
        . '    }else if ($("button[name=\"add-to-cart\"]").length > 0){ '
        . '        $("button[name=\"add-to-cart\"]").first().click(); '
        . '    }else{ '
        . '    $dataContainer = $("[fngrsharesdk-product-container=\""+url+"\"]").find(".ajax_add_to_cart"); '
        . '    if($dataContainer.length > 0){ '
        . '        $dataContainer.click(); '
        . '    }else{ '
        . '        alert("Product was not added to the cart"); '
        . '}'
        . '    } '
        . '  }; ' .
        'var _fngrshareConfig = {' .
        'isStaging: function _isStaging(){' .
        'return ' . $payapigateway->get_is_staging() . ';' .
        '},' .
        'enablePurchases: function _enablePurchases(){' .
        'return ' . $payapigateway->get_is_instant_buy() . ';' .
        '},' .
        'shortenerClientId: function _shortenerClientId(){' .
        'return "' . $payapigateway->get_payapi_public_id() . '";' .
            '}' .
            '};' .
            '</script>';
    }

    function payapi_get_partial_payment_from_cart()
    {
        global $woocommerce;
        $gateway        = Payapi_Gateway::single();
        $cart           = $woocommerce->cart;
        $shippingExcVat = 0;
        $discountExcVat = 0;
        $shippingTax    = 0;

        $cart->calculate_shipping();

        $shippingExcVat = $cart->shipping_total;
        $discountExcVat = $cart->discount_cart;

        $baseExclTax  = $cart->subtotal_ex_tax;
        $taxAmount    = $cart->get_taxes_total();
        $totalOrdered = $baseExclTax + $taxAmount + $shippingExcVat - $discountExcVat;

        $partialPay = $gateway->getPhpSdk()->partialPayment(round($totalOrdered * 100), get_woocommerce_currency());
        return $partialPay;
    }

    function payapi_generate_mini_cart_button()
    {
        if (!WC()->cart->is_empty()) {
            echo '<a rel="nofollow" class="cart-instantbuy-button checkout button">' . __("Instant Buy", 'payapi-gateway') . '</a>';
            $partialPay = payapi_get_partial_payment_from_cart();
            if ($partialPay && $partialPay['code'] == 200) {
                $strPartialP = str_replace("1.1.0", ($partialPay['data']['pricePerMonthInCents'] / 100.0) . ' ' . $partialPay['data']['currency'], __('Starting from 1.1.0/month', 'payapi-gateway'));

                echo '<a rel="nofollow" style="margin-bottom:10px" class="cart-instantbuy-button payapi-partial checkout button">' . $strPartialP . '</a>';
            }
        }
    }

    add_filter('woocommerce_payment_gateways', 'payapi_add_gateway');

    /**
     * Add scripts
     **/

    $payapigateway = Payapi_Gateway::single();
    if ($payapigateway->is_available()) {
        add_action('wp_head', 'payapi_generate_fngr_config');
        add_action('woocommerce_after_mini_cart', 'payapi_generate_mini_cart_button');
        add_filter('wp_enqueue_scripts', 'payapi_incjs_checkout');

    }

    function payapi_incjs_checkout()
    {
        $payapigateway = Payapi_Gateway::single();
        if (is_checkout()) {
            $urlJs = plugin_dir_url(__FILE__) . 'assets/js/payapi_checkout.js';
            wp_enqueue_script('payapi-checkout-js', $urlJs);
        }
        wp_enqueue_style('fngrsharesdk-css', plugin_dir_url(__FILE__) . 'assets/fngrsharesdk/sdk.css');
        wp_enqueue_script('fngrsharesdk-touch-js', plugin_dir_url(__FILE__) . 'assets/fngrsharesdk/touch.js');
        wp_enqueue_script('fngrsharesdk-ui-js', plugin_dir_url(__FILE__) . 'assets/fngrsharesdk/sdk-ui.js');
        wp_enqueue_script('fngrsharesdk-controller-js', plugin_dir_url(__FILE__) . 'assets/fngrsharesdk/sdk-controller.js');

        wp_enqueue_script('payapi-generic-js', plugin_dir_url(__FILE__) . 'assets/js/payapi_generic.js');

        if (is_product()) {
            include_once 'includes/product-page.php';
        }

        if (is_cart()) {
            include_once 'includes/cart-page.php';
        }

        wp_enqueue_script('payapi-cartpage-js', plugin_dir_url(__FILE__) . 'assets/js/payapi_cart.js');
    }

/**
 * This function is where we register our routes for our example endpoint.
 */
    function payapi_register_routes()
    {
        register_rest_route('payapi-gateway/v1', '/secureformgenerator', array(
            'methods'  => 'POST',
            'callback' => 'payapi_register_secureformgenerator_cb',
        ));

        register_rest_route('payapi-gateway/v1', '/callback', array(
            'methods'  => 'POST',
            'callback' => 'payapi_register_callback_cb',
        ));

        register_rest_route('payapi-gateway/v1', '/checkpartialpayments', array(
            'methods'  => 'POST',
            'callback' => 'payapi_register_checkpartialpayment_cb',
        ));

        register_rest_route('payapi-gateway/v1', '/addtocart', array(
            'methods'  => 'POST',
            'callback' => 'payapi_register_addtocart_cb',
        ));
    }

    function payapi_register_secureformgenerator_cb($request)
    {
        include_once 'includes/Payapi_SecureFormGenerator.php';
        include_once 'includes/Payapi_Callback.php';

        $address          = false;
        $payapigateway    = Payapi_Gateway::single();
        $secureFormHelper = new Payapi_SecureFormGenerator($payapigateway);
        $hasPartial       = isset($request['partialPaymentsEnabled']);
        if (isset($request['payapidata'])) {
            //From checkout
            $shipping_method = $request['payapidata']['shipping_method[0'];
            if (!isset($request['payapidata']['ship_to_different_address'])) {
                $address = [
                    'first_name'     => $request['payapidata']['billing_first_name'],
                    'last_name'      => $request['payapidata']['billing_last_name'],
                    'company'        => $request['payapidata']['billing_company'],
                    'email'          => $request['payapidata']['billing_email'],
                    'phone'          => $request['payapidata']['billing_phone'],
                    'address_1'      => $request['payapidata']['billing_address_1'],
                    'address_2'      => $request['payapidata']['billing_address_2'],
                    'city'           => $request['payapidata']['billing_city'],
                    'state'          => $request['payapidata']['billing_state'],
                    'postcode'       => $request['payapidata']['billing_postcode'],
                    'country'        => $request['payapidata']['billing_country'],
                    'order_comments' => $request['payapidata']['order_comments'],
                ];
            } else {
                $address = [
                    'first_name'     => $request['payapidata']['shipping_first_name'],
                    'last_name'      => $request['payapidata']['shipping_last_name'],
                    'company'        => $request['payapidata']['shipping_company'],
                    'email'          => $request['payapidata']['billing_email'],
                    'phone'          => $request['payapidata']['billing_phone'],
                    'address_1'      => $request['payapidata']['shipping_address_1'],
                    'address_2'      => $request['payapidata']['shipping_address_2'],
                    'city'           => $request['payapidata']['shipping_city'],
                    'state'          => $request['payapidata']['shipping_state'],
                    'postcode'       => $request['payapidata']['shipping_postcode'],
                    'country'        => $request['payapidata']['shipping_country'],
                    'order_comments' => $request['payapidata']['order_comments'],
                ];
            }

        }

        $payapiApiKey = $payapigateway->get_option('payapi_api_key');
        $payapiObject = $secureFormHelper->postSecureForm($address, false, $hasPartial);
        try {
            $strSigned = JWT::encode($payapiObject, $payapiApiKey);
            return new WP_REST_Response(["url" => $secureFormHelper->get_request_url(), "data" => $strSigned], 200);
        } catch (Exception $err) {
            return new WP_REST_Response(["msg" => "Wrong data decoding. Please review PayApi data in your backoffice", "platform" => "woocommerce", "status" => "wrong_signature"], 400);
        }

    }

    function payapi_register_callback_cb($request)
    {
        include_once 'includes/Payapi_Callback.php';
        $data = $request['data'];
        if (isset($data)) {
            Payapi_Gateway::log("Data: " . $data);
            $payapigateway = Payapi_Gateway::single();
            $payapiApiKey  = $payapigateway->get_payapi_api_key();
            try {
                $decoded = JWT::decode($data, $payapiApiKey, ['HS256']);
                return Payapi_Callback::process(json_decode($decoded));
            } catch (Exception $err) {
                return new WP_REST_Response(["msg" => "Wrong data decoding. Please review PayApi data in your backoffice", "platform" => "woocommerce", "status" => "wrong_signature"], 400);
            }
        }
        return new WP_REST_Response(["msg" => "Missing or wrong PayApi parameters", "platform" => "woocommerce", "status" => "bad_request"], 400);
    }

    function payapi_register_checkpartialpayment_cb($request)
    {
        include_once 'includes/check-partialpayments.php';
        $partial = payapi_check_partial_payments($request);
        if ($partial) {
            return new WP_REST_Response($partial, 200);
        }
        return new WP_REST_Response(["msg" => "Missing or wrong parameters", "platform" => "woocommerce", "status" => "bad_request"], 400);
    }

    function payapi_register_addtocart_cb($request)
    {
        $productId = isset($request['productId']) ? $request['productId'] : false;
        $qty       = isset($request['qty']) ? $request['qty'] : 1;
        if ($productId && WC()->cart->add_to_cart($productId, $qty)) {
            wc_add_to_cart_message($productId);
        } else {
            wc_add_notice('Product not added to cart', 'error');
        }

        return new WP_REST_Response([], 200);
    }

    function payapi_create_redirect_pages()
    {
        $pages        = get_pages();
        $contact_page = array('slug' => 'payapi-redirect', 'title' => 'Redirect page');
        $found        = false;
        foreach ($pages as $page) {
            $apage = $page->post_name;
            switch ($apage) {
                case 'payapi-redirect':$found = true;
                    break;
                default:$no_page;
            }
        }

        if (!$found) {
            $page_id = wp_insert_post(array(
                'post_title'   => $contact_page['title'],
                'post_type'    => 'page',
                'post_name'    => $contact_page['slug'],
                'post_status'  => 'publish',
                'post_excerpt' => 'User profile and author page details page ! ',
            ));
            add_post_meta($page_id, '_wp_page_template', plugin_dir_path(__FILE__) . 'pages/redirect.php');
        }
    }

    function payapi_locate_template($template_name)
    {

        $default_path = plugin_dir_path(__FILE__) . 'templates/'; // Path to the template folder
        $template     = $default_path . $template_name;

        return apply_filters('payapi_locate_template', $template, $template_name, '', $default_path);
    }

    function payapi_template_loader($template)
    {
        global $post;
        if ($post->post_name == 'payapi-redirect') {
            $file = 'redirect.php';
            if (file_exists(payapi_locate_template($file))) {
                $template = payapi_locate_template($file);
                return $template;
            }
        }

        return $template;
    }

    add_filter('template_include', 'payapi_template_loader');
    add_action('admin_init', 'payapi_create_redirect_pages');

    add_action('rest_api_init', 'payapi_register_routes');

// define the woocommerce_before_shop_loop_item callback
    function payapi_action_woocommerce_before_shop_loop_item()
    {
        global $product;
        $gateway = Payapi_Gateway::single();
        $partial = $gateway->getPhpSdk()->partialPayment(round($product->get_price() * 100), get_woocommerce_currency());
        if ($partial && $partial['code'] == 200) {
            echo '<meta name="payapi-partial-payment" content="1">';
        }
    };

// add the action
    add_action('woocommerce_before_shop_loop_item', 'payapi_action_woocommerce_before_shop_loop_item', 10, 0);

    function payapi_action_partial_payment_to_checkout()
    {
        $partialPay = payapi_get_partial_payment_from_cart();
        if ($partialPay && $partialPay['code'] == 200) {
            $strPartialInitial = str_replace("1.1.0", ($partialPay['data']['pricePerMonthInCents'] / 100.0) . ' ' . $partialPay['data']['currency'], __('Starting from 1.1.0/month', 'payapi-gateway'));
            echo '<button style="margin-top:10px;" type="button" class="alt button" id="payapi-checkout-partial"><span>' . $strPartialInitial . '</span></button>';
        }
    }

    add_action('woocommerce_review_order_after_submit', 'payapi_action_partial_payment_to_checkout');

    function use_payapi_default_shipping_method($method, $available_methods)
    {
        if (WC()->session->get('payapi_generate_metas')) {
            $payapigateway  = Payapi_Gateway::single();
            $default_method = $payapigateway->default_instant_shipping;
            foreach ($available_methods as $method_id => $method_body) {
                if (substr($method_id, 0, strlen($default_method) + 1) === $default_method . ":") {
                    return $method_id;
                }

            }
        }
        return $method;
    }
    add_filter('woocommerce_shipping_chosen_method', 'use_payapi_default_shipping_method', 10, 2);
}
