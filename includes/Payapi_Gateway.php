<?php

use Payapi\PaymentsSdk\payapiSdk;

class Payapi_Gateway extends WC_Payment_Gateway
{
    public static $single = false;

    /** @var bool Whether or not logging is enabled */
    public static $log_enabled = true;

    /** @var WC_Logger Logger instance */
    public static $log = false;

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        $this->id           = 'payapisecureform';
        $this->has_fields   = true;
        $this->method_title = __('PayApi Online Secure Payment', 'payapi-gateway');

        $this->title              = $this->method_title;
        $this->description        = __('Secure Online and Mobile Payments', 'payapi-gateway');
        

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables.
        $this->payapi_public_id = $this->get_option('payapi_public_id');
        $this->payapi_api_key   = $this->get_option('payapi_api_key');
        $this->enabled          = $this->get_option('enabled');
        $this->staging          = 'yes' === $this->get_option('staging');
        $this->instant_buy      = 'yes' === $this->get_option('instant_buy');
        $this->default_instant_shipping = $this->get_option('default_instant_shipping');
        
        if (!$this->initPhpSdk()){
            $this->enabled = false;
        }
        $branding = $this->getPhpSdk()->branding();
        if($branding && $branding['code'] == 200) {
            Payapi_Gateway::log("BRANDING.  ".json_encode($branding));
            $this->method_title = $branding['data']['partnerName'];
            $this->title = $this->method_title;
            $this->description = $branding['data']['partnerSlogan'];            
        }

        $this->method_description = $this->title .' - '.$this->description. ' ' . sprintf(__('includes a new payment gateway for the checkout process through the Secure-Form. In order to use the payment gateway, please register for a free %suser account%s', 'payapi-gateway'), '<a href="https://input.payapi.io">', '</a>');

        if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
        } else {
            add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
        }

    }

    public function process_admin_options() {
        //CALLSUPER        
        $this->initPhpSdk(true);
        parent::process_admin_options();
    }

    public function get_payapi_public_id()
    {
        return $this->payapi_public_id;
    }

    public function get_payapi_api_key()
    {
        return $this->payapi_api_key;
    }

    public function get_is_staging()
    {
        return $this->staging;
    }

    public function get_is_instant_buy()
    {
        return $this->instant_buy;
    }

    public function get_is_enabled()
    {
        return $this->enabled;
    }

    /**
     * Get the Secure Form request URL.
     * @return string
     */
    public function get_request_url($isPostEndpoint = true)
    {
        $baseUrl = 'https://input.payapi.io/v1/';

        if ($this->staging) {
            $baseUrl = 'https://staging-input.payapi.io/v1/';
        }

        if ($isPostEndpoint) {
            return $baseUrl . 'secureform/' . $this->payapi_public_id;
        } else {
            return $baseUrl . 'webshop/';
        }
    }

    /**
     * Logging method.
     * @param string $message
     */
    public static function log($message)
    {
        if (self::$log_enabled) {
            if (empty(self::$log)) {
                self::$log = new WC_Logger();
            }
            self::$log->add('payapi', $message);
        }
    }

    /**
     * Check if this gateway is enabled and available in the user's country.
     * @return bool
     */
    public function is_valid_for_use()
    {
        return $this->enabled && $this->get_payapi_public_id() != "" && $this->get_payapi_api_key() != "";
    }

    public function is_available()
    {

        return $this->enabled && $this->get_payapi_public_id() != "" && $this->get_payapi_api_key() != "";
    }

    /**
     * Admin Panel Options.
     * - Options for bits like 'title' and availability on a country-by-country basis.
     *
     * @since 1.0.0
     */
    public function admin_options()
    {
        parent::admin_options();
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        include 'payapi-settings.php';
    }

    /**
     * Can the order be refunded via PayApi
     * @param  WC_Order $order
     * @return bool
     */
    public function can_refund_order($order)
    {
        return false;
    }

    public function get_icon()
    {
        $branding = $this->getPhpSdk()->branding();
        if($branding && $branding['code'] == 200) {
            $icon_html = '<img style="margin:4px; max-height: 50px;" src="' . esc_attr($branding['data']['partnerIconUrl']) . '"  />';
            return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
        }
    }

    public function getProductContainerListingsSelector()
    {
        return $this->get_option('payapi_selector_product_container_listings');
    }

    public function getMainContainerSelector()
    {
        return $this->get_option('payapi_selector_main_page_container');
    }

    public static function single()
    {
        if (self::$single === false) {
            self::$single = new self();
        }
        return self::$single;
    }

    private function initPhpSdk($refreshMerchantSettings = false) {
        $config = ["debug" => true];            
            if (!isset($this->sdk)) {
                $this->sdk = new payapiSdk($config, 'native', false);
            }            
            if ($refreshMerchantSettings) {
                $resp = $this->sdk->settings($this->staging, $this->payapi_public_id, $this->payapi_api_key);
            } else {
                $resp = $this->sdk->settings();
                if ($resp['code'] != 200) {
                    Payapi_Gateway::log('REFRESHING MERCHANT SETTINGS. NOT CACHED OR EXPIRED ');
                    $resp = $this->sdk->settings($this->staging, $this->payapi_public_id, $this->payapi_api_key);
                }
            }            
            return $resp['code'] == 200;
    }

    public function getPhpSdk() {
        if(!isset($this->sdk)){            
            $this->initPhpSdk();            
        }
        return $this->sdk;
    }

}
