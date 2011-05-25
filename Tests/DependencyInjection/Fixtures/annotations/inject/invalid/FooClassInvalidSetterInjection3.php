<?php
/** @Service */
class FooClassInvalidSetterInjection3
{
    /** @Inject({"foo"}) */
    public function setDependencies($fooService, $barService)
    {
    }
}
