<?php

namespace payapi;

final class adaptor
{
    public static $single = false;

    private $plugin       = false;
    private $adapt        = false;
    private $error        = false;
    private $route        = false;
    private $config       = false;
    private $debug        = false;
    private $adaptors     = array(
        "native",
        "opencart2",
        "opencart22",
        "opencart23",
        "opencart3",
        "prestashop",
        "magento"
    );

    protected $log        = false;

    private function __construct($adapt, $plugin)
    {
        if (self::$single !== false) {
            return self::$single;
        }
        $this->error = error::single();
        $this->route = router::single();
        //->
        if (in_array($plugin, $this->adaptors) !== false) {
            $this->plugin = $plugin;
        } else {
            $this->error('[adaptor] not available', 'warning');
            $this->plugin = 'native';
        }
        $pluginRoute = $this->route->plugin($this->plugin);
        if (is_string($pluginRoute) === true) {
            require($pluginRoute);
            $this->adapt = plugin::single($adapt);
            $config = $this->config();
            $this->config = config::single($config);
            $this->debug = debug::single($this->debug());
            foreach ($config as $key => $setting) {
                //-> @FIXME TODELETE after update
                if($key != 'staging') {
                    $enabled =($setting != false) ? 'true' : 'false';
                    $this->debug->add('[' . $key . '] ' . $enabled);
                }
            }
        } else {
            $this->debug->add('[plugin] 404', 'error');
        }
    }

    private function validated()
    {
        return $this->adapt->validated();
    }

    public function product($product)
    {
        return $this->adapt->product($product);
    }

    public function payment($payment, $partialPaymentMethod = null)
    {
        return $this->adapt->payment($payment, $partialPaymentMethod);
    }

    public function instantPayment($payment)
    {
        return $this->adapt->instantPayment($payment);
    }

    public function log($info)
    {
        return $this->adapt->log($info);
    }

    public function session()
    {
        return $this->adapt->session();
    }

    public function db()
    {
        return $this->adapt->db();
    }

    public function customer($email)
    {
        return $this->adapt->customer($email);
    }

    public function debug()
    {
        return $this->adapt->debug();
    }

    public function demo()
    {
        return $this->adapt->demo();
    }

    public function staging()
    {
        return $this->adapt->staging();
    }

    public function version()
    {
        return $this->adapt->version();
    }

    private function config()
    {
        $config = array(
            "debug" => $this->debug(),
            "staging" => $this->staging(),
            "demo" => $this->demo()
        );
        return $config;
    }

    public function localized($localized)
    {
        return $this->adapt->localized($localized);
    }

    public static function single($adapt, $plugin)
    {
        if (self::$single === false) {
            self::$single = new self($adapt, $plugin);
        }
        return self::$single;
    }

    public function __toString()
    {
        return(string) $this->adapt;
    }
}
