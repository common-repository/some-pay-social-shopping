<?php

namespace payapi;

//-> add error loggin
final class error
{

  public static
    $single                  =    false;

  private
    $error                   =    false,
    $fullTrace               =    false,
    $domain                  =    false,
    $instance                =    false,
    $log                     =    false,
    $labels                  =    array(
      "fatal",
      "warning",
      "notice",
      "undefined"
    );

  private function __construct()
  {
    $this->domain = instance::domain();
    $this->instance = instance::this();
    $this->log = router::routeError() . date('Ymd') . '.' . 'error' . '.' . 'log';
  }

  private function save($info, $label)
  {
    $trace = $this->trace(debug_backtrace());
    $entry =(date('Y-m-d H:i:s e', time()) . '[' . $this->domain . '][' . $this->instance . '][' . $label . '] ' . $trace . ' ' .((is_string($info)) ? $info :((is_array($info) ? json_encode($info) :((is_bool($info) || is_object($info)) ?(string) $info : 'undefined')))));
    $fileredEntry = filter_var($entry, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    return error_log($fileredEntry . "\n", 3, $this->log);
  }

  private function trace($traced)
  {
    $separator = '->';
    if ($this->fullTrace !== true) {
      $class = str_replace('payapi\\', null,(isset($traced[3]['class'])) ? str_replace('"', null, $traced[3]['class']) :((isset($traced[2]['class'])) ? $traced[2]['class'] : $traced[1]['class']));
      $function = str_replace('__', null,(isset($traced[3]['function'])) ? str_replace('"', null, $traced[3]['function']) :((isset($traced[2]['function'])) ? $traced[2]['function'] : $traced[1]['function']));
      $route = str_replace(array('payapi\\', '___'), null, $class . $separator . $function);
      return $route;
    }
    $levels = 5;
    $route = null;
    for($cont = count($traced); $cont > 0; $cont --) {
      $route .=((isset($traced[$cont]['class']) === true) ? $traced[$cont]['class'] . $separator : null) .((isset($traced[$cont]['function']) === true) ? $traced[$cont]['function'] . $separator : null);
    }
    return $route;
  }

  public function add($error, $label)
  {
    $checkedLabel =(in_array($label, $this->labels) === true) ? $label : 'undefined';
    $this->error[$checkedLabel][] = $error;
    return $this->save($error, $label);
  }

  public function alert()
  {
    return $this->error;
  }

  public function undefined()
  {
    return 600;
  }

  public function noValidSsl()
  {
    return 505;
  }

  public function notAcceptable()
  {
    return 406;
  }

  public function notImplemented()
  {
    return 501;
  }

  public function badRequest()
  {
    return 400;
  }

  public function notFound()
  {
    return 404;
  }

  public function unexpectedResponse()
  {
    return 406;
  }

  public function timeout()
  {
    return 504;
  }

  public function knockNotValid()
  {
    return 404;
  }

  public function unauthorized()
  {
    return 401;
  }

  public function noValidCronAccess()
  {
    return 404;
  }

  public function transactionDuplicated()
  {
    return 401;
  }

  public function forbidden()
  {
    return 403;
  }

  public function knockUnexpectedSignature()
  {
    return 403;
  }

  public function notValidSchema()
  {
    return 400;
  }

  public function notValidLocalizationSchema()
  {
    return 404;
  }

  public function notValidMethod()
  {
    return 405;
  }

  public function notValidSsl()
  {
    return 505;
  }

  public function notLocalizableAccess()
  {
    return 416;
  }

  public function noCacheSanitization()
  {
    return 409;
  }

  public function notSatisfied()
  {
    return 412;
  }

  public function booBoo()
  {
    return 600;
  }

  public static function single()
  {
    if (self::$single === false) {
      self::$single = new self();
    }
    return self::$single;
  }


}
