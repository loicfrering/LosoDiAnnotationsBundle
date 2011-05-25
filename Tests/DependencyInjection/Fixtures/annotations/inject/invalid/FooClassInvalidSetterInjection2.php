<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidSetterInjection2
{
    /** @Inject("foo") */
    public function setDependencies($fooService, $barService)
    {
    }
}
