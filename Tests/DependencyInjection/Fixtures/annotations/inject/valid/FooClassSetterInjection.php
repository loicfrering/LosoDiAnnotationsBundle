<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service("setter.injection") */
class FooClassSetterInjection
{
    /** @Inject */
    public function setFooService($fooService)
    {
    }

    /** @Inject("bar") */
    public function setBarService($barService)
    {
    }

    /** @Inject */
    public function setDependencies($fooService, $barService)
    {
    }

    /** @Inject({"foo", "bar"}) */
    public function setNamedDependencies($fooService, $barService)
    {
    }
}
