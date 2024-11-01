<?php

namespace payapi;

final class api extends helper
{

  public
    $version                       =   '0.0.1';

  private
    $curl                          =     false,
    $knock                         =     false,
    $code                          =     false,
    $entity                        =     false,
    $headers                       =     false,
    $timeout                       =         1,
    //$id                            =     false,
    $index                         =     'no',
    $follow                        =     'no',
    $mode                          =     'sdk',
    $modes                         =    array(
      'json' => 'application/json',
      'html' => 'text/html',
      'sdk'  => false
    ),
    $responses                     =    array(
      // @NOTE PHP ZEND INTERNAL STATUS HEADERS

      // Informational 1xx
      100 =>                        'continue',
      101 =>             'switching Protocols',

      // Success 2xx
      200 =>                         'success',
      201 =>                         'created',
      202 =>                        'accepted',
      203 =>   'non-Authoritative Information',
      204 =>                      'no Content',
      205 =>                   'reset Content',
      206 =>                 'partial Content',

      // Redirection 3xx
      300 =>                'multiple choices',
      301 =>               'moved permanently',
      302 =>                           'found',  // 1.1
      303 =>                       'see Other',
      304 =>                    'not modified',
      305 =>                       'use proxy',
      // 306 is deprecated but reserved
      307 =>              'temporary redirect',

      // Client Error 4xx
      400 =>                     'bad request',
      401 =>                    'unauthorized',
      402 =>                'payment required',
      403 =>                       'forbidden',
      404 =>                       'not found',
      405 =>              'method not allowed',
      406 =>                  'not acceptable',
      407 =>   'proxy authentication required',
      408 =>                 'request timeout',
      409 =>                        'conflict',
      410 =>                            'gone',
      411 =>                 'length required',
      412 =>             'precondition failed',
      413 =>        'request entity too large',
      414 =>            'request-uri too long',
      415 =>          'unsupported media type',
      416 => 'requested range not satisfiable',
      417 =>              'expectation failed',

      // Server Error 5xx
      500 =>           'internal server error',
      501 =>                 'not implemented',
      502 =>                     'bad gateway',
      503 =>             'service unavailable',
      504 =>                 'gateway timeout',
      505 =>      'http version not supported',
      509 =>        'bandwidth limit exceeded',

      // @NOTE Extra One(s) 6xx  :)
      600 =>                         'boo boo'
     );

  protected function ___autoload()
  {
    $this->ip = $this->getIp();
  }

  public function ip()
  {
    return $this->ip;
  }

  public function render($data, $code)
  {
    //->
    $label =(($code === 200) ? 'data' : 'error');
    $this->buffer = array(
      "code" => $code,
      $label => $data
    );
    $this->code =($this->checkCode($code) === true) ? $code : 600;
    $this->headers();
    $this->listening();
    $this->debug->blank('=== LISTENING ==>');
    return $this->buffer;
  }

  private function listening()
  {
    $this->debug('[' . $this->code . '] rendering', 'api');
    $this->debug->run();
  }

  public function response($code = false)
  {
    return $this->getApiResponse($code);
  }

  public function returnResponse($code)
  {
    $code =($this->checkCode($code) === true) ? $code : 600;
    return $this->render($this->responses[$code], $code);
  }

  public function curl($url, $secured = true, $post = false, $timeout = 1, $return = 1, $header = 0, $ssl = 1, $fresh = 1, $noreuse = 1)
  {
    if ($this->curl === false) {
      $this->curl = new curl();
    }
    $checkTimeout =($this->config->staging() === true) ? 2 : $timeout;
    $response = $this->curl->proccess($url, $secured, $post, $checkTimeout, $return, $header, $ssl, $fresh, $noreuse);
    //->
    return $response;
  }

  public function knock()
  {
    if ($this->knock === false) {
      $this->knock = new knock();
    }
    $knock = $this->knock->listen();
    //->
    return $knock;
  }

  public function defaultCode()
  {
    end($this->responses);
    return key($this->responses);
  }

  private function undefinedIp()
  {
    return 'undefined';
  }

  public function code($responseCode)
  {
    if ($this->isCleanCodeInt($responseCode) === true && isset($this->responses[$responseCode])) {
      return $responseCode;
    }
    return $this->error->notAcceptable();
  }

  public function checkCode($responseCode)
  {
    if ($this->isCleanCodeInt($responseCode) === true && isset($this->responses[$responseCode]) === true) {
      return true;
    }
    return false;
  }

  private function headers()
  {
    if ($this->headers !== true) {
      $this->debug('[headers] disabled');
      return true;
    }
    $this->debug('[headers] ' . $this->mode);
    if(isset($this->modes[$this->mode]) === true && is_string($this->modes[$this->mode]) === true) {
      header('Content-type: ' . $this->modes[$this->mode]);
    }
    header("X-Robots-Tag: " . $this->index . "index," . $this->follow . "follow");
    return http_response_code($this->code);
  }

  public function mode($mode)
  {
    if(is_string($mode) === true && isset($this->modes[$mode]) === true) {
      return $this->mode = $mode;
    }
    $this->warning('no valid api mode');
    return false;
  }

  public function index($status = false)
  {
    if ($status === true) {
      return $this->index = null;
    }
    return $this->index = 'no';
  }

  public function follow($status = false)
  {
    if ($status === true) {
      return $this->follow = null;
    }
    return $this->follow = 'no';
  }

  private function getApiResponse($responseCode)
  {
    if ($this->isCleanCodeInt($responseCode) === true && isset($this->responses[$responseCode]) === true) {
      $code = $responseCode;
    } else {
      $this->debug('response code no valid', 'api');
      $code = $this->error->notAcceptable();
    }
    return $this->responses[$code];
  }

  private function getIp()
  {
    if (($access = $this->filterIp(getenv('HTTP_CLIENT_IP'))) == false)
      if (($access = $this->filterIp(getenv('HTTP_X_FORWARDED_FOR'))) == false)
        if (($access = $this->filterIp(getenv('HTTP_X_FORWARDED'))) == false)
          if (($access = $this->filterIp(getenv('HTTP_FORWARDED_FOR'))) == false)
            if (($access = $this->filterIp(getenv('HTTP_FORWARDED'))) == false)
              if (($access = $this->filterIp(getenv('REMOTE_ADDR'))) == false)
                $access = $this->undefinedIp();
    $ip = htmlspecialchars($access, ENT_COMPAT, 'UTF-8');
    return $ip;
  }

  private function hackAccess()
  {
    $this->warning('access IP hacked');
    //-> check core.instance.php
    $this->warning('SERVER NAME hacked');
    return '84.79.234.58';
    return '84.79.' . rand(100, 200) . '.' . rand(100, 200);
  }

  public function filterIp($ip)
  {
    if (md5(filter_var($ip, FILTER_VALIDATE_IP)) === md5($ip) && ip2long($ip) !== false) {
      return preg_replace("/[^0-0.\d]/i", '', $ip);
    }
    return false;
  }

  private function isCleanCodeInt($int)
  {
    if (is_int($int) === true && $int >= 200 && $int <= 600) {
      return true;
    }
    return false;
  }

  public function checkIncomingHasValidSsl($url)
  {
    return $this->checkSsl($url);
  }

  public function __toString()
  {
    return $this->version;
  }


}
