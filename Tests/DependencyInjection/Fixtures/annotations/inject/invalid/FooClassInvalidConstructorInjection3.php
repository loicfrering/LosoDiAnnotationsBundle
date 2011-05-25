<?php
/** @Service */
class FooClassInvalidConstructorInjection3
{
    /** @Inject({"foo"}) */
    public function __construct($fooService, $barService)
    {
    }
}
