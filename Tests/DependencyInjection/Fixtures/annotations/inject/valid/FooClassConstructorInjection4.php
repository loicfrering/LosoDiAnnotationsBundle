<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

/** @Service("constructor.injection4") */
class FooClassConstructorInjection4
{
    /** @Inject({"?foo", "bar=", "?baz=", "%param%"}) */
    public function __construct($fooService, $barService, $bazService, $param)
    {

    }
}
