<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbf5f9051b9f18920242045eb7c09e2d6
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Payapi\\PaymentsSdk\\' => 19,
            'Payapi\\Branding\\' => 16,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Payapi\\PaymentsSdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/payapi/payapi-sdk-php/src',
        ),
        'Payapi\\Branding\\' => 
        array (
            0 => __DIR__ . '/..' . '/payapi/somepay-branding/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbf5f9051b9f18920242045eb7c09e2d6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbf5f9051b9f18920242045eb7c09e2d6::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
