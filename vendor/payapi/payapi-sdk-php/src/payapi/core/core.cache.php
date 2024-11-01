<?php

namespace payapi;

final class cache extends helper
{

  protected
    $version               = '0.0.1';

  private
    $intance               =   false,
    $dir                   =   false,
    $caches                =   false,
    $cache                 =   array(
      //             expiration days
      "localize"           =>     30,
      "ssl"                =>      1,
      "product"            =>      1,
      "payment"            =>      1,
      "transaction"        =>  false,
      "update"             =>  false,
      "reseller"           =>  false,
      "instance"           =>  false,
      "account"            =>  false,
      "settings"           =>  false
    );

  public function ___autoload()
  {
    $this->instance = instance::this();
  }

  public function caches()
  {
    return $this->cache;
  }

  public function read($key, $token)
  {
    if (isset($this->caches[$key][$token])) {
      return $this->caches[$key][$token];
    }
    $file = $this->validate($key, $token);
    if (is_string($file) === true) {
      if (is_file($file) === true) {
        $this->debug('[' . $key . '] cached');
        $cacheInfo = date('',filemtime($file));
        if ($this->cache[$key] === false || filemtime($file) > strtotime("-" . $this->cache[$key] . " days")) {
          $cache = file_get_contents($file);
          $this->caches[$key][$token] = $cache;
          return $cache;
        } else {
          $this->debug('[' . $key . '] cache expired');
        }
      } else {
        $this->debug('[' . $key . '] uncached');
      }
    } else {
      $this->debug('[' . $key . '] no valid key');
    }
    return false;
  }

  public function delete($key, $token)
  {
    if (is_string($file = $this->validate($key, $token)) === true) {
      if (is_file($file)) {
        $this->debug('deleting cache: ' . $key, 'debug');
        return unlink($file);
      } else {
        $this->warning('to delete cache file not found');
      }
    }
    return false;
  }

  public function writte($key, $token, $data)
  {
    if (is_string($file = $this->validate($key, $token)) === true) {
      //-> checks data is encrypted
      if (is_string($data) === true && substr_count($data, '.') === 1) {
        $this->checkDir(str_replace(basename($file), null, $file));
        return file_put_contents($file, $data, LOCK_EX);
      } else {
        $this->error('cache data not properly encrypted');
      }
    }
    return false;
  }

  protected function validate($key, $token)
  {
    if (isset($this->cache[$key])) {
      if (is_string($token) === true) {
        return $this->route->cache($key, $token);
      } else {
        $this->error('[cache] token no valid');
      }
    } else {
      $this->error('[cache] key no valid');
    }
    return false;
  }

  private function checkDir($dir)
  {
    if (is_dir($dir) === true) {
      return true;
    }
    return mkdir($dir, 0700, true);
  }

  public function sanitize()
  {
    //-> callled fron cron model
    $error = 0;
    foreach($this->cache as $cache => $expiration) {
      //-> sanitize expirable caches
      if ($expiration !== false) {
        $folder = $this->route->cache($cache);
        foreach(glob($folder . 'cache' . '.' . '*' . '.' . 'data') as $file) {
          if (filemtime($file) > strtotime("-" . $expiration . " days")) {
            if (unlink($file) !== true) {
              $error ++;
            }
          }
        }
      }
    }
    if ($error === 0) {
      return true;
    }
    return false;
  }


}
