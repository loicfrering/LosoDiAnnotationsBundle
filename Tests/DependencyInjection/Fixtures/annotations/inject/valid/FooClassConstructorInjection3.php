<?php
/** @Service("constructor.injection3") */
class FooClassConstructorInjection3
{
    /** @Inject */
    public function __construct($fooService, $barService)
    {

    }
}
