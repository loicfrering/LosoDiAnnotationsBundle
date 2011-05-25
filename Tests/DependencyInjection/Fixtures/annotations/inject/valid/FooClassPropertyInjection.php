<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service("property.injection") */
class FooClassPropertyInjection
{
    /** @Inject */
    private $fooService;

    public function setFooService($fooService)
    {
    }

    /** @Inject("bar") */
    private $barService;

    public function setBarService($barService)
    {
    }
}
