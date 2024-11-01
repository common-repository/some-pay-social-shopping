<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->settings($staging, $payapi_public_id, $payapi_api_enc_key) //-> refresh settings
*
*           $sdk->settings() //-> get cached settings
*
* @TYPE     public
*
* @PARAMS
*.          $staging = boolean
*           $payapi_public_id = string/false
*           $payapi_api_enc_key = string/false
*
* @RETURNS
*           settings array OR $this->error->badRequest()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(3) {
*            ["partialPayments"]=>
*             array(9) {
*              ["preselectedPartialPayment"]=>
*               string(12) "pay_in_parts"
*              ["numberOfInstallments"]=>
*               int(36)
*              ["minimumAmountAllowedInCents"]=>
*               int(10000)
*              ["minimumAmountAllowedCurrency"]=>
*               string(3) "EUR"
*              ["invoiceFeeInCents"]=>
*               int(435)
*              ["invoiceFeeCurrency"]=>
*               string(3) "EUR"
*              ["nominalAnnualInterestRateInCents"]=>
*               int(1300)
*              ["paymentTermInDays"]=>
*               int(30)
*              ["whitelistedCountries"]=>
*               array(1) {
*                [0]=>
*                 string(2) "FI"
*               }
*             }
*            ["reseller"]=>
*             string(6) "payapi"
*            ["timestamp"]=>
*             float(1500903420.0991)
*           }
* @CARE
*          most SDK commands are not available till settings is successful
*          public commands: info, localize, settings
*
*
* @NOTE
*          seetings info is cached
*          reseller info is also cached
*
* @VALID
*          schema.settings*
*
* @TODO
*          add stag/prod flag to command
*.         update brand data on settings refresh
*
*/
final class commandSettings extends controller
{

  private $staging = false;

  public function run()
  {
    if ($this->arguments(1) != false) {
      $this->cache('delete', 'settings', $this->instance());
    }
    if ($this->validate->publicId($this->arguments(1)) === true && $this->validate->apiKey($this->arguments(2)) === true) {
      $publicId = $this->arguments(1);
      $apiKey = $this->arguments(2);
    } else {
      $publicId = $this->publicId();
      $apiKey = $this->apiKey();
    }
    //-> @TODO review config mode handling
    $this->config->mode($this->arguments(0));
    $this->staging = $this->serialize->mode($this->arguments(0));
    if ($this->validate->publicId($publicId) === true && $this->validate->apiKey($apiKey) === true) {
      $cached = $this->cache('read', 'settings', $this->instance());
      if ($this->arguments(1) === false && $cached !== false) {
        return $this->render($cached);
      } else {
        $endPoint = $this->serialize->endPointSettings($publicId);
        $request = $this->curl($endPoint, $this->payload($apiKey), true);
        if ($request !== false && isset($request['code']) === true) {
          if ($request['code'] === 200) {
            $decodedData = json_decode($this->decode($request['data'], $apiKey), true);
            $validated = $this->validate->schema($decodedData, $this->load->schema('settings'));
            if (is_array($validated) !== false) {
              $error = 0;
              foreach($validated as $key => $value) {
                if($value !== false) {
                  $settings[$key] = $this->validate->schema($value, $this->load->schema('settings' . '.' . $key));
                  if (is_array($settings[$key]) === false) {
                    $error ++;
                  }
                } else {
                  $settings[$key] = false;
                }
              }
              if ($error === 0) {
                $this->cache('writte', 'account', $this->instance(), array(
                  "publicId" => $publicId,
                  "apiKey"   => $this->encode($apiKey, false, true),
                  "staging"  => $this->staging
                ));
                $resellerData = $settings['reseller'];
                $resellerId = $resellerData['partnerId'];
                $settings['reseller'] = $resellerId;
                $this->cache('writte', 'reseller', $resellerId, $resellerData);
                $this->cache('writte', 'settings', $this->instance(), $settings);
                return $this->render($this->cache('read', 'settings', $this->instance()));
              } else {
                $this->error('no valid settings', 'warning');
                return $this->returnResponse($this->error->notValidSchema());
              }
            } else {
              //-> not valid schema from PA
              $this->error('no valid settings', 'warning');
              return $this->returnResponse($this->error->notValidSchema());
            }
          } else {
            return $this->returnResponse($request['code']);
          }
        } else {
          return $this->returnResponse($this->error->unexpectedResponse());
        }
      }
    } else {
      return $this->returnResponse($this->error->badRequest());
    }
    return $this->returnResponse($this->error->timeout());
  }

  private function updateBrand()
  {
    //->
  }

  private function payload($apiKey)
  {
    $payload = array(
      //=>
      "storeDomain" => getenv('SERVER_NAME')
    );
    return $this->encode($payload, $apiKey);
  }


}
