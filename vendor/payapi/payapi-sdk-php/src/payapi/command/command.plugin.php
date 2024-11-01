<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->plugin()
*
* @TYPE     public
*
* @RETURNS
*           plugin/brand array OR $sdk->plugin($this->defaultPluginBrand) array
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(15) {
*              ["partnerId"]=>
*              string(6) "payapi"
*              ["partnerName"]=>
*              string(6) "PayApi"
*              ["partnerSlogan"]=>
*              string(33) "Secure Online and Mobile Payments"
*              ["partnerLogoUrl"]=>
*              string(67) "https://input.payapi.io/modules/core/img/brand/logo_transparent.png"
*              ["partnerIconUrl"]=>
*              string(77) "https://input.payapi.io/modules/core/img/brand/payapi_shield_protected_v2.jpg"
*              ["partnerSupportInfoL1"]=>
*              string(212) "For any support requests or help, please do not hesitate to contact &lt;strong&gt;PayApi Support&lt;/strong&gt; via &lt;a href=&quot;https://payapi.io&quot;&gt;payapi.io&lt;/a&gt; or via email: support@payapi.io."
*              ["partnerSupportInfoL2"]=>
*              string(0) ""
*              ["webshopBaseDomain"]=>
*              string(21) "multimerchantshop.com"
*              ["partnerWebUrl"]=>
*              string(17) "https://payapi.io"
*              ["partnerContactEmail"]=>
*              string(17) "support@payapi.io"
*              ["partnerContactPhone"]=>
*              string(11) "34667074000"
*              ["updated_at"]=>
*              string(14) "20170531104941"
*              ["created_at"]=>
*              string(14) "20161027100000"
*              ["updatable"]=>
*              string(1) "1"
*              ["enable"]=>
*              string(1) "1"
*           }
*
* @NOTE
*          $this->defaultPluginBrand()
*               *plugin/brand data is defaulted if gets error
*
*          plugin/brand data is related just to plugin branding, so it is static
*               it is not updated on merchantSettings/storeCreation calls
*
* @VALID
*          @TODO schema.pluginBrand
*
* @TODO
*         Command is called plugin so it will include more info about adaptor and script using SDK
*
*/

final class commandPlugin extends controller
{

  private $defaultPluginBrand = 'payapi';

  public function run()
  {
    $pluginBrand = $this->pluginBrand();
    if (is_array($pluginBrand) !== false) {
      $this->debug('[branded] ' . $pluginBrand['partnerId']);
      return $this->render($pluginBrand);
    }
    $this->warning('plugin brand defaulted');
    return $this->defaultPluginBrand();
  }

  private function pluginBrand()
  {
    if (is_string($this->arguments(0)) === true) {
      $this->debug('brand key: ' . $this->arguments(0));
      if (is_string($this->arguments(0)) === true) {
        $pluginBrand = $this->load->pluginBrand($this->arguments(0));
        if (is_array($pluginBrand) === true) {
          return $pluginBrand;
        } else {
          $this->warning('invalid plugin branding');
        }
      } else {
        $this->warning('invalid value');
      }
    } else {
      $this->debug('[brand] default');
    }
    return false;
  }

  private function defaultPluginBrand()
  {
    return $this->load->pluginBrand($this->defaultPluginBrand);
  }


}
