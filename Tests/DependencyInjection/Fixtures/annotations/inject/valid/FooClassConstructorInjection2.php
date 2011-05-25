<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection2") */
class FooClassConstructorInjection2
{
    /** @Inject("foo") */
    public function __construct($fooService)
    {

    }
}
