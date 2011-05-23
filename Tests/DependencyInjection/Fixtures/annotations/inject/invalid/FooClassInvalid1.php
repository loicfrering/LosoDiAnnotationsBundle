<?php
/** @Service */
class FooClassInvalid1
{
    /** @Inject({"foo", "bar"}) */
    public function setDependencies($fooService)
    {
    }
}
