<?php

namespace payapi;

class translator extends helper
{

  private
    $folder              =    false,
    $translations        = array();

  public function auto()
  {
    $this->folder = $this->route->routeData();
  }

  public function translate($key, $value)
  {
    $translator = $this->load($key);
    if (isset($translator[$value]) === true) {
      return $translator[$value];
    }
    $this->error('translation fails', 'warning');
    return false;
  }

  private function load($key)
  {
    if (isset($this->translatior[$key]) === true) {
      return $this->translator[$key];
    } else {
      $file = $this->route($key);
      if (is_string($file) === true) {
        $translator = json_decode($file, true);
        if (is_array($translator) !== false) {
          $this->translator[$key] = $translator;
          return $this->translator[$key];
        }
        $this->error('malformed translator', 'warning');
      }
    }
    $this->error('not translator available', 'warning');
    return false;
  }


}
