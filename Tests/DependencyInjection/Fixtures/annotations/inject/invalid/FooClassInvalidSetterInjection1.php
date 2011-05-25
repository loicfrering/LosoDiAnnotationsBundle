<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidSetterInjection1
{
    /** @Inject({"foo", "bar"}) */
    public function setDependencies($fooService)
    {
    }
}
