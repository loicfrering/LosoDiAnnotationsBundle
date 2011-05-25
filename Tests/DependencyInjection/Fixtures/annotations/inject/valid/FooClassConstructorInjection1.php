<?php
/** @Service("constructor.injection1") */
class FooClassConstructorInjection1
{
    /** @Inject */
    public function __construct($fooService)
    {

    }
}
