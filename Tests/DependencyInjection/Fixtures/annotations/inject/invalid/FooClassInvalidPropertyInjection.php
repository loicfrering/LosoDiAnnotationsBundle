<?php
use LoSo\LosoBundle\DependencyInjection\Annotations\Inject;
use LoSo\LosoBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidPropertyInjection
{
    /** @Inject({"foo"}) */
    private $fooService;

    public function setFooService($fooService)
    {
    }
}
