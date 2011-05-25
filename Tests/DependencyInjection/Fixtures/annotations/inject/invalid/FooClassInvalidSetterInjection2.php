<?php
/** @Service */
class FooClassInvalidSetterInjection2
{
    /** @Inject("foo") */
    public function setDependencies($fooService, $barService)
    {
    }
}
