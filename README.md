LosoDiAnnotationsBundle
=======================

What is LosoDiAnnotationsBundle?
--------------------------------

A bundle that enables Dependency Injection by annotations into your Symfony2
projects.

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

License
-------

The files in this archive are released under the MIT license. You can find a
copy of this license in the LICENSE file.

Documentation
-------------

You can read the documentation in
[Resources/doc/index.rst](https://github.com/loicfrering/LosoDiAnnotationsBundle/tree/master/Resources/doc/index.rst)

Tests
-----

LosoDiAnnotationsBundle is heavily tested! To run the tests, you'll need to set the path to
the vendors libraries in Tests/bootstrap.php and run the following command from
the project's root:

    phpunit --colors --bootstrap Tests/bootstrap.php Tests

Installation
------------

1\. Add LosoDiAnnotationsBundle to your vendor libraries:

```bash
$ cp -r <path_to>/LosoDiAnnotationsBundle vendor/bundles/Loso/Bundle/DiAnnotationsBundle
```

Or via Git:

```bash
$ git submodule add git://github.com/loicfrering/LosoDiAnnotationsBundle.git vendor/bundles/Loso/Bundle/DiAnnotationsBundle
```

2\. Register Loso namespace with the autoloader in app/autoload.php:

```php
<?php
$loader->registerNamespaces(array(
    // ...
    'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Loso'             => __DIR__.'/../vendor/bundles',
    // ...
));
```

3\. Register LosoDiAnnotationsBundle with your application's kernel in app/AppKernel.php:

```php
<?php
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
```

Configuration
-------------

You can configure LosoDiAnnotationsBundle in one of the following ways in
app/config/config.yml:

```yaml
# app/config/config.yml
loso_di_annotations:
    service_scan:
        DemoBundle: ~
        MyBundle:
            base_namespace: [Prefix1, Prefix2\SubPrefix]
        arbitrary_key:
            dir:
                - dir1
                - dir2
```
