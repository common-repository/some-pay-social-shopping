<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->response($code)
*
* @TYPE     private
*
* @RETURNS
*           standard PHP response/error(+600)
*
* @SAMPLE
*          ["code"]=>
*           int(501)
*          ["error"]=>
*           string(15) "not implemented"
*
* @NOTE
*           this command is mostly for testing
*
*/
final class commandResponse extends controller
{

  public function run()
  {
    return $this->returnResponse($this->arguments(0));
  }


}
