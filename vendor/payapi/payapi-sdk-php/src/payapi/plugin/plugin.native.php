<?php

namespace payapi;

final class plugin
{

  public static
    $single                    =     false;

  public
    $version                   =        '0.0.0';

  private
    $native                    =          false,
    $config                    =          false,
    $db                        =          false;

  public function __construct($config)
  {
    $this->config = $config;
  }

  public function validated()
  {
    //->
    return true;
  }

  public function product($product)
  {
    //->
    return $product;
  }

  public function payment($payment)
  {
    //->
    return $payment;
  }

  public function instantPayment($payment)
  {
    //->
    return $payment;
  }

  public function loadLog()
  {
    //->
    return false;
  }

  public function log($info)
  {
    return true;
  }

  public function config()
  {
    return $this->config;
  }

  public function session()
  {
    return false;
  }

  public function db()
  {
    return false;
  }

  public function customer($email)
  {
    return false;
  }

  public function debug()
  {
    if (isset($this->config['debug']) === true && $this->config['debug'] === true) {
      return true;
    }
    return false;
  }

  public function demo()
  {
    if (isset($this->config['demo']) === true && $this->config['demo'] === true) {
      return true;
    }
    return false;
  }

  public function nativeVersion()
  {
    return $this->version;
  }

  public function version()
  {
    return $this->version;
  }

  public function staging()
  {
    if (isset($this->config['staging']) === true && $this->config['staging'] === true) {
      return true;
    }
    return false;
  }

  public function localized($localized)
  {
    return $localized;
  }

  public static function single($adapt)
  {
    if (self::$single === false) {
      self::$single = new self($adapt);
    }
    return self::$single;
  }

  public function __toString()
  {
    return $this->version;
  }


}
