<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection2") */
class FooClassConstructorInjection2
{
    /** @Inject("foo") */
    public function __construct($fooService)
    {

    }
}
