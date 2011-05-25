<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidConstructorInjection3
{
    /** @Inject({"foo"}) */
    public function __construct($fooService, $barService)
    {
    }
}
