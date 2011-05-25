<?php
/** @Service("constructor.injection2") */
class FooClassConstructorInjection2
{
    /** @Inject("foo") */
    public function __construct($fooService)
    {

    }
}
