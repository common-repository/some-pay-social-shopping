<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->payment($data)
*
* @PARAMS
*           $paymentPriceInCents = numeric
*           $paymentCurrency = string
*
* @RETURNS
*           payment signed/unsigned data OR $this->error->badRequest()
*          (included signed payload and endpoints)
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(3) {
*            ["payment"]=>
*             array(4) {
*              ["products"]=>
*               array(2) {
*                [0]=>
*                 array(9) {
*                  ["id"]=>
*                   string(8) "ref87567"
*                  ["url"]=>
*                   string(99) "https%3A%2F%2Fstore.multimerchantshop.xyz%2Findex.php%3Froute%3Dproduct%2Fproduct%26product_id%3D43"
*                  ["title"]=>
*                   string(9) "Product 1"
*                  ["imageUrl"]=>
*                   string(151) "https%3A%2F%2Fstore.payapi.io%2Fmedia%2F43307ac7f356d51e6dd65b8ca9fe3d93%2Fimage%2Fcache%2Fcatalog%2FUsers%2FUser4%2Fpayapi_premium_support-228x228.jpg"
*                  ["category"]=>
*                   string(10) "category 1"
*                  ["priceInCentsExcVat"]=>
*                   int(20000)
*                  ["priceInCentsIncVat"]=>
*                   int(24000)
*                  ["quantity"]=>
*                   int(1)
*                  ["options"]=>
*                   array(2) {
*                    ["color"]=>
*                     string(4) "blue"
*                    ["size"]=>
*                     string(3) "XXL"
*                   }
*                 }
*                [1]=>
*                 array(9) {
*                  ["id"]=>
*                   string(8) "ref87568"
*                  ["url"]=>
*                   string(99) "https%3A%2F%2Fstore.multimerchantshop.xyz%2Findex.php%3Froute%3Dproduct%2Fproduct%26product_id%3D43"
*                  ["title"]=>
*                   string(9) "Product 2"
*                  ["imageUrl"]=>
*                   string(151) "https%3A%2F%2Fstore.payapi.io%2Fmedia%2F43307ac7f356d51e6dd65b8ca9fe3d93%2Fimage%2Fcache%2Fcatalog%2FUsers%2FUser4%2Fpayapi_premium_support-228x228.jpg"
*                  ["category"]=>
*                   string(10) "category 2"
*                  ["priceInCentsExcVat"]=>
*                   int(20000)
*                  ["priceInCentsIncVat"]=>
*                   int(24000)
*                  ["quantity"]=>
*                   int(2)
*                  ["options"]=>
*                   array(2) {
*                    ["color"]=>
*                     string(3) "red"
*                    ["size"]=>
*                     string(2) "XL"
*                   }
*                 }
*               }
*              ["order"]=>
*               array(6) {
*                ["sumInCentsIncVat"]=>
*                 int(72000)
*                ["sumInCentsExcVat"]=>
*                 int(60000)
*                ["vatInCents"]=>
*                 int(12000)
*                ["currency"]=>
*                 string(3) "EUR"
*                ["referenceId"]=>
*                 string(41) "REF-df0fab450ad31386979154ad017bba98-test"
*                ["tosUrl"]=>
*                 string(39) "https%3A%2F%2Fstore.example.com%2Fterms"
*               }
*              ["callbacks"]=>
*               array(4) {
*                ["processing"]=>
*                 string(51) "https%3A%2F%2Fapi.example.com%2Fcallback-processing"
*                ["success"]=>
*                 string(48) "https%3A%2F%2Fapi.example.com%2Fcallback-success"
*                ["failed"]=>
*                 string(47) "https%3A%2F%2Fapi.example.com%2Fcallback-failed"
*                ["chargeback"]=>
*                 string(51) "https%3A%2F%2Fapi.example.com%2Fcallback-chargeback"
*               }
*              ["returnUrls"]=>
*               array(3) {
*                ["success"]=>
*                 string(49) "https%3A%2F%2Fstore.example.com%2Fpayment-success"
*                ["cancel"]=>
*                 string(48) "https%3A%2F%2Fstore.example.com%2Fpayment-cancel"
*                ["failed"]=>
*                 string(48) "https%3A%2F%2Fstore.example.com%2Fpayment-failed"
*               }
*             }
*            ["payload"]=>
*             string(2340) "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcInByb2R1Y3RzXCI6W3tcImlkXCI6XCJyZWY4NzU2N1wiLFwidXJsXCI6XCJodHRwcyUzQSUyRiUyRnN0b3JlLm11bHRpbWVyY2hhbnRzaG9wLnh5eiUyRmluZGV4LnBocCUzRnJvdXRlJTNEcHJvZHVjdCUyRnByb2R1Y3QlMjZwcm9kdWN0X2lkJTNENDNcIixcInRpdGxlXCI6XCJQcm9kdWN0IDFcIixcImltYWdlVXJsXCI6XCJodHRwcyUzQSUyRiUyRnN0b3JlLnBheWFwaS5pbyUyRm1lZGlhJTJGNDMzMDdhYzdmMzU2ZDUxZTZkZDY1YjhjYTlmZTNkOTMlMkZpbWFnZSUyRmNhY2hlJTJGY2F0YWxvZyUyRlVzZXJzJTJGVXNlcjQlMkZwYXlhcGlfcHJlbWl1bV9zdXBwb3J0LTIyOHgyMjguanBnXCIsXCJjYXRlZ29yeVwiOlwiY2F0ZWdvcnkgMVwiLFwicHJpY2VJbkNlbnRzRXhjVmF0XCI6MjAwMDAsXCJwcmljZUluQ2VudHNJbmNWYXRcIjoyNDAwMCxcInF1YW50aXR5XCI6MSxcIm9wdGlvbnNcIjp7XCJjb2xvclwiOlwiYmx1ZVwiLFwic2l6ZVwiOlwiWFhMXCJ9fSx7XCJpZFwiOlwicmVmODc1NjhcIixcInVybFwiOlwiaHR0cHMlM0ElMkYlMkZzdG9yZS5tdWx0aW1lcmNoYW50c2hvcC54eXolMkZpbmRleC5waHAlM0Zyb3V0ZSUzRHByb2R1Y3QlMkZwcm9kdWN0JTI2cHJvZHVjdF9pZCUzRDQzXCIsXCJ0aXRsZVwiOlwiUHJvZHVjdCAyXCIsXCJpbWFnZVVybFwiOlwiaHR0cHMlM0ElMkYlMkZzdG9yZS5wYXlhcGkuaW8lMkZtZWRpYSUyRjQzMzA3YWM3ZjM1NmQ1MWU2ZGQ2NWI4Y2E5ZmUzZDkzJTJGaW1hZ2UlMkZjYWNoZSUyRmNhdGFsb2clMkZVc2VycyUyRlVzZXI0JTJGcGF5YXBpX3ByZW1pdW1fc3VwcG9ydC0yMjh4MjI4LmpwZ1wiLFwiY2F0ZWdvcnlcIjpcImNhdGVnb3J5IDJcIixcInByaWNlSW5DZW50c0V4Y1ZhdFwiOjIwMDAwLFwicHJpY2VJbkNlbnRzSW5jVmF0XCI6MjQwMDAsXCJxdWFudGl0eVwiOjIsXCJvcHRpb25zXCI6e1wiY29sb3JcIjpcInJlZFwiLFwic2l6ZVwiOlwiWExcIn19XSxcIm9yZGVyXCI6e1wic3VtSW5DZW50c0luY1ZhdFwiOjcyMDAwLFwic3VtSW5DZW50c0V4Y1ZhdFwiOjYwMDAwLFwidmF0SW5DZW50c1wiOjEyMDAwLFwiY3VycmVuY3lcIjpcIkVVUlwiLFwicmVmZXJlbmNlSWRcIjpcIlJFRi1kZjBmYWI0NTBhZDMxMzg2OTc5MTU0YWQwMTdiYmE5OC10ZXN0XCIsXCJ0b3NVcmxcIjpcImh0dHBzJTNBJTJGJTJGc3RvcmUuZXhhbXBsZS5jb20lMkZ0ZXJtc1wifSxcImNhbGxiYWNrc1wiOntcInByb2Nlc3NpbmdcIjpcImh0dHBzJTNBJTJGJTJGYXBpLmV4YW1wbGUuY29tJTJGY2FsbGJhY2stcHJvY2Vzc2luZ1wiLFwic3VjY2Vzc1wiOlwiaHR0cHMlM0ElMkYlMkZhcGkuZXhhbXBsZS5jb20lMkZjYWxsYmFjay1zdWNjZXNzXCIsXCJmYWlsZWRcIjpcImh0dHBzJTNBJTJGJTJGYXBpLmV4YW1wbGUuY29tJTJGY2FsbGJhY2stZmFpbGVkXCIsXCJjaGFyZ2ViYWNrXCI6XCJodHRwcyUzQSUyRiUyRmFwaS5leGFtcGxlLmNvbSUyRmNhbGxiYWNrLWNoYXJnZWJhY2tcIn0sXCJyZXR1cm5VcmxzXCI6e1wic3VjY2Vzc1wiOlwiaHR0cHMlM0ElMkYlMkZzdG9yZS5leGFtcGxlLmNvbSUyRnBheW1lbnQtc3VjY2Vzc1wiLFwiY2FuY2VsXCI6XCJodHRwcyUzQSUyRiUyRnN0b3JlLmV4YW1wbGUuY29tJTJGcGF5bWVudC1jYW5jZWxcIixcImZhaWxlZFwiOlwiaHR0cHMlM0ElMkYlMkZzdG9yZS5leGFtcGxlLmNvbSUyRnBheW1lbnQtZmFpbGVkXCJ9fSI.hJAf-ROJ6qCjpqchiuBMn-4ZmvE1R9u0ImTyf4X4ZI4"
*            ["endPointPayment"]=>
*             string(64) "https://staging-input.payapi.io/v1/secureform/your_public_id/"
*
* @NOTE
*          $products are adapted through plugin
*
* @TODO
*          handle shipping
*          adapt other info(consumer, callbacks, returns, etcetera...)
*
*/
class commandPayment extends controller
{

    private $payment = false;

    public function run()
    {
        $data = $this->arguments(0);
        $partialPaymentMethod = $this->arguments(1);
        //error_log(' SDK partialPaymentMethod: '.$partialPaymentMethod, 0);
        $data = $this->adaptor->payment($data, $partialPaymentMethod);
        $error = 0;
        $md5 = md5(json_encode($data, JSON_HEX_TAG));
        $cache = $this->cache ('read', 'payment', $md5);
        if ($cache !== false && 1 === 2) {
            return $cache;
        } else {
            if (is_array($this->validate->schema($data, $this->load->schema('payment'))) !== false) {
                /*
                $sanitized = array(
                    'order' => array()
                );
                 */
                foreach ($data as $key => $value) {
                    if ($key !== 'products') {
                        $sanitization = $this->validate->schema($value, $this->load->schema('payment.' . $key));
                        if (is_array($sanitization) !== true) {
                            $error++;
                        } else {
                            $sanitized[$key] = $sanitization;
                        }
                    } else {
                        foreach ($value as $key => $product) {
                            $sanitization = $this->validate->schema(
                                $product,
                                $this->load->schema('payment' . '.' . 'products')
                            );
                            if (is_array($sanitization) !== true) {
                                $error++;
                            } else {
                                $sanitized['products'][] = $sanitization;
                            }
                        }
                    }
                }
            } else {
                $error = 1;
            }
            if ($error === 0) {
                $this->payment = $sanitized;
                $this->debug('[schema] valid');
                /*
                $data = array(
                  'title' => 'shipping and handling',
                  'model' => 'shipping',
                  'category' => 'shipping',
                  'priceInCentsExcVat' => 1000,
                  'priceInCentsIncVat' => 1200
                );
                $this->addShipping($data);
                $this->payment = $this->product($this->payment);
                $this->order();
                 */
                // $payloadJson = json_encode($this->payment, true);
                $payloadJwt = $this->encode($this->payment, $this->apiKey());
                $payment = array(
                    "payment"            => $this->payment,
                    "payload"            => $payloadJwt,
                    //"decoded"            => $this->decode($payloadJwt, $this->publicId()),
                    "endPointPayment"    => $this->serialize->endPointPayment($this->publicId()),
                    "order_form_js_path" => "payapi/views/js/order_form.js",
                );
                $this->cache('writte', 'payment', $md5);
                return $this->render($payment);
            } else {
                $this->debug('not valid', 'schema');
                return $this->returnResponse($this->error->badRequest());
            }
        }
        return returnResponse($this->error->notImplemented());
    }

    private function product($data)
    {
        foreach ($data['products'] as $key => $product) {
            $data['products'][$key]['vatInCents'] =
                ($data['products'][$key]['priceInCentsIncVat'] - $data['products'][$key]['priceInCentsExcVat']);
            $data['products'][$key]['vatPercentage'] = ($this->serialize->percentage(
                $data['products'][$key]['priceInCentsIncVat'],
                $data['products'][$key]['vatInCents'])
            );
        }
        return $data;
    }

    private function order()
    {
        //-> @TODO handle shipping&handling(add shipping to product array)
        //-> $this->payment['order']['shippingHandlingFeeInCentsIncVat']
        //-> $this->payment['order']['shippingHandlingFeeInCentsExcVat']
        $sumInCentsIncVat = 0;
        $sumInCentsExcVat = 0;
        //-> @TODO @CARE to move to adaptor/plugin
        foreach ($this->payment['products'] as $key => $product) {
            $sumInCentsIncVat +=($product['priceInCentsIncVat'] * $product['quantity']);
            $sumInCentsExcVat +=($product['priceInCentsExcVat'] * $product['quantity']);
        }
        $vatInCents = $sumInCentsIncVat - $sumInCentsExcVat;
        $vatPercentage = $this->serialize->percentage($sumInCentsIncVat, $vatInCents);
        $order = array(
            "sumInCentsIncVat" => $sumInCentsIncVat                          ,
            "sumInCentsExcVat" => $sumInCentsExcVat                          ,
            "vatInCents"       => $vatInCents                                ,
            "vatPercentage"    => $vatPercentage                             ,
            "currency"         => $this->payment['order']['currency'],
            "referenceId"      => $this->payment['order']['referenceId']                                     ,
        );
        if (isset($this->payment['order']['tosUrl']) === true) {
            $order['tosUrl'] = $this->payment['order']['tosUrl'];
        }
        $this->payment['order'] = $order;
        return $this->payment['order'];
    }

    private function addShipping($data)
    {
        $shipping = array(
            'title'              => $data['title'],
            'model'              => $data['model'],
            'category'           => $data['category'],
            'priceInCentsExcVat' => $data['priceInCentsExcVat'],
            'priceInCentsIncVat' => $data['priceInCentsIncVat'],
            'quantity'           => 1
        );
        $this->payment['products'][] = $shipping;
    }

    private function cacheKey($md5)
    {
        $cacheKey = date('YmdHis', time()) .'-' . $md5;
        return $cacheKey;
    }
}
