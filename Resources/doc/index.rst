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

The files in this archive are released under the MIT license.  You can find a
copy of this license in the LICENSE file.

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

You can configure LosoBundle in one of the following ways in
app/config/config.yml::

    loso:
        service_scan:
            DemoBundle: ~
            MyBundle:
                base_namespace: [Prefix1, Prefix2\SubPrefix]
            arbitrary_key:
                dir:
                    - dir1
                    - dir2

Importing annotations
---------------------

There are 4 annotations available in LosoBundle::

    @Service
    @Repository
    @Controller
    @Inject

In order to use these annotations in your classes, you need to import them via
PHP's use statements.

You can use a namespace alias for the annotations' namespace::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class FooService
    {
        /** @DI\Inject */
        public function __construct($barService)
        {
        }
    }

Or you can import each annotation::

    use LoSo\LosoBundle\DependencyInjection\Annotations\Service;
    use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;

    /** @Service */
    class FooService
    {
        /** @Inject */
        public function __construct($barService)
        {
        }
    }

.. tip::

    The first way is the preferred way as it is less verbose and more explicit
    to see in your code that the annotation your are currently using belongs to
    DI.

Service definition
------------------

@Service
~~~~~~~~

The `@Service` annotation declare the class as managed by the container. You
can specify all the options you would specify through XML or YAML.

Usage::

    @Service
    @Service("service.id")
    @Service(name="service.id", public=false, tags={{name=tag1}, {name=tag2}})

    @Service(configurator="configure")
    @Service(configurator={"@bar", "configure"})
    @Service(configurator={"BazClass", "configureStatic"})

    @Service(factoryMethod="getInstance")
    @Service(factoryService="foo", factoryMethod="build") */

    @Service(scope="container")

You can combine this different options as you wish.

If you do not explicitly set the service id, this one will be determined from
the class name. All the following three classes would have *myService* as id:

* MyService a simple non namespaced class.
* Application_Service_MyService an old fashioned PEAR style namespaced class.
* \Application\Service\MyService a PHP 5.3 namespaced class.

@Inject
~~~~~~~

The `@Inject` annotation declare a service's dependency that have to be
injected by the container when the service is retrieved. You can declare
dependencies upon the constructor, properties or setter methods.

Usage::

    @Inject
    @Inject("service.id")
    @Inject("?service.id")
    @Inject("service.id=")
    @Inject("?service.id=")
    @Inject("%param.name%")
    @Inject("string containing %param.name% parameter")
    @Inject({"service1.id", "?service2.id", "service3.id=", "%param.name%", ...})

Services referenced in `@Inject` annotations follow some conventions similar to
the YamlFileLoader:

* A question mark `?` before a service id makes the reference optional.
* An equal sign `=` at the end of a service id makes the reference not strict.
* Parameters are surrounded with `%`

Let's see the `@Inject` behavior in each of his emplacement possibilities.

Constructor injection
+++++++++++++++++++++

Annotating the constructor with `@Inject` annotation will declare each
arguments of the method as a dependency whose id is the argument id. For now
you can't explicitly define the individual service id that needs to be injected
as argument.

Example::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class MyService
    {
        protected $fooService;
        protected $barService;

        /** @DI\Inject */
        public function __construct($fooService, $barService)
        {
            $this->fooService = $fooService;
            $this->barService = $barService;
        }
    }

Will declare in YAML::

    services:
        myService:
            class: MyService
            arguments: [@fooService, @barService]

Setting explicit service id::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class MyService
    {
        protected $fooService;

        /** @DI\Inject("foo.service") */
        public function __construct($fooService)
        {
            $this->fooService = $fooService;
        }
    }

Will declare in YAML::

    services:
        myService:
            class: MyService
            arguments: [@foo.service]

With multiple constructor arguments::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class MyService
    {
        protected $fooService;
        protected $barService;

        /** @DI\Inject({"foo.service", "bar.service"}) */
        public function __construct($fooService, $barService)
        {
            $this->fooService = $fooService;
            $this->barService = $barService;
        }
    }

Will declare in YAML::

    services:
        myService:
            class: MyService
            arguments: [@foo.service, @bar.service]

Setter injection
++++++++++++++++

On a setter method, the `@Inject` annotation will declare a call method on the
service with another service reference as parameter. The same way than
previously, you can explicitly specify the id of the service you want to
inject, otherwise it will be determined thanks to the method name.

Example::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class MyService
    {
        protected $fooService;
        protected $barService;

        /** @DI\Inject */
        public function setFooService($fooService)
        {
            $this->fooService = $fooService;
            return $this;
        }

        /** @DI\Inject("bar.service") */
        public function setBarService($barService)
        {
            $this->barService = $barService;
            return $this;
        }

        /** @DI\Inject */
        public function setDependencies1($fooService, $barService)
        {
            $this->fooService = $fooService;
            $this->barService = $barService;
            return $this;
        }

        /** @DI\Inject({"foo.service", "bar.service"}) */
        public function setDependencies2($fooService, $barService)
        {
            $this->fooService = $fooService;
            $this->barService = $barService;
            return $this;
        }
    }

Will declare in YAML::

    services:
        myService:
            class: MyService
            methodCalls:
                setFooService: [@fooService]
                setBarService: [@bar.service]
                setDependencies1: [@fooService, @barService]
                setDependencies2: [@foo.service, @bar.service]

Property injection
++++++++++++++++++

Finally, on a property, the `@Inject` annotation will also declare a method
call on a setter whose method name is calculated among the property name and
with the service reference you want to inject as parameter. The service
reference id can be explicitly specified, the property name will be used
otherwise.

Example::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class MyService
    {
        /** @DI\Inject */
        protected $fooService;

        /** @DI\Inject("bar.service") */
        protected $barService;

        public function setFooService($fooService)
        {
            $this->fooService = $fooService;
            return $this;
        }

        public function setBarService($barService)
        {
            $this->barService = $barService;
            return $this;
        }
    }

Will declare in YAML::

    services:
        myService:
            class: MyService
            methodCalls:
                setFooService: [@fooService]
                setBarService: [@bar.service]

Repository definition
---------------------

You can easily declare custom entity repositories in the service container
thanks to the `@Repository` annotation. You just need to specifiy on which
entity the repository will act for.

Usage::

    @Repository("FooBundle:BarEntity")
    @Repository("My\FooBundle\Entity\BarEntity")

    @Repository(name="foo.repository", entity="FooBundle:BarEntity")

    @Repository(entity="FooBundle:BarEntity", entityManager="custom")

Example::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;
    use Doctrine\ORM\EntityRepository;

    /** @DI\Repository("FooBundle:Item") */
    class ItemRepository extends EntityRepository
    {
        public function findByCategory($category)
        {
            $q = $this->createQueryBuilder('i')
                       ->where(':category MEMBER OF i.categories')
                       ->getQuery();

            return $q->execute(array('category' => $category));
        }
    }

Now you can easily inject the repository in your controller::

    use LoSo\LosoBundle\DependencyInjection\Annotations as DI;

    /** @DI\Controller */
    class ItemController
    {
        /** @DI\Inject **/
        public function __construct($itemRepository)
        {
            $this->itemRepository = $itemRepository;
        }

        // ....
    }
