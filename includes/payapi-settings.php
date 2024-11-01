<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings for PayApi Gateway.
 */
$this->form_fields = array(

    'enabled'                                    => array(
        'title'   => __('Enable/Disable', 'payapi-gateway'),
        'type'    => 'checkbox',
        'label'   => __('Enable Gateway', 'payapi-gateway'),
        'default' => 'yes',
    ),
    'payapi_public_id'                           => array(
        'title'       => __('Public Id', 'payapi-gateway'),
        'type'        => 'text',
        'description' => __('Get your API credentials from your payments Backoffice.', 'payapi-gateway'),
        'default'     => '',
        'desc_tip'    => true,
    ),
    'payapi_api_key'                             => array(
        'title'       => __('Api Key', 'payapi-gateway'),
        'type'        => 'password',
        'description' => __('Get your API credentials from your payments Backoffice.', 'payapi-gateway'),
        'default'     => '',
        'desc_tip'    => true,
    ),
    'staging'                                    => array(
        'title'   => __('Staging', 'payapi-gateway'),
        'type'    => 'checkbox',
        'label'   => __('Enable staging mode', 'payapi-gateway'),
        'default' => 'no',
    ),
    'instant_buy'                                => array(
        'title'   => __('Instant Buy', 'payapi-gateway'),
        'type'    => 'checkbox',
        'label'   => __('Enable Instant Buy', 'payapi-gateway'),
        'default' => 'yes',
    ),
    'default_instant_shipping'                   => array(
        'title'   => __('Default shipping method for Instant Buy', 'payapi-gateway'),
        'type'    => 'select',
        'class'   => 'wc-enhanced-select',
        'options' => payapi_get_active_shipping_methods(),
    ),
    'payapi_selector_product_container_listings' => array(
        'title'   => __('jQuery Selector for product container in listings', 'payapi-gateway'),
        'type'    => 'text',
        'default' => '',
    ),
    'payapi_selector_main_page_container'        => array(
        'title'   => __('jQuery Selector for page container', 'payapi-gateway'),
        'type'    => 'text',
        'default' => '',
    ),

);
