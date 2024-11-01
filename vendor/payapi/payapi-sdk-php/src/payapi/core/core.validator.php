<?php

namespace payapi;

//-> @NOTE   $this->schema($data, $schema)
//->         also sanitize data
final class validator extends helper
{

  public
    $version                    = '1.0.0';

  public function command($command)
  {
    //->
    if (is_string($command) === true) {
      return true;
    }
    return false;
  }

  public function schema($data, $schema)
  {
    if (is_array($schema) !== false) {
      if ($this->checkNoObjects($data) === true) {
        if (isset($schema['___schema']) === true && is_array($schema['___schema']) !== false) {
          $error = 0;
          $validated = array();
          foreach($schema['___schema'] as $key => $value) {
            if (isset($data[$key]) === true) {
              $min =((isset($value['___min']) === true && is_int($value['___min']) === true) ? $value['___min'] : false);
              $max =((isset($value['___max']) === true && is_int($value['___max']) === true) ? $value['___max'] : false);
              if ($this->check($data[$key], $value['___type'], $min, $max) !== true) {
                $error ++;
                $this->warning('[' . $key . '] no valid value', 'schema');
              }
              if ($value['___type'] === 'urlencoded') {
                $data[$key] = $this->serialize->paymentUrlEncode($data[$key]);
              }
            } else if ($value['___mandatory'] !== false) {
              $error ++;
              $this->warning('[' . $key . '] mandatory missed', 'schema');
            }
          }
          if ($error === 0) {
            //-> @NOTE sanitization
            return $this->sanitize->schema($schema, $data);
          }
        } else {
          $this->warning('no valid schema', 'schema');
        }
      } else {
        $this->warning('object blocked', 'schema');
      }
    } else {
      $this->warning('[' . json_encode($schema) . '] no valid schema', 'schema');
    }
    return false;
  }

  public function isString($string, $min = false, $max = false)
  {
    if (is_string($string) === true) {
      $error = 0;
      if (is_int($min) && strlen($string) < $min) {
        $error ++;
      }
      if (is_int($max) && strlen($string) > $max) {
        $error ++;
      }
      if ($error === 0) {
        return true;
      }
    }
    return false;
  }

  public function isUrl($url, $unsecure = false)
  {
    if ($this->isString($url, 11, 1000) === true && strpos($url, 'http') !== false && md5(filter_var($url, FILTER_VALIDATE_URL)) === md5($url)) {
      if ($unsecure !== true) {
        if (strpos($url, 'http://') !== false || strpos($url, 'https://') === false) {
          $this->warning('unsecure connection blocked', 'url');
          return false;
        }
      }
      return true;
    }
    return false;
  }

  public function isArray($array, $min = false, $max = false)
  {
    if (is_array($array) !== false && $this->checkNoObjects($array) === true) {
      $error = 0;
      if (is_int($min) && count($array) < $min) {
        $error ++;
      }
      if (is_int($max) && count($array) > $max) {
        $error ++;
      }
      if ($error === 0) {
        return true;
      }
    }
    return false;
  }

  private function isSetting($setting, $min, $max)
  {
    if ($setting === false || $this->isArray($setting, $min, $max) === true) {
      return true;
    }
    return false;
  }

  public function callbackStatuses()
  {
    return array(
      "processing",
      "successful",
      "failed",
      "chargeback"
    );
  }

  public function returnStatuses()
  {
    return array(
      "success",
      "cancel",
      "failed"
    );
  }

  private function isCallbackStatus($status)
  {
    if (in_array($status, $this->callbackStatuses())) {
      return true;
    }
    return false;
  }

  private function isReturnStatus($status)
  {
    if (in_array($status, $this->returnStatuses())) {
      return true;
    }
    return false;
  }

  public function commandLineInterfaceAccess()
  {
    if (function_exists('php_sapi_name')) {
      if (php_sapi_name() === 'cli') {
        return true;
      }
    } //-> { /** this should trigger a alert/warning if noCron access **/ }
    return false;
  }

  public function isInteger($integer, $min = false, $max = false)
  {
    if (is_int($integer) === true) {
      $error = 0;
      if (is_int($min) && $integer < $min) {
        $error ++;
      }
      if (is_int($max) && $integer > $max) {
        $error ++;
      }
      if ($error === 0) {
        return true;
      }
    }
    return false;
  }

  public function isNumber($number, $min = false, $max = false)
  {
    if (is_numeric($number) === true) {
      return $this->isInteger((int) $number);
    }
    return false;
  }

  public function isValidCode($code)
  {
    if (is_int($code) && preg_match('/^\d{3}$/', $code) && $code <= 600 && $code >= 200) {
      return true;
    }
    return false;
  }

  public function isPhoneNumber($phone)
  {
    if (is_numeric($phone) === true && $phone > 9999999 && $phone < 9999999999999999999) {
      return true;
    }
    return false;
  }

  public function isEmail($email)
  {
    if (md5(filter_var($email, FILTER_VALIDATE_EMAIL)) === md5($email)) {
      return true;
    }
    return false;
  }

  public function check($data, $type, $min = false, $max = false)
  {
    switch($type) {
      case 'string':
        return $this->isString($data, $min, $max);
      break;
      case 'integer':
        return $this->isInteger($data, $min, $max);
      break;
      case 'number':
        return $this->isNumber($data);
      break;
      case 'ip':
        return $this->ip($data);
      break;
      case 'phone':
        return $this->isPhoneNumber($data);
      break;
      case 'callbackStatus':
        return $this->isCallbackStatus($data);
      break;
      case 'returnStatus':
        return $this->isReturnStatus($data);
      break;
      case 'urlencoded':
      case 'url':
        return $this->isUrl($data);
      break;
      case 'email':
        return $this->isEmail($data);
      break;
      case 'setting':
        return $this->isSetting($data, $min, $max);
      break;
      case 'array':
        return $this->isArray($data, $min, $max);
      break;
      default:
        $this->warning('[type] not defined : ' . $type);
        return false;
      break;
    }
    return false;
  }

  public function ssl($checkDomain = false, $selfsigned = false, $timeout = 1, $checked = false)
  {
    $verifyPeer =($selfsigned === true) ? false : true;
    $domain =(is_string($checkDomain) === true) ? $this->sanitize->parseDomain($checkDomain) : $this->domain;
    $this->debug('domain: ' . $this->domain);
    $socket = stream_context_create(['http' =>['method' => 'GET'], 'ssl' =>[
      'capture_peer_cert'       => true,
      'capture_peer_cert_chain' => true,
      'disable_compression'     => true, //-> anti CRIME
      'verify_depth'            => 3,
      //'passphrase'              => '',
      'verify_expiry'           => $verifyPeer,
      'allow_cert_self_signed'  => $selfsigned,
      "verify_peer_name"        => $verifyPeer,
      'verify_peer'             => $verifyPeer,
      'ciphers'                 => 'TLSv1.2'
   ]]);
    $this->debug('[SSL] ' . $domain);
    $stream = stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 1, STREAM_CLIENT_CONNECT, $socket);
    if ($stream != false) {
      $streamParams = stream_context_get_params($stream);
      $streamMetas = stream_get_meta_data($stream);
      if (isset($streamParams["options"]["ssl"]["peer_certificate"]) === true && isset($streamMetas['stream_type']) === true && md5($streamMetas['stream_type']) === md5('tcp_socket/ssl')) {
        $this->debug(('[VALID] ' . $streamMetas['stream_type']));
        return $streamParams["options"]["ssl"]["peer_certificate"];
      }
    } else if ($checked === false) {
      sleep(100); //-> avoiding timeout
      $this->debug('[SSL] retry');
      return $this->ssl($domain, $selfsigned, $timeout, true);
    }
    $this->warning('ssl certificate', 'NOVALID');
    return false;
  }

  public function ip($ip)
  {
    if (md5(filter_var($ip, FILTER_VALIDATE_IP)) === md5($ip) && ip2long($ip) !== false) {
      return true;
    }
    return false;
  }
  //->
  public function publicId($apiKey)
  {
    if ($this->isString($apiKey, 4, 250) === true) {
      return true;
    }
    return false;
  }
  //->
  public function apiKey($apiKey)
  {
    if ($this->isString($apiKey, 4, 250) === true) {
      return true;
    }
    return false;
  }
  //->
  public function knock()
  {
    return false;
  }

  private function objectsToArray($object, &$array)
  {
    //->
    if (! is_object($object) && ! is_array($object)) {
      $array = $object;
      return $array;
    }
    foreach($object as $key => $value) {
      if (! empty($value)) {
        $array[$key] = array();
        $this->serialized($value, $array[$key]);
      } else {
        $array[$key] = $value;
      }
    }
    return $array;
  }

  private function checkNoObjects($data)
  {
    //-> @TODO review
    if (is_array($data) !== false) {
      foreach($data as $key => $value) {
        if ($this->checkNoObjects($value) !== true) {
          $this->error('object blocked : ' . $key, 'warning');
          return false;
        }
      }
    }//-> else
    if (is_object($data) === false) {
      return true;
    }
    return false;
  }

  private static function isAlphaNumeric($key)
  {
    return preg_match('~^[0-9a-z]+$~i', $key);
  }

  public function __toString()
  {
    return $this->version;
  }


}
