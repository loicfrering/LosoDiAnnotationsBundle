<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection1") */
class FooClassConstructorInjection1
{
    /** @Inject */
    public function __construct($fooService)
    {

    }
}
