<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->branding('payapi')
*           $sdk->branding() //-> get branding from external branding library OR default branding
*
* @TYPE     public
*
* @RETURNS
*           (array) brand OR $sdk->branding($this->defaultPluginBrand)
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
               ["partnerBackoffice"]=>
                  array(2) {
                    ["production"]=>
                    string(15) "input.payapi.io"
                    ["staging"]=>
                    string(23) "staging-input.payapi.io"
                  }
*           }
*
* @NOTE
*          $this->defaultPluginBrand()
*               *brand data is defaulted if gets error
*
* @TODO
*         update brand info in settings call
*
*/

final class commandBranding extends controller
{

  private $defaultPluginBrand = 'payapi';

  public function run()
  {
    $pluginBrand = $this->pluginBrand();
    if (is_array($pluginBrand) !== false) {
      $this->debug('[branded] ' . $pluginBrand['partnerId']);
      $pluginBrand['partnerBackoffice'] = $this->backOffice();
      return $this->render($pluginBrand);
    }
    return $this->render($this->defaultPluginBrand());
  }

  private function pluginBrand()
  {    
    if (is_string($this->arguments(0)) === true) {
      $this->debug('checking brand: ' . $this->arguments(0));
      return $this->getPluginBrandFromCode($this->arguments(0));
    } else {
      if (method_exists('\Payapi\Branding\Branding','getBrandingCode')) {
          $brandFromComposer = new \Payapi\Branding\Branding();
          $brandCode = $brandFromComposer->getBrandingCode();
          $this->debug('checking brand from library: ' . $brandCode);
          return $this->getPluginBrandFromCode($brandCode);          
      } else {
        $this->debug('[brand] default');
      }
    }

    return false;
  }

  private function backOffice()
  {
      $backoffice = array(
          "production" => 'input.payapi.io',
          "staging" => 'staging-input.payapi.io'
      );
      return $backoffice;
  }

  private function defaultPluginBrand()
  {
    $this->warning('plugin brand defaulted');
    return $this->load->pluginBrand($this->defaultPluginBrand);
  }

  private function getPluginBrandFromCode($code)
  {
    if (is_string($code) === true) {
        $pluginBrand = $this->load->pluginBrand($code);
        if (is_array($pluginBrand) === true) {
          $pluginBrand['partnerBackoffice'] = $this->backOffice();
          return $pluginBrand;
        } else {
          $this->warning('invalid plugin branding');          
        }
      } else {
        $this->warning('invalid value');
      }
      return false;
  }

}
