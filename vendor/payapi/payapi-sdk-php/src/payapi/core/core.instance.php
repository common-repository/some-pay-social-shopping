<?php

namespace payapi;

final class instance
{

  public static function this()
  {
    return self::encode(self::domain());
  }

  public static function get($domain)
  {
    return self::encode($domain);
  }

  public static function domain()
  {
    return getenv('SERVER_NAME');
  }

  private static function encode($decoded)
  {
    if (is_string($decoded) === true) {
      return md5($decoded);
    }
    return false;
  }

}
