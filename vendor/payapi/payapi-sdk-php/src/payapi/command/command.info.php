<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->info()
*
* @TYPE     public
*
* @RETURNS
*           SDK info AND version string
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           string(17) "PayApi SDK v0.0.1"
*
* @TODO
*           include debug info(same than extradata)
*
*/
final class commandInfo extends controller
{

  public function run()
  {
    return $this->render($this->entity->get('___info'));
  }


}
