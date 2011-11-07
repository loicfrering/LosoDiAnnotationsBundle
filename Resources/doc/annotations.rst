Annotations
===========

There are 4 annotations available in LosoDiAnnotationsBundle::

* @Service for service definition.
* @Repository for repository definition.
* @Controller for controller definition.
* @Inject for dependency management.

Importing annotations
---------------------

In order to use these annotations in your classes, you need to import them via
PHP's use statements.

You can use a namespace alias for the annotations' namespace::

    use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

    /** @DI\Service */
    class FooService
    {
        /** @DI\Inject */
        public function __construct($barService)
        {
        }
    }

Or you can import each annotation::

    use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;
    use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;

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
