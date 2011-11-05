Installation
============

Requirements
------------

LosoBundle requires PHP 5.3 or later. It has been tested with **Symfony
v2.0.0 final release**.

You will need at least the following libraries in your vendor directory:

* Doctrine\Common
* Symfony

If you want to use the functionalities around entity repositories, you will
also need:

* Doctrine\DBAL
* Doctrine\ORM

Installation
------------

1. Add LosoBundle to your vendor libraries::

    $ cp LosoBundle vendor/bundles/LoSo/

   Or via Git::

    $ git submodule add git://github.com/loicfrering/LosoBundle.git vendor/bundles/LoSo/LosoBundle

2. Register LoSo namespace with the autoloader in app/autoload.php::

    $loader->registerNamespaces(array(
        // ...
        'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
        'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
        'LoSo'             => __DIR__.'/../vendor/bundles',
        // ...
    ));

3. Register LosoBundle with your application's kernel in app/AppKernel.php::

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new LoSo\LosoBundle\LosoBundle(),
            // ...
        );

        // ...

        return $bundles;
    }
