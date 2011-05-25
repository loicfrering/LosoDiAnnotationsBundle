<?php
/** @Service */
class FooClassInvalidConstructorInjection1
{
    /** @Inject({"foo", "bar"}) */
    public function __construct($fooService)
    {
    }
}
