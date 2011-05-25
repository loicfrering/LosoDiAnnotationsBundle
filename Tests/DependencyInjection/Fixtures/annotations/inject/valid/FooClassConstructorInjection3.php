<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection3") */
class FooClassConstructorInjection3
{
    /** @Inject */
    public function __construct($fooService, $barService)
    {

    }
}
