<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->localize()
*           $sdk->localize(true) //-> refresh localization
*           $sdk->localize(true/false, $ip) //-> localizates ip
*
* @TYPE     public
*
* @PARAMS
*           $ip = valid ip
*
* @RETURNS
*           localization array OR $this->error->notFound()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(8) {
*            ["ip"]=>
*             string(12) "84.79.234.58"
*            ["countryCode"]=>
*             string(2) "ES"
*            ["countryName"]=>
*             string(5) "Spain"
*            ["regionName"]=>
*             string(6) "Madrid"
*            ["regionCode"]=>
*             string(2) "MD"
*            ["postalCode"]=>
*             string(5) "28529"
*            ["timezone"]=>
*             string(13) "Europe/Madrid"
*            ["timestamp"]=>
*             float(1500884755.4039)
*           }
*
* @NOTE
*          localization info is cached
*          data is adapted through plugin
*
* @VALID
*          schema.localize
*
* @TODO
*          localize just when needed->transaction
*          common ip cahe is still isolated at encoding
*
*/
final class commandLocalize extends controller
{

  public function run()
  {
    if ($this->validate->ip($this->arguments(1)) === true) {
      $ip = $this->arguments(1);
    } else {
      $ip = $this->ip();
    }
    $this->debug('[check] ' . $ip);
    $cached = $this->cache('read', 'localize', $ip);
    if ($this->arguments(0) !== true && $cached !== false) {
      return $this->render($cached);
    } else {
      $endPoint = $this->serialize->endPointLocalization($ip);
      $request = $this->curl($endPoint, false, false);
      if ($request !== false && isset($request['code']) === true) {
        if ($request['code'] === 200) {
          $validated = $this->validate->schema($request['data'], $this->load->schema('localize'));
          if (is_array($validated) !== false) {
            $this->debug('[localize] valid schema');
            $adaptedData = $this->adaptor->localized($validated);
            $this->cache('writte', 'localize', $ip, $adaptedData);
            return $this->render($this->cache('read', 'localize', $ip));
          } else {
            //-> not valid schema from PA
            $this->error('no valid localization', 'warning');
            return $this->returnResponse($this->error->notValidLocalizationSchema());
          }
        } else {
          return $this->returnResponse($request['code']);
        }
      }
    }
    return $this->returnResponse($this->error->timeout());
  }


}
