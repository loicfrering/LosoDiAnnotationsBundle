<?php
/** @Service */
class FooClassInvalidPropertyInjection
{
    /** @Inject({"foo"}) */
    private $fooService;

    public function setFooService($fooService)
    {
    }
}
