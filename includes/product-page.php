<?php

if (!defined('ABSPATH')) {
    exit;
}

//Add product page instant buy button
wp_enqueue_script('payapi-productpage-js', plugin_dir_url(__FILE__) . '../assets/js/payapi_productpage.js');

add_action('woocommerce_before_main_content', 'payapi_generate_meta_instant_buy');
add_action('woocommerce_after_add_to_cart_button', 'payapi_add_instant_buy_button_product_page');

function payapi_add_instant_buy_button_product_page()
{

    global $post;
    $productId        = $post->ID; 

    include_once 'Payapi_Gateway.php';
    $payapigateway = Payapi_Gateway::single();
    $baseUrl       = $payapigateway->get_request_url(false);
    
    echo '<script type="text/javascript">var baseUrl = "' . $baseUrl . '"; var payapiProductId= '. $productId .'; var startingFromPartial = "'.__('Starting from ##/month','payapi-gateway').'";</script>';    
    echo '<button type="button" class="alt button" id="payapi-button">' . __("Instant Buy", 'payapi-gateway') . '</button>';

    include_once 'check-partialpayments.php';
    $qty = 1;
    if (isset($_GET['quantity'])) {
        $qty = intval($_GET['quantity']);
    }
    $jsonData = ['productId' => $productId, 'qty' => $qty ];
    $partialPay = payapi_check_partial_payments($jsonData);
    echo '<div id="partialpay-container">';
    if($partialPay['code'] == 200){
        $strPartialInitial = str_replace("##", ($partialPay['data']['pricePerMonthInCents'] / 100.0) . ' ' . $partialPay['data']['currency'],__('Starting from ##/month','payapi-gateway'));
        echo '<button type="button" class="alt button" id="payapi-partpay-button"><span>'.$strPartialInitial.'</span></button>';        
    }
    echo '</div>';
}

function getPrices() {   
    $_pf = new WC_Product_Factory();
    $prd = $_pf->get_product($productId);
    
    $prices    = [];
    $prices["0"] = $prd->get_price();
    if ($prd->is_type('variable')) {
        $available_variations = $prd->get_available_variations();
        foreach ($available_variations as $productData) {
            $prices[$productData['id']] = $productData['price'];
        }
    }
    return $prices;
}

function payapi_generate_options_from_query($productId)
{
    $qty = 1;
    if (isset($_GET['quantity'])) {
        $qty = intval($_GET['quantity']);
    }

    $_pf = new WC_Product_Factory();
    $prd = $_pf->get_product($productId);

    $variation_id = 0;
    $variation    = [];
    $params       = $_GET;
    if ($prd->is_type('variable')) {
        $available_variations = $prd->get_available_variations();
        foreach ($available_variations as $value) {
            if (array_intersect_assoc($value['attributes'], $params) == $value['attributes']) {
                return ['qty' => $qty, 'variation_id' => $value['id'], 'variation' => $value['attributes']];
            }
        }
    }
    return ['qty' => $qty];
}

function payapi_generate_meta_instant_buy()
{
    if (isset($_GET['consumerIp'])) {
        global $post;
        include_once 'Payapi_Gateway.php';
        include_once 'Payapi_SecureFormGenerator.php';
        $productId        = $post->ID;        
        $isPartialPaymentUrl = $_GET['partialPayment'] == "1";
        $opts             = payapi_generate_options_from_query($productId);
        $payapigateway    = Payapi_Gateway::single();
        $secureFormHelper = new Payapi_SecureFormGenerator($payapigateway);
        $secureFormData   = $secureFormHelper->getInstantBuySecureForm($productId, $opts, $_GET['consumerIp']);

        $partialPay = $payapigateway->getPhpSdk()->partialPayment($secureFormData['products'][0]['priceInCentsIncVat'] * $secureFormData['products'][0]['quantity'], $secureFormData['order']['currency'], $_GET['consumerIp']);
        
        echo '<meta name="io.payapi.webshop" content="'.$payapigateway->get_payapi_public_id().'">';        
        echo '<meta name="order.currency" content="'.$secureFormData['order']['currency'].'">';
        echo '<meta name="order.shippingHandlingFeeInCentsExcVat" content="'.$secureFormData['products'][1]['priceInCentsExcVat'].'">';

        if ($partialPay && $partialPay['code'] == 200 && $isPartialPaymentUrl) {
            echo '<meta name="order.preselectedPartialPayment" content="'.$partialPay['data']['paymentMethod'].'">';
        }

        echo '<meta name="order.shippingHandlingFeeInCentsIncVat" content="'.$secureFormData['products'][1]['priceInCentsIncVat'].'">';
        echo '<meta name="order.tosUrl" content="'.$secureFormData['order']['tosUrl'].'">';
        echo '<meta name="product.id" content="'.$secureFormData['products'][0]['id'].'">';
        echo '<meta name="product.quantity" content="'.$secureFormData['products'][0]['quantity'].'">';
        echo '<meta name="product.title" content="'.$secureFormData['products'][0]['title'].'">';
        echo '<meta name="product.imageUrl" content="'.$secureFormData['products'][0]['imageUrl'].'">';
        echo '<meta name="product.priceInCentsIncVat" content="'.$secureFormData['products'][0]['priceInCentsIncVat'].'">';
        echo '<meta name="product.priceInCentsExcVat" content="'.$secureFormData['products'][0]['priceInCentsExcVat'].'">';
        echo '<meta name="product.vatInCents" content="'.$secureFormData['products'][0]['vatInCents'].'">';
        echo '<meta name="product.vatPercentage" content="'.$secureFormData['products'][0]['vatPercentage'].'">';
        echo '<meta name="product.hasMandatoryFields" content="' . 0 .'">';
        echo '<meta name="consumer.email" content="'.$secureFormData['consumer']['email'].'">';
        echo '<meta name="consumer.locale" content="'.$secureFormData['consumer']['locale'].'">';
        echo '<meta name="callbacks.processing" content="'.$secureFormData['callbacks']['processing'].'">';
        echo '<meta name="callbacks.success" content="'.$secureFormData['callbacks']['success'].'">';
        echo '<meta name="callbacks.failed" content="'.$secureFormData['callbacks']['failed'].'">';
        echo '<meta name="callbacks.chargeback" content="'.$secureFormData['callbacks']['chargeback'].'">';
        echo '<meta name="returnUrls.success" content="'.$secureFormData['returnUrls']['success'].'">';
        echo '<meta name="returnUrls.cancel" content="'.$secureFormData['returnUrls']['cancel'].'">';
        echo '<meta name="returnUrls.failed" content="'.$secureFormData['returnUrls']['failed'].'">';
        echo '<meta name="product.extraData" content="'.$secureFormData['products'][0]['extraData'].'">';
     
    }
}
