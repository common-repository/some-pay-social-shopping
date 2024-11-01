<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->product($product)
*
* @TYPE     public
*
* @RETURNS
*           adapted product array
*
* @TODO
*           ---/---
*
*/


class commandProduct extends controller
{

    private $product = false;
    private $schema = array(
        "id" => true,
        "url" => true,
        "mandatory" => true,
        "priceInCents" => true
    );

    public function run()
    {	
    	if (is_array($this->arguments(0)) === true) {
    		$this->product = $this->adaptor->product($this->arguments(0));
    		if (is_array($this->product) === true && $this->validateProduct($this->product) === true) {
                $paymentEndPoint = null;
                $this->product['paymentPost'] = $paymentEndPoint . 'undefined';
                $this->product['paymentInstant'] = $paymentEndPoint . 'undefined';
                $this->product['paymentPartial'] = $paymentEndPoint . 'undefined';
                $this->product['payload'] = 'undefined';
                $this->product['metadata'] = 'undefined';
	        	return $this->render($product);
    		} else {
    			$this->warning('product adaption failed');
    			return $this->returnResponse($this->error->unexpectedResponse());
    		}
    	}
    	return $this->returnResponse($this->error->badRequest());
    }

    private function validateProduct($product)
    {
        $error = 0;
        foreach ($this->schema as $key => $mandatory) {
            if (isset($product[$key]) !== true && $mandatory === true) {
                $error ++;
            }
        }
        if ($error === 0) {
            return true;
        }
        return false;
    }

    private function queryParams()
    {
        //->
        $public = md5($this->publicId() . md5(date('Ymd', time())));
    }


}