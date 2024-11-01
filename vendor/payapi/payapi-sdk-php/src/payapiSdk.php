<?php
/**
*
*  @package    PayApi PHP SDK
*              https://payapi.io/
*
*  @uses       PHP +v5.x
*              JWT
*
*  @category   Payments, Social Sharing
*  @version    v1.0.0.0(20170711)
*  @version    v2.0.0.0(20171019)
*
*  @param     (object) adapt =(main sdk config || adapt model for adaptor plugin)
*
*  @NOTE
*        - 1. to log into sdk thought :
*              $sdk->settings($payapi_public_id, $payapi_api_enc_key);
*              * this returns merchantSettings payload if valid
*                and creates an encrypted cache for both:
*                payapi/cache/<instanceKey>/account
*                payapi/cache/<instanceKey>/settings
*
*        - 2. commands :
*              $sdk->instantPayment($data);                       //-> validate/sanitize/cache product/payment data
*                                                                          returns array("metadata", "endPointPayment", "endPointProductPayment");
*              $sdk->branding();                                  //-> gets branding/partner info
*
*              $sdk->callback();                                  //-> gets/cache callback decoded transaction
*
*              $sdk->localize();                                  //-> gets/cache ip decoded localization(plugin adds adapt country and zone ids)
*              $sdk->localize(true);                              //-> gets & refresh ip decoded localization cache(plugin adds adapt country and zone ids)
*
*              $sdk->settings('public_id', 'api_key', true);      //-> verify account & gets/cache instance merchantSettings, also refresh account data
*              $sdk->settings(false, false, true);                //-> refresh & gets instance merchantSettings
*              $sdk->settings();                                  //-> gets instance cached merchantSettings
*
*              $sdk->partialPayment($totalInCents, $currency);    //-> calculate partialPayment from merchantSettings
*
*              $sdk->response(<standard_response_code_int>);      //-> get response info
*
*              $sdk->info();                                      //-> gets sdk debug info
*
*       (*) plugin is defined in /payapi/app.engine.php
*              - native
*              - opencart23
*              - magento
*              - prestashop
*
*  @return
*        - success :
*              array(
*                "code"  =>   (int)            200,
*                "data"  => (array) <expectedData>
*             );
*
*        - error :
*              array(
*                "code"  =>   (int)    <errorCode>,
*                "error" =>(string)    <errorData>
*             );
*
*
*  @author     florin
*  @copyright  PayApi Ltd
*  @license    GPL v3.0
*
*
*  @TODO       ever!
*              sdk default noplugin settings
*
*
**/

//-> $         curl --insecure https://www.sdk.dev > /dev/null

namespace Payapi\PaymentsSdk;
use \payapi\engine as app;

class payapiSdk
{

  private
    $app                       =   false;

  public function __construct($adapt = false, $plugin = false, $branding = false)
  {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'payapi' . DIRECTORY_SEPARATOR . 'app' . '.' . 'engine' . '.' . 'php');
    $this->app = app :: single($adapt, $plugin, $branding);
    return $this->app;
  }

  public function __call($command, $arguments = array())
  {
    return $this->app->$command($arguments);
  }


}
