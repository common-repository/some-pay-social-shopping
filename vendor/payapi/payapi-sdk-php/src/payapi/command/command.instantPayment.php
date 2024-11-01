<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->instantPayment($product)
*
* @TYPE     private
*
* @RETURNS
*           instantPayment metadata/endpoints
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(4) {
*            ["payload"]=>
*             string(1569) "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcImlvXCI6e1wicGF5YXBpLndlYnNob3BcIjpcIm11bHRpbWVyY2hhbnRzaG9wXCJ9LFwicHJvZHVjdFwiOntcImlkXCI6XCJyZWY4NzU2N1wiLFwidXJsXCI6XCJodHRwczpcXFwvXFxcL3N0b3JlLm11bHRpbWVyY2hhbnRzaG9wLnh5elxcXC9pbmRleC5waHA_cm91dGU9cHJvZHVjdFxcXC9wcm9kdWN0JnByb2R1Y3RfaWQ9NDNcIixcInRpdGxlXCI6XCJQcm9kdWN0IDFcIixcImltYWdlVXJsXCI6XCJodHRwczpcXFwvXFxcL3N0b3JlLnBheWFwaS5pb1xcXC9tZWRpYVxcXC80MzMwN2FjN2YzNTZkNTFlNmRkNjViOGNhOWZlM2Q5M1xcXC9pbWFnZVxcXC9jYWNoZVxcXC9jYXRhbG9nXFxcL1VzZXJzXFxcL1VzZXI0XFxcL3BheWFwaV9wcmVtaXVtX3N1cHBvcnQtMjI4eDIyOC5qcGdcIixcImNhdGVnb3J5XCI6XCJjYXRlZ29yeSAxXCIsXCJwcmljZUluQ2VudHNFeGNWYXRcIjoyMDAwMCxcInByaWNlSW5DZW50c0luY1ZhdFwiOjI0MDAwLFwib3B0aW9uc1wiOntcImNvbG9yXCI6XCJibHVlXCIsXCJzaXplXCI6XCJYWExcIn0sXCJxdWFudGl0eVwiOjEsXCJ2YXRJbkNlbnRzXCI6NDAwMCxcInZhdFBlcmNlbnRhZ2VcIjoyMH0sXCJvcmRlclwiOntcImN1cnJlbmN5XCI6XCJFVVJcIn0sXCJjYWxsYmFja3NcIjp7XCJwcm9jZXNzaW5nXCI6XCJodHRwcyUzQSUyRiUyRmFwaS5leGFtcGxlLmNvbSUyRmNhbGxiYWNrLXByb2Nlc3NpbmdcIixcInN1Y2Nlc3NcIjpcImh0dHBzJTNBJTJGJTJGYXBpLmV4YW1wbGUuY29tJTJGY2FsbGJhY2stc3VjY2Vzc1wiLFwiZmFpbGVkXCI6XCJodHRwcyUzQSUyRiUyRmFwaS5leGFtcGxlLmNvbSUyRmNhbGxiYWNrLWZhaWxlZFwiLFwiY2hhcmdlYmFja1wiOlwiaHR0cHMlM0ElMkYlMkZhcGkuZXhhbXBsZS5jb20lMkZjYWxsYmFjay1jaGFyZ2ViYWNrXCJ9LFwicmV0dXJuVXJsc1wiOntcInN1Y2Nlc3NcIjpcImh0dHBzJTNBJTJGJTJGc3RvcmUuZXhhbXBsZS5jb20lMkZwYXltZW50LXN1Y2Nlc3NcIixcImNhbmNlbFwiOlwiaHR0cHMlM0ElMkYlMkZzdG9yZS5leGFtcGxlLmNvbSUyRnBheW1lbnQtY2FuY2VsXCIsXCJmYWlsZWRcIjpcImh0dHBzJTNBJTJGJTJGc3RvcmUuZXhhbXBsZS5jb20lMkZwYXltZW50LWZhaWxlZFwifX0i.iK_4xO5lr3-MtNRQmgEccsM0uC70-TnpACzkNFmVlu8"
*            ["metadata"]=>
*             string(1519) "<meta name="io.payapi.webshop" content="your_public_id">
*           <meta name="product.id" content="ref87567">
*           <meta name="product.url" content="https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=43">
*           <meta name="product.title" content="Product 1">
*           <meta name="product.imageUrl" content="https://store.payapi.io/media/43307ac7f356d51e6dd65b8ca9fe3d93/image/cache/catalog/Users/User4/payapi_premium_support-228x228.jpg">
*           <meta name="product.category" content="category 1">
*           <meta name="product.priceInCentsExcVat" content="20000">
*           <meta name="product.priceInCentsIncVat" content="24000">
*           <meta name="product.options" content="color=blue&size=XXL">
*           <meta name="product.quantity" content="1">
*           <meta name="product.vatInCents" content="4000">
*           <meta name="product.vatPercentage" content="20">
*           <meta name="order.currency" content="EUR">
*           <meta name="callbacks.processing" content="https%3A%2F%2Fapi.example.com%2Fcallback-processing">
*           <meta name="callbacks.success" content="https%3A%2F%2Fapi.example.com%2Fcallback-success">
*           <meta name="callbacks.failed" content="https%3A%2F%2Fapi.example.com%2Fcallback-failed">
*           <meta name="callbacks.chargeback" content="https%3A%2F%2Fapi.example.com%2Fcallback-chargeback">
*           <meta name="returnUrls.success" content="https%3A%2F%2Fstore.example.com%2Fpayment-success">
*           <meta name="returnUrls.cancel" content="https%3A%2F%2Fstore.example.com%2Fpayment-cancel">
*           <meta name="returnUrls.failed" content="https%3A%2F%2Fstore.example.com%2Fpayment-failed">"
*            ["endPointInstantBuy"]=>
*             string(61) "https://staging-input.payapi.io/v1/webshop/your_public_id/"
*            ["endPointProductInstantBuy"]=>
*             string(160) "https://staging-input.payapi.io/v1/webshop/your_public_id/https%3A%2F%2Fstore.multimerchantshop.xyz%2Findex.php%3Froute%3Dproduct%2Fproduct%26product_id%3D43"
*
* @NOTE
*           product data is adapted through plugin
*
* @VALID
*          schema.instantPayment.*
*
* @TODO
*           hanldle extraData
*           handle stockage ?
*
*/
final class commandInstantPayment extends controller
{

    protected $payment = false;

    public function run()
    {
        $data = $this->arguments(0);
        //-> @FIXME ???
        $data['product'] = $this->adaptor->product($data['product']);
        $error = 0;
        $md5 = md5(json_encode($data, JSON_HEX_TAG));
        $cache = $this->cache ('read', 'product', $md5);
        if ($cache !== false) {
            return $cache;
        }

        if (is_array($this->validate->schema($data, $this->load->schema('instantPayment'))) === true) {
            $sanitized = array();
            foreach ($data as $key => $value) {
                $sanitization = $this->validate->schema($value, $this->load->schema('instantPayment.' . $key));
                if (is_array($sanitization) === true) {
                    $sanitized[$key] = $sanitization;
                } else {
                    $error++;
                }
            }
        } else {
            $error = 1;
        }

        if ($error !== 0) {
            $this->debug('not valid', 'schema');
            return $this->returnResponse($this->error->badRequest());
        }

        $this->debug('[schema] valid');
        $this->payment = array_merge(
            array("io" => array("payapi.webshop" => $this->publicId())),
            $this->product($sanitized)
        );
        $metaData = $this->metadata($this->payment);
        $productUrl =
            $this->payment['product']['url'] . '&' . 'extraData' . '=' . $this->extraData($data['product']);
        //-> @TODO options/extraData
        $product = array(
            //"product"                 => $this->payment,
            //"payload"                 => $this->encode($payloadJson, $this->publicId()),
            "metadata"                  => $metaData,
            "productUrl"                => $productUrl,
            "endPointInstantBuy"        => $this->serialize->endPointInstantBuy($this->publicId()),
            "endPointProductInstantBuy" => $this->serialize->endPointInstantBuy($this->publicId()) . $this->serialize->paymentUrlEncode($productUrl)
        );
        $this->cache('writte', 'product', $md5);

        return $this->render($product);
    }

    private function extraData($product)
    {
        $options = array();
        foreach ($product['options'] as $key => $value) {
            $options[$key] = $value;
        }
        $data = array(
            'options' => $options,
            'ip' => $this->ip(),
            'currency' => null,
            'locale' => null
        );
        $flag = $this->encode(json_encode($data, JSON_HEX_TAG), false, true);
        return $flag;
    }

    private function metadata()
    {
        //-> @TODO
        $data = $this->payment;
        $metaData = null;
        foreach ($data as $key => $value) {
            if (is_array($value) !== false) {
                foreach ($value as $meta => $content) {
                    if ($meta === 'options' && is_array($data[$key][$meta]) !== false) {
                        $contentParsed = $this->serialize->options($data[$key][$meta]);
                    } else {
                        $contentParsed = $data[$key][$meta];
                    }
                    $metaData .= '<meta name="' . $key . '.' . $meta . '" content="' . $contentParsed . '">' . "\r\n";
                }
            }
        }
        return $metaData;
    }

    private function product($data)
    {
        $data['product']['vatInCents'] =
            $data['product']['priceInCentsIncVat'] - $data['product']['priceInCentsExcVat'];
        $data['product']['vatPercentage'] =
            $this->serialize->percentage($data['product']['priceInCentsIncVat'], $data['product']['vatInCents']);
        return $data;
    }

    private function cacheKey($md5)
    {
        $cacheKey = date('YmdHis', time()) .'-' . $md5;
        return $cacheKey;
    }
}
