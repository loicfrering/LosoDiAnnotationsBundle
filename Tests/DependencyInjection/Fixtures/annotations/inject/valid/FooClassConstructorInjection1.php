<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection1") */
class FooClassConstructorInjection1
{
    /** @Inject */
    public function __construct($fooService)
    {

    }
}
