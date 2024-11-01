<?php

namespace payapi;

final class debug
{

  public static
    $single                  =    false;

  protected
    $history                 =    false;

  private
    $enabled                 =    false,
    $microtime               =    false,
    $lapses                  =        0,
    $run                     =        0,
    $lapse                   = array(),
    $fullTrace               =    false,
    $dir                     =    false,
    $file                    =    false,
    $labels                  =    array(
      'info',
      'time',
      'api',
      'run',
      'debug',
      'error',
      'warning',
      'fatal'
    );

  protected function __construct($enabled)
  {
    // $this->route
    if ($enabled !== true) {
      return false;
    }
    $this->microtime = microtime(true);
    $this->enabled = $enabled;
    $this->lapse('execution', true);
    $this->lapse('app', true);
    $this->dir = str_replace('core', 'debug', __DIR__) . DIRECTORY_SEPARATOR;
    $this->file = $this->dir . 'debug.' . __NAMESPACE__ . '.' . 'log';
    $this->reset();
    $this->set('=== DEBUG === ' . $this->timestamp() . ' ==>');
  }

  public function load()
  {
    return $this->timing('load',(microtime(true) - $this->microtime));
  }

  public function run($refresh = false)
  {
    if ($refresh === true) {
      $this->run = microtime(true);
    } else {
      $microseconds =(microtime(true) - $this->run);
      return $this->timing('run', $microseconds);
    }
  }

  public function lapse($key, $refresh = false)
  {
    if (isset($this->lapse[$key]) === true && is_numeric($this->lapse[$key]) && $refresh !== true) {
      $lapse =(microtime(true) - $this->lapse[$key]);
      switch($key) {
        case 'app':
          $microseconds =($lapse - $this->lapses);
        break;
        case 'execution':
        $microseconds = $lapse;
        break;
        default:
          if ($key != 'run') {
            $this->lapses += $lapse;
          }
          $this->run += $lapse;
          $microseconds = $lapse;
        break;
      }
      return $this->timing($key, $microseconds);
    }
    $this->lapse[$key] = microtime(true);
  }

  private function milisecons($microseconds) {
    $miliseconds = (round($microseconds, 3) * 1000);
    return $miliseconds;
  }

  private function reset()
  {
    $this->history = false;
    file_put_contents($this->file, '');
    return $this->blank();
  }

  private function timing ($key, $microseconds)
  {
    $timing = '[' . $key . '] timing ' . $this->milisecons($microseconds) . 'ms.';
    return $this->add($timing , 'time');
  }

  private function timestamp()
  {
    return date('Y-m-d H:i:s e', time());
  }

  public function add($info, $label = 'info')
  {
    $trace = $this->trace(debug_backtrace());
    $miliseconds = str_pad(round((microtime(true) - $this->microtime) * 1000, 0), 4, '0', STR_PAD_LEFT);
    $entry =($miliseconds . ' [' . $this->label($label) . '] ' . $trace . ' ' .((is_string($info)) ? $info :((is_array($info) ? json_encode($info) :((is_bool($info) || is_object($info)) ?(string) $info : 'undefined')))));
    $this->history[] = $entry;
    return $this->set($entry);
  }

  public function blank($info = null)
  {
    $this->set($info);
  }

  public function trace($traced)
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

  public function label($label)
  {
    if (is_string($label) && preg_match('~^[a-z]+$~i', $label) && in_array($label, $this->labels)) {
      return $label;
    }
    reset($this->labels);
    return current($this->labels);
  }

  public function history()
  {
    return $this->history;
  }

  protected function set($entry)
  {
    if ($this->enabled !== true) {
      return false;
    }
    $fileredEntry = filter_var($entry, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    return error_log($fileredEntry . "\n", 3, $this->file);
  }

  public static function single($enabled = false)
  {
    if (self::$single === false) {
      self::$single = new self($enabled);
    }
    return self::$single;
  }

  public function __toString()
  {
    return json_encode($this->history, true);
  }

  public function __destruct()
  {
    $this->lapse('app');
    $this->set('=== ' . $this->milisecons(microtime(true) - $this->microtime) . 'ms. === ' . $this->timestamp() . ' ===');
    $this->blank();
  }


}
