<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection3") */
class FooClassConstructorInjection3
{
    /** @Inject */
    public function __construct($fooService, $barService)
    {

    }
}
