<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations\DefinitionBuilder;

interface AnnotationDefinitionBuilderInterface
{
    public function build(\ReflectionClass $reflClass, $annot);
}
