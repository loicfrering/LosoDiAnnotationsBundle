<?php
/** @Service */
class FooClassInvalidSetterInjection1
{
    /** @Inject({"foo", "bar"}) */
    public function setDependencies($fooService)
    {
    }
}
