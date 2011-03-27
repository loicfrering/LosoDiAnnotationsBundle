<?php
/** @Service("constructor.injection") */
class FooClassConstructorInjection
{
    /** @Inject */
    public function __construct($fooService, $barService)
    {

    }
}
