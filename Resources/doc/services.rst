Service definition
==================

@Service
--------

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
-------

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
~~~~~~~~~~~~~~~~~~~~~

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
~~~~~~~~~~~~~~~~

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
~~~~~~~~~~~~~~~~~~

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

