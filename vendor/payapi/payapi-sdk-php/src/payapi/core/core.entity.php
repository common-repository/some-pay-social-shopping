<?php

namespace payapi;

final class entity
{

  public static
    $single            =       false;

  private
    $prefix            =       '___',
    $signature         =        null,
    $data              =    array(),
    $config            =    array();

  private function __construct()
  {
    $this->signature = $this->prefix . 'extradata';
    $this->set($this->signature, array());
    $this->addInfo('stamp', microtime(true));
  }

  public function get($key)
  {
    if ($this->has($key) === true) {
      return $this->data[$key];
    }
    return $key;
  }

  public function remove($key)
  {
    if ($this->has($key) === true) {
      unset($this->data[$key]);
      return true;
    }
    return false;
  }

  public function has($key)
  {
    if (is_string($key) === true) {
      return isset($this->data[$key]);
    }
    return false;
  }

  public function set($key, $value = false)
  {
    if (preg_match('~^[0-9a-z]+$~i', $key) !== false) {
      $this->data[$key] = $value;
      return true;
    }
    return false;
  }

  public function addInfo($key, $info)
  {
    $refreshInfo = array_merge(
      array(
        $this->prefix .(string) $key =>(string) $info
      ),
      $this->get($this->signature)
    );
    $this->set($this->signature, $refreshInfo);
  }

  public function signature()
  {
    return $this->signature;
  }

  public function extradata()
  {
    return array(
      $this->signature => $this->get($this->signature)
    );
  }

  public function sanitizeExtradata($array)
  {
    if (isset($array[$this->signature])) {
      unset($array[$this->signature]);
    }
    return $array;
  }

  public function addExtradata($array)
  {
    return array_merge(
      $this->sanitizeExtradata($array),
      $this->extradata()
    );
  }

  public static function single()
  {
    if (self::$single === false) {
      self::$single = new self;
    }
    return self::$single;
  }


}
