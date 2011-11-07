<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidSetterInjection1
{
    /** @Inject({"foo", "bar"}) */
    public function setDependencies($fooService)
    {
    }
}
