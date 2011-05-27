LosoBundle
==========

What is LosoBundle?
-------------------

A bundle that enables Dependency Injection by annotations into your Symfony2
projects.

Requirements
------------

LosoBundle requires PHP 5.3 or later. It has been tested with **Symfony
v2.0.0BETA3**.

You will need at least the following libraries in your vendor directory:

* Doctrine\Common
* Symfony

If you want to use the functionalities around entity repositories, you will
also need:

* Doctrine\DBAL
* Doctrine\ORM

License
-------

The files in this archive are released under the MIT license. You can find a
copy of this license in the LICENSE file.

Documentation
-------------

You can read the documentation in `Resources/doc/index.rst
<https://github.com/loicfrering/LosoBundle/tree/master/Resources/doc/index.rst>`_

Tests
-----

LosoBundle is heavily tested! To run the tests, you'll need to set the path to
the vendors libraries in Tests/bootstrap.php and run the following command from
the project's root::

    phpunit --colors --bootstrap Tests/bootstrap.php Tests

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


Configuration
-------------

You can configure LosoBundle in one of the following ways in app/config/config.yml::

    loso:
        service_scan:
            DemoBundle: ~
            MyBundle:
                base_namespace: [Prefix1, Prefix2\SubPrefix]
            arbitrary_key:
                dir:
                    - dir1
                    - dir2
