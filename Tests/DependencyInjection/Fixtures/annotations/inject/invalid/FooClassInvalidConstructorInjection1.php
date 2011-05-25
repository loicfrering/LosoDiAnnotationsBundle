<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidConstructorInjection1
{
    /** @Inject({"foo", "bar"}) */
    public function __construct($fooService)
    {
    }
}
