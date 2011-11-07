<?php
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Inject;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service;

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

    /** @Inject("?baz=") */
    private $bazService;

    public function setBazService($bazService)
    {
    }

    /** @Inject("%param%") */
    private $param;

    public function setParam($param)
    {
    }
}
