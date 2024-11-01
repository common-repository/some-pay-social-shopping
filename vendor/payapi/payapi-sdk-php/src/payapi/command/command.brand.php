<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->brand()
*
* @TYPE     private
*
* @RETURNS
*           brand/reseller array OR $this->error->notFound()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(10) {
*            ["partnerId"]=>
*             string(6) "payapi"
*            ["partnerName"]=>
*             string(6) "PayApi"
*            ["partnerSlogan"]=>
*             string(33) "Secure Online and Mobile Payments"
*            ["partnerLogoUrl"]=>
*             string(67) "https://input.payapi.io/modules/core/img/brand/logo_transparent.png"
*            ["partnerIconUrl"]=>
*             string(77) "https://input.payapi.io/modules/core/img/brand/payapi_shield_protected_v2.jpg"
*            ["partnerSupportInfoL1"]=>
*             string(178) "For any support requests or help, please do not hesitate to contact <strong>PayApi Support</strong> via <a href="https://payapi.io">payapi.io</a> or via email: support@payapi.io."
*            ["webshopBaseDomain"]=>
*             string(21) "multimerchantshop.com"
*            ["partnerWebUrl"]=>
*             string(17) "https://payapi.io"
*            ["partnerContactEmail"]=>
*             string(17) "support@payapi.io"
*            ["timestamp"]=>
*             float(1500903420.0844)
*           }
*
* @NOTE
*          brand/reseller data is cached/updated in settings data fetch
*
* @VALID
*          schema.settings.reseller
*
*/
final class commandBrand extends controller
{

  public function run()
  {
    if (is_array($this->brand()) !== false) {
      return $this->render($this->brand());
    }
    return $this->returnResponse($this->error->notFound());
  }


}
