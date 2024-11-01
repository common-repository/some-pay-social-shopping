<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->callback()
*
* @TYPE     private
*
* @PARAMS
*           php://input
*
* @RETURNS
*           callback order payload
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(6) {
*            ["payment"]=>
*             array(1) {
*              ["status"]=>
*               string(10) "processing"
*             }
*            ["order"]=>
*             array(5) {
*              ["sumInCentsIncVat"]=>
*               int(322)
*              ["sumInCentsExcVat"]=>
*               int(300)
*              ["vatInCents"]=>
*               int(22)
*              ["currency"]=>
*               string(3) "EUR"
*              ["referenceId"]=>
*               string(6) "ref123"
*             }
*            ["products"]=>
*             array(2) {
*              [0]=>
*               array(13) {
*                ["id"]=>
*                 string(9) "bbc123456"
*                ["quantity"]=>
*                 int(1)
*                ["title"]=>
*                 string(15) "Black bling cap"
*                ["description"]=>
*                 string(15) "Flashy fine cap"
*                ["imageUrl"]=>
*                 string(39) "https://example.com/black_bling_cap.png"
*                ["category"]=>
*                 string(13) "Caps and hats"
*                ["options"]=>
*                 array(1) {
*                  [0]=>
*                   string(6) "size=1"
*                 }
*                ["model"]=>
*                 string(11) "BLINGCAP123"
*                ["priceInCentsIncVat"]=>
*                 int(122)
*                ["priceInCentsExcVat"]=>
*                 int(100)
*                ["vatInCents"]=>
*                 int(22)
*                ["vatPercentage"]=>
*                 int(22)
*                ["extraData"]=>
*                 string(37) "manufacturer=Bling Bling&origin=China"
*               }
*              [1]=>
*               array(13) {
*                ["id"]=>
*                 string(9) "pbc123456"
*                ["quantity"]=>
*                 int(1)
*                ["title"]=>
*                 string(14) "Pink bling cap"
*                ["description"]=>
*                 string(15) "Flashy fine cap"
*                ["imageUrl"]=>
*                 string(38) "https://example.com/pink_bling_cap.png"
*                ["category"]=>
*                 string(13) "Caps and hats"
*                ["options"]=>
*                 array(1) {
*                  [0]=>
*                   string(6) "size=2"
*                 }
*                ["model"]=>
*                 string(11) "BLINGCAP123"
*                ["priceInCentsIncVat"]=>
*                 int(222)
*                ["priceInCentsExcVat"]=>
*                 int(200)
*                ["vatInCents"]=>
*                 int(22)
*                ["vatPercentage"]=>
*                 int(22)
*                ["extraData"]=>
*                 string(37) "manufacturer=Bling Bling&origin=China"
*               }
*             }
*            ["shippingAddress"]=>
*             array(8) {
*              ["recipientName"]=>
*               string(10) "John Smith"
*              ["co"]=>
*               string(8) "Jane Doe"
*              ["streetAddress"]=>
*               string(19) "Delivery street 123"
*              ["streetAddress2"]=>
*               string(9) "Apt. 1202"
*              ["postalCode"]=>
*               string(5) "90210"
*              ["city"]=>
*               string(8) "New York"
*              ["stateOrProvince"]=>
*               string(2) "NY"
*              ["countryCode"]=>
*               string(2) "US"
*             }
*            ["consumer"]=>
*             array(4) {
*              ["consumerId"]=>
*               string(9) "happyjohn"
*              ["email"]=>
*               string(25) "happyconsumer@example.com"
*              ["locale"]=>
*               string(5) "en-US"
*              ["mobilePhoneNumber"]=>
*               string(11) "34123456789"
*             }
*            ["extraInputData"]=>
*             array(2) {
*              ["message"]=>
*               string(19) "message to merchant"
*              ["tableNumber"]=>
*               int(12)
*             }
*           }
*
* @NOTE
*          transactions are cached per status
*          duplicated status updates are not allowed
*
* @VALID
*          schema.callback*
*
* @TODO
*          handle order through adaptor/plugin
*
*/
final class commandCallback extends controller
{

    public function run()
    {
        //-> returnResponse should display a json response and send headers
        //-> enable api headers?
        //-> http_response_code($code);
        $knock = $this->knock();
        $sanitized = array();

        if (is_string($knock) !== true) {
            return $this->returnResponse($this->error->knockNotValid());
        }
        $knockDecoded = $this->decode($knock, $this->apiKey());
        if (is_string($knockDecoded) !== true) {
            return $this->returnResponse($this->error->knockUnexpectedSignature());
        }
        $knockData = json_decode($knockDecoded, true);
        if (is_array($knockData) === false) {
            return $this->returnResponse($this->error->notValidSchema());
        }
        $knockValidated = $this->validate->schema($knockData, $this->load->schema('callback'));
        if (is_array($knockValidated) !== false) {
            $error = 0;
            foreach ($knockValidated as $schema => $data) {
                if ($schema === 'products') {
                    foreach ($data as $key => $product) {
                        $productValidated = $this->validate->schema(
                            $product,
                            $this->load->schema('callback' . '.' . 'products')
                        );
                        if (is_array($productValidated) !== false) {
                            $sanitized['products'][] = $productValidated;
                        } else {
                            $error++;
                        }
                    }
                } else {
                    if ($data !== false) {
                        $dataValidated = $this->validate->schema(
                            $data,
                            $this->load->schema('callback' . '.' . $schema)
                        );
                        if (is_array($dataValidated) !== false) {
                            $sanitized[$schema] = $dataValidated;
                        } else {
                            $this->warning($schema, 'schema');
                            $error++;
                        }
                    }
                }
            }
        } else {
            $error++;
        }

        if ($error === 0) {
            $duplicated = $this->cache(
                'read',
                'transaction',
                $knockData['payment']['status'] . $knockData['order']['referenceId']
            );
            if ($duplicated !== false) {
                return $this->returnResponse($this->error->transactionDuplicated());
            }
            $this->cache(
                'writte',
                'transaction',
                $knockData['payment']['status'] . $knockData['order']['referenceId'],
                $sanitized
            );
            return $this->render($sanitized);
        } else {
            return $this->returnResponse($this->error->notValidSchema());
        }
    }
}
