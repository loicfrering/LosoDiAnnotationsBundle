<?php
/** @Service */
class FooClassInvalid2
{
    /** @Inject("foo") */
    public function setDependencies($fooService, $barService)
    {
    }
}
