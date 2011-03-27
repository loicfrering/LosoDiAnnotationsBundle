<?php
/** @Service("property.injection") */
class FooClassPropertyInjection
{
    /** @Inject*/
    private $fooService;

    public function setFooService($fooService)
    {
    }

    /** @Inject("bar") */
    private $barService;

    public function setBarService($barService)
    {
    }
}
