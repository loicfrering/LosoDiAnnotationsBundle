<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidSetterInjection2
{
    /** @Inject("foo") */
    public function setDependencies($fooService, $barService)
    {
    }
}
