<?php

namespace payapi;

//-> @NOTE @CARE "php://input" is not available with enctype="multipart/form-data"
final class knock extends helper
{

  public function listen()
  {
    //-> CHECK SSL(IP) on api
    //$server = json_encode(stream_get_wrappers(), true);
    //$this->debug('[server] ___stream : ' . $server);
    if (getenv('REQUEST_METHOD') == 'POST') {
      //->
      $this->debug('access from : ' . 'TODO');
      if ($this->sslEnabled() !== false) { // TODO check incomming domain $this->checkIncomingHasValidSsl
        $this->debug('[ACK] success');
        $this->debug->lapse('knock', true);
        $jsonExpected = $this->stream(fopen("php://input", "r"));
        $this->debug->lapse('knock');
        if (is_bool($jsonExpected) === false && is_string($jsonExpected) === true && strlen($jsonExpected) > 12) {
          $dataExpected = json_decode($jsonExpected, true);
          if (isset($dataExpected['data']) && is_object($dataExpected['data']) === false && is_string($dataExpected['data']) !== false && substr_count($dataExpected['data'], '.') == 2) { //-> && is_string($array['data']) === true && substr_count($dataExpected['data'], '.') == 2
            $this->debug('[ACK] success');
            return $dataExpected['data'];
          } else {
            $this->warning('unexpected ', 'knock');
          }
        } else {
          $this->warning('empty ', 'knock');
        }
      } else {
        $this->warning('no valid ', 'ssl');
      }
    } else {
      $this->debug('[knock] method not allowed');
    }
    unset($jsonExpected);
    return false;
  }

  private function stream($foo)
  {
    $blocked = false;
    $stream = null;
    while(($line = fread($foo, 64)) && $blocked === false) {
      if (isset($stream) === null && $blocked === false && md5(substr($line, 0, 9)) != md5('{"data":"')) {
        //-> @TODO review on error (gets unexpected curl)
        $this->warning('stream blocked', 'filter');
        $blocked = true;
      }
      $stream .= $line;
    }
    fclose($foo);
    return $stream;
  }

  private function sslEnabled()
  {
    //->
    return true;
  }


}
