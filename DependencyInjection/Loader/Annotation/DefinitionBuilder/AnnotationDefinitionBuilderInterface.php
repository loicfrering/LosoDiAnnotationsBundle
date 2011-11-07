<?php

namespace Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder;

interface AnnotationDefinitionBuilderInterface
{
    public function build(\ReflectionClass $reflClass, $annot);
}
