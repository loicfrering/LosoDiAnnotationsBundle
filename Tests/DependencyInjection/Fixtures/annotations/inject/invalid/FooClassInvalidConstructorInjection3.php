<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service */
class FooClassInvalidConstructorInjection3
{
    /** @Inject({"foo"}) */
    public function __construct($fooService, $barService)
    {
    }
}
