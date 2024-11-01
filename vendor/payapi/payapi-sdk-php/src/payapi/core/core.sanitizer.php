<?php

namespace payapi;

final class sanitizer
{

  public static
    $single                     =   false;

  protected
    $version                  =   '0.0.0';

  public function schema($schema, $data)
  {
    if (isset($schema['___schema']) === true && is_array($data) !== false) {
      $diffs = array_diff_key($data, $schema['___schema']);
      foreach($diffs as $diff => $value) {
        if ($diff != 'numberOfInstallments') {
          unset($data[$diff]);
        }
      }
      return $data;
    }
    return false;
  }

  public function parseDomain($url)
  {
    $parsed = parse_url($url);
    if (! isset($parsed['host']))
      return false;
    return $parsed['host'];
  }

  public function sanitizeStream($stream)
  {
    return filter_var($url, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  }

  public function sanitizedQuotes($url)
  {
    return filter_var($url, FILTER_SANITIZE_MAGIC_QUOTES);
  }

  public function sanitizedSpecialChars($string)
  {
    return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
  }

  public function sanitizedFullSpecialChars($string)
  {
    return filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
  }

  public function sanitizedInt($int)
  {
    return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
  }

  public function render($render)
  {
    if (is_array($render) !== false) {
      $sanitize = array('___tk');
      foreach($render as $key => $value) {
        if (in_array($key, $sanitize) === true) {
          unset($render[$key]);
        }
        if (is_array($value)) {
          foreach($value as $key2 => $value2) {
            if (in_array($key2, $sanitize) === true) {
              unset($render[$key][$key2]);
            }
          }
        }
      }
    }
    return $render;
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
