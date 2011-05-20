<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations\DefinitionBuilder;

use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
abstract class AbstractServiceDefinitionBuilder extends AbstractAnnotationDefinitionBuilder
{
    private $annotations;

    public function build(\ReflectionClass $reflClass, $annot)
    {
        $this->annotations = array(
            'LoSo\LosoBundle\DependencyInjection\Loader\Annotations\Inject'
        );
        $definition = new Definition($reflClass->getName());

        if (null !== ($constructor = $reflClass->getConstructor())) {
            $this->processConstructor($constructor, $definition);
        }
        $this->processProperties($reflClass->getProperties(), $definition);
        $this->processMethods($reflClass->getMethods(), $definition, $reflClass);

        return array('id' => null, 'definition' => $definition);
    }

    private function processConstructor($constructor, $definition)
    {
        foreach ($this->annotations as $annotClass) {
            if ($annot = $this->reader->getMethodAnnotation($constructor, $annotClass)) {
                $annot->defineFromConstructor($constructor, $definition);
            }
        }
    }

    private function processProperties($properties, $definition)
    {
        foreach ($properties as $property) {
            foreach ($this->annotations as $annotclass) {
                if ($annot = $this->reader->getpropertyannotation($property, $annotclass)) {
                    $annot->definefromproperty($property, $definition);
                }
            }
        }
    }

    private function processMethods($methods, $definition, $reflClass)
    {
        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() == $reflClass->getName() && strpos($method->getName(), 'set') === 0) {
                foreach ($this->annotations as $annotClass) {
                    if ($annot = $this->reader->getMethodAnnotation($method, $annotClass)) {
                        $annot->defineFromMethod($method, $definition);
                    }
                }
            }
        }
    }

    protected function extractServiceName(\ReflectionClass $reflClass, $definedName = null)
    {
        $serviceName = $definedName;

        if (null === $serviceName) {
            $className = $reflClass->getName();
            if (false !== ($pos = strrpos($className, '_'))) {
                $serviceName = lcfirst(substr($className, $pos + 1));
            } else if (false !== ($pos = strrpos($className, '\\'))) {
                $serviceName = lcfirst(substr($className, $pos + 1));
            } else {
                $serviceName = lcfirst($className);
            }
        }

        return $serviceName;
    }
}
