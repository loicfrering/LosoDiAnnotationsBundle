Installation
============

Requirements
------------

LosoDiAnnotationsBundle requires PHP 5.3 or later. It has been tested with
**Symfony v2.0.4**.

You will need at least the following libraries in your vendor directory:

* Doctrine\Common
* Symfony

If you want to use the functionalities around entity repositories, you will
also need:

* Doctrine\DBAL
* Doctrine\ORM

Installation
------------

1. Add LosoDiAnnotationsBundle to your vendor libraries::

    $ cp -r <path_to>/LosoDiAnnotationsBundle vendor/bundles/Loso/Bundle/DiAnnotationsBundle

   Or via Git::

    $ git submodule add git://github.com/loicfrering/LosoDiAnnotationsBundle.git vendor/bundles/Loso/Bundle/DiAnnotationsBundle

2. Register Loso namespace with the autoloader in app/autoload.php::

    $loader->registerNamespaces(array(
        // ...
        'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
        'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
        'Loso'             => __DIR__.'/../vendor/bundles',
        // ...
    ));

3. Register LosoDiAnnotationsBundle with your application's kernel in app/AppKernel.php::

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Loso\Bundle\DiAnnotationsBundle\LosoDiAnnotationsBundle(),
            // ...
        );

        // ...

        return $bundles;
    }
