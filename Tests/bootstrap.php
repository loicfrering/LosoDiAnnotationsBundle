<?php

require_once __DIR__.'/../../../../vendor/symfony/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => __DIR__.'/../../../../vendor/symfony',
    'Doctrine\\Common' => __DIR__.'/../../../../vendor/doctrine-common',
    'Doctrine\\DBAL' => __DIR__.'/../../../../vendor/doctrine-dbal',
    'Doctrine\\ORM' => __DIR__.'/../../../../vendor/doctrine',
    'LoSo' => __DIR__.'/../../..'
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerAutoloadNamespace('LoSo\LosoBundle\DependencyInjection\Annotations', __DIR__ . '/../../..');
