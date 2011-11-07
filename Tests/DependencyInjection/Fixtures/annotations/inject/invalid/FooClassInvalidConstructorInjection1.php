<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidConstructorInjection1
{
    /** @Inject({"foo", "bar"}) */
    public function __construct($fooService)
    {
    }
}
