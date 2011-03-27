<?php

require_once __DIR__.'/../../../../vendor/symfony/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => __DIR__.'/../../../../vendor/symfony',
    'Doctrine\\Common' => __DIR__.'/../../../../vendor/doctrine-common',
    'LoSo' => __DIR__.'/../../..'
));
$loader->register();
