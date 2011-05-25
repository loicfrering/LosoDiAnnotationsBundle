LosoBundle
==========

What is LosoBundle?
-------------------

A bundle that enables Dependency Injection by annotations into your Symfony2
projects.

Requirements
------------

LosoBundle requires PHP 5.3 or later.
It has been tested with Symfony 2.0 Preview Release 8.

You will need the following libraries in your vendor directory:

* Doctrine\Common
* Symfony

License
-------

The files in this archive are released under the MIT license.
You can find a copy of this license in the LICENSE file.

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

Service definition
------------------

@Service
~~~~~~~~

The @Service annotation declare the class as managed by the container. You can
specify all the options you would specify through XML or YAML.

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

The @Inject annotation declare a service's dependency that have to be injected
by the container when the service is retrieved. You can declare dependencies
upon the constructor, properties or setter methods.

Usage::

    @Inject
    @Inject("service.id")
    @Inject({"service1.id", "service2.id", ...})

Let's see the @Inject behavior in each of his emplacement possibilities.

Constructor injection
+++++++++++++++++++++

Annotating the constructor with @Inject annotation will declare each arguments
of the method as a dependency whose id is the argument id. For now you can't
explicitly define the individual service id that needs to be injected as
argument.

Example::

    /** @Service */
    class MyService
    {
        protected $fooService;
        protected $barService;

        /** @Inject */
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

    /** @Service */
    class MyService
    {
        protected $fooService;

        /** @Inject("foo.service") */
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

    /** @Service */
    class MyService
    {
        protected $fooService;
        protected $barService;

        /** @Inject({"foo.service", "bar.service"}) */
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

On a setter method, the @Inject annotation will declare a call method on the
service with another service reference as parameter. The same way than
previously, you can explicitly specify the id of the service you want to
inject, otherwise it will be determined thanks to the method name.

Example::

    /** @Service */
    class MyService
    {
        protected $fooService;
        protected $barService;

        /** @Inject */
        public function setFooService($fooService)
        {
            $this->fooService = $fooService;
            return $this;
        }

        /** @Inject("bar.service") */
        public function setBarService($barService)
        {
            $this->barService = $barService;
            return $this;
        }

        /** @Inject */
        public function setDependencies1($fooService, $barService)
        {
            $this->fooService = $fooService;
            $this->barService = $barService;
            return $this;
        }

        /** @Inject({"foo.service", "bar.service"}) */
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

Finally, on a property, the @Inject annotation will also declare a method call
on a setter whose method name is calculated among the property name and with
the service reference you want to inject as parameter. The service reference id
can be explicitly specified, the property name will be used otherwise.

Example::

    /** @Service */
    class MyService
    {
        /** @Inject */
        protected $fooService;

        /** @Inject("bar.service") */
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
thanks to the @Repository annotation. You just need to specifiy on which entity
the repository will act for.

Usage::

    @Repository("FooBundle:BarEntity")
    @Repository("My\FooBundle\Entity\BarEntity")

    @Repository(name="foo.repository", entity="FooBundle:BarEntity")

    @Repository(entity="FooBundle:BarEntity", entityManager="custom")

Example::

    use Doctrine\ORM\EntityRepository;

    /** @Repository("FooBundle:Item") */
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

    /** @Controller */
    class ItemController
    {
        /** @Inject **/
        public function __construct($itemRepository)
        {
            $this->itemRepository = $itemRepository;
        }

        // ....
    }