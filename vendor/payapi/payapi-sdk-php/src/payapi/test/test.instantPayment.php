<?php


$product = array (
  "product"                => array (
    "id"                   => 'ref87567'   ,
    "url"                  => 'https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=43' ,
    "title"                => 'Product 1'  ,
    "imageUrl"             => 'https://store.payapi.io/media/43307ac7f356d51e6dd65b8ca9fe3d93/image/cache/catalog/Users/User4/payapi_premium_support-228x228.jpg' ,
    "category"             => 'category 1' ,
    "priceInCentsExcVat"   => 20000        ,
    "priceInCentsIncVat"   => 24000        ,
    "options"              => array (
      "color"              => 'blue' ,
      "size"               => 'XXL'
    ) ,
    "quantity"             => 1
  ) ,
  "order"           => array (
    "currency"      => 'EUR'
  ) ,
  "callbacks"       => array (
    "processing"    => 'https://api.example.com/callback-processing' ,
    "success"       => 'https://api.example.com/callback-success' ,
    "failed"        => 'https://api.example.com/callback-failed' ,
    "chargeback"    => 'https://api.example.com/callback-chargeback'
  ) ,
  "returnUrls"      => array (
    "success"       => 'https://store.example.com/payment-success' ,
    "cancel"        => 'https://store.example.com/payment-cancel' ,
    "failed"        => 'https://store.example.com/payment-failed' ,
    "extraInput"    => 'https://store.example.com/payment-failed'
  )
) ;

$test = $sdk -> instantPayment ( $product ) ;
