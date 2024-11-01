<?php


$product = array (
  "products"                => array (
    array (
      "id"                   => 'ref87567' . rand ( 1000 , 9999 )   ,
      "url"                  => 'https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=41' ,
      "title"                => 'Product 1'  ,
      "imageUrl"             => 'https://store.payapi.io/media/43307ac7f356d51e6dd65b8ca9fe3d93/image/cache/catalog/Users/User4/payapi_premium_support-228x228.jpg' ,
      "category"             => 'category 1' ,
      "priceInCentsExcVat"   => 20000        ,
      "priceInCentsIncVat"   => 24000        ,
      "quantity"             => 1            ,
      "options"              => array (
        "color"              => 'blue'       ,
        "size"               => 'XXL'
      )
    ) ,
    array (
      "id"                   => 'ref87568' . rand ( 1000 , 9999 ) ,
      "url"                  => 'https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=42' ,
      "title"                => 'Product 2'  ,
      "imageUrl"             => 'https://store.payapi.io/media/43307ac7f356d51e6dd65b8ca9fe3d93/image/cache/catalog/Users/User4/payapi_premium_support-228x228.jpg' ,
      "category"             => 'category 2' ,
      "priceInCentsExcVat"   => 20000        ,
      "priceInCentsIncVat"   => 24000        ,
      "quantity"             => 2            ,
      "options"              => array (
        "color"              => 'red'        ,
        "size"               => 'XL'
      )
    ) ,
    array (
      "id"                   => 'ref87569' . rand ( 1000 , 9999 ) ,
      "url"                  => 'https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=43' ,
      "title"                => 'Product 3'  ,
      "imageUrl"             => 'https://store.payapi.io/media/43307ac7f356d51e6dd65b8ca9fe3d93/image/cache/catalog/Users/User4/payapi_premium_support-228x228.jpg' ,
      "category"             => 'category 3' ,
      "priceInCentsExcVat"   => 50000        ,
      "priceInCentsIncVat"   => 60000        ,
      "quantity"             => 3            ,
      "options"              => array (
        "color"              => 'white'      ,
        "size"               => 'M'
      )
    )
  ) ,
  "order"           => array (
    "currency"      => 'EUR' ,
    "referenceId"   => ( 'REF-' . md5 ( date ( 'YmdHis' , time () ) ) . '-' . 'test' ) ,
    "tosUrl"        => 'https://store.example.com/terms'
  ) ,
  "callbacks"       => array (
    "processing"    => 'https://api.example.com/callback-processing' ,
    "success"       => 'https://api.example.com/callback-success'    ,
    "failed"        => 'https://api.example.com/callback-failed'     ,
    "chargeback"    => 'https://api.example.com/callback-chargeback'
  ) ,
  "returnUrls"      => array (
    "success"       => 'https://store.example.com/payment-success'   ,
    "cancel"        => 'https://store.example.com/payment-cancel'    ,
    "failed"        => 'https://store.example.com/payment-failed'    ,
    "extraInput"    => 'https://store.example.com/payment-failed'
  )
) ;

$test = $sdk -> payment ( $product ) ;
