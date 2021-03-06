<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0f95cd3eeae04125aec60e476e0283d1
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FPDF' => __DIR__ . '/..' . '/setasign/fpdf/fpdf.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0f95cd3eeae04125aec60e476e0283d1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0f95cd3eeae04125aec60e476e0283d1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0f95cd3eeae04125aec60e476e0283d1::$classMap;

        }, null, ClassLoader::class);
    }
}
