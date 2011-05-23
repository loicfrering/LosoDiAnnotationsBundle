<?php
/** @Service */
class FooClassInvalid3
{
    /** @Inject({"foo"}) */
    public function setDependencies($fooService, $barService)
    {
    }
}
