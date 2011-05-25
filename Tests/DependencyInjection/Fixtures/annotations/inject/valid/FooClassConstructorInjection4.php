<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection4") */
class FooClassConstructorInjection4
{
    /** @Inject({"foo", "bar"}) */
    public function __construct($fooService, $barService)
    {

    }
}
