<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidPropertyInjection
{
    /** @Inject({"foo"}) */
    private $fooService;

    public function setFooService($fooService)
    {
    }
}
