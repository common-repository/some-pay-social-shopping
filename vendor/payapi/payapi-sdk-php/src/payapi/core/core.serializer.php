<?php

namespace payapi;

class serializer
{

  public static
    $single                     =   false;

  protected
    $instance                   =   false,
    $version                    = '0.0.1';

  private $staging = false;

  protected function __construct()
  {
    $this->instance = instance::this();
    $this->domain = instance::domain();
    $this->config = config::single();
    $this->staging = $this->config->get('staging');

  }

  public function publicQueryFlag()
  {
    return md5($this->domain . md5($this->instance));
  }

  public function mode($staging = false)
  {
    if($staging === true || $staging === 1) {
      $this->staging = true;
    } else {
      $this->staging = false;
    }
    return $this->staging;
  }

  public function endPointLocalization($ip)
  {
    $api = $this->https() . $this->staging() . 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' . 'fraud' . '/' . 'ipdata' . '/' . $ip;
    return $api;
  }

  public function endPointSettings($publicId, $staging = false)
  {
    $api = $this->https() . $this->staging() . $this->api() . 'merchantSettings' . '/' . $publicId;
    return $api;
  }

  public function endPointInstantBuy($publicId)
  {
    $api = $this->https() . $this->staging() . $this->webshop() . $publicId . '/';
    return $api;
  }

  public function endPointPayment($publicId)
  {
    $api = $this->https() . $this->staging() . $this->payment() . $publicId . '/';
    return $api;
  }

  public function endPointQr($url)
  {
    return $this - https() . $this->staging() . $this->webshop() . 'qr' . '/' . $this->paymentUrlEncode($url);
  }

  private function webshop()
  {
    return $this->endPoint() . 'webshop' . '/';
  }

  private function payment()
  {
    return $this->endPoint() . 'secureform' . '/';
  }

  private function api()
  {
    return $this->endPoint() . 'api' . '/';
  }

  private function endPoint()
  {
    return 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/';
  }

  private function https()
  {
    return 'https' . ':' . '//';
  }

  private function staging()
  {
    $route =(($this->staging === true) ? 'staging' . '-' : null);
    return $route;
  }

  public function paymentUrlEncode($url)
  {
    return urlencode(html_entity_decode($url));
  }

  public function microstamp()
  {
    return  microtime(true);
  }

  public function getDomainFromUrl($url)
  {
    $parsed = parse_url($url);
    if (isset($parsed['host']) === true) {
      return $parsed['host'];
    }
    return false;
  }

  public function timestamp()
  {
    return date('Y-m-d H:i:s T', time());
  }

  public function options($options)
  {
    $optionsSerialized = '';
    foreach($options as $option => $value) {
      $optionsSerialized .=(($optionsSerialized !== '') ? '&' : null) . $option . '=' . $value;
    }
    return $optionsSerialized;
  }

  public function percentage($total, $part)
  {
    return(int) round((($part * 100) /($total - $part)), 0);
  }

  public function arrayToJson($array)
  {
    $json = json_encode($array, true);
    return $json;
  }

  public function jsonToArray($json, $toArray = false)
  {
    $array = json_decode($json, $toArray);
    return $array;
  }

  public function lenght($value, $lenght)
  {
    if (is_array($value) !== true && is_object($value) !== true) {
      if (preg_match("/^\d{" . $lenght . "}$/", $int) === true) {
        return true;
      }
    }
    return false;
  }

  public static function cleanLogNamespace($route)
  {
    return str_replace(array('payapi\\', 'controller_', 'model_'), null, $route);
  }

  public function undefined()
  {
    return 'undefined';
  }

  public function __toString()
  {
    return $this->version;
  }

  public static function single()
  {
    if (self::$single === false) {
      self::$single = new self();
    }
    return self::$single;
  }


}
