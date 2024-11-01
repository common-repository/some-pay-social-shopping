<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->publicId()
*
* @TYPE     private
*
* @RETURNS
*           publicId string OR $this->error->notFound()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           string(17) "your_public_id"
*
* @NOTE
*          only available after settings/install
*
*/
final class commandPublicId extends controller
{

  public function run()
  {
    if (is_string($this->publicId()) === true) {
      return $this->render($this->publicId());
    }
    return $this->returnResponse($this->error->notFound());
  }


}
