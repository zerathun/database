<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita3c1c3c070ad740fc5bcc3bad0da2af7
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Funclib\\' => 8,
        ),
        'D' => 
        array (
            'Database\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Funclib\\' => 
        array (
            0 => __DIR__ . '/..' . '/zerathun/funclib/src',
        ),
        'Database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Database',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita3c1c3c070ad740fc5bcc3bad0da2af7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita3c1c3c070ad740fc5bcc3bad0da2af7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
