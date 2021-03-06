<?php

require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => __DIR__.'/../vendor/symfony/src',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL' => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine\\ORM' => __DIR__.'/../vendor/doctrine/lib',
));
$loader->registerNamespaceFallbacks(array(
    __DIR__ . '/..'
));
$loader->register();

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Loso\\Bundle\\DiAnnotationsBundle\\')) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 3)).'.php';
        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;
        return true;
    }
});
AnnotationRegistry::registerLoader(function($class) {
    if (0 === strpos($class, 'Loso\\Bundle\\DiAnnotationsBundle\\')) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 3)).'.php';
        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;
        return true;
    }
});
