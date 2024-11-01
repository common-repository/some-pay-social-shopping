<?php

namespace payapi;

/*
*
* @IMPORTANT
*           DEPRECATED [20171011] florin
*           moved to $sdk->product($product)
*           cheack out: command.product.php
*
*/

final class commandValidate extends controller
{

  public function run()
  {
    //->
    return $this->returnResponse($this->error->notImplemented());
  }


}
