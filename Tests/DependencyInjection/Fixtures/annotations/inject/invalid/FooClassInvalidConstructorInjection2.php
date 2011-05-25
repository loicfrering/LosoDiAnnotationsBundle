<?php
/** @Service */
class FooClassInvalidConstructorInjection2
{
    /** @Inject("foo") */
    public function __construct($fooService, $barService)
    {
    }
}
