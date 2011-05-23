<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
abstract class AbstractServiceDefinitionBuilder extends AbstractAnnotationDefinitionBuilder
{
    private static $injectAnnot = 'LoSo\LosoBundle\DependencyInjection\Annotations\Inject';

    public function build(\ReflectionClass $reflClass, $annot)
    {
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
        if ($annot = $this->reader->getMethodAnnotation($constructor, self::$injectAnnot)) {
            $parameters = $constructor->getParameters();
            foreach($parameters as $parameter) {
                $serviceReference = new Reference($annot->value ?: $this->extractReferenceNameFromParameter($parameter));
                $definition->addArgument($serviceReference);
            }
        }
    }

    private function processProperties($properties, $definition)
    {
        foreach ($properties as $property) {
            if ($annot = $this->reader->getpropertyannotation($property, self::$injectAnnot)) {
                $propertyName = $this->filterUnderscore($property->getName());
                $serviceReference = new Reference($annot->value ?: $this->extractReferenceNameFromProperty($property));
                $definition->addMethodCall('set' . ucfirst($propertyName), array($serviceReference));
            }
        }
    }

    private function processMethods($methods, $definition, $reflClass)
    {
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'set') === 0) {
                if ($annot = $this->reader->getMethodAnnotation($method, self::$injectAnnot)) {
                    $arguments = array();
                    $parameters = $method->getParameters();
                    if (null === $annot->value) {
                        foreach ($parameters as $parameter) {
                            $arguments[] = new Reference($parameter->getName());
                        }
                    }
                    else {
                        if (!is_array($annot->value)) {
                            $annot->value = array($annot->value);
                        }
                        if (count($annot->value) != $method->getNumberOfParameters()) {
                            throw new \InvalidArgumentException(sprintf('Annotation "@Inject" when specifying services id must have one id per method argument for "%s::%s"', $method->getDeclaringClass()->getName(), $method->getName()));
                        }
                        foreach ($parameters as $index => $parameter) {
                            $arguments[] = new Reference($annot->value[$index]);
                        }
                    }
                    $definition->addMethodCall($method->getName(), $arguments);
                }
            }
        }
    }

    protected function extractReferenceNameFromProperty($property)
    {
        return $this->filterUnderscore($property->getName());
    }

    protected function extractReferenceNameFromMethod($method)
    {
        return $this->filterSetPrefix($method->getName());
    }

    protected function extractReferenceNameFromParameter($parameter)
    {
        return $parameter->getName();
    }

    protected function filterUnderscore($value)
    {
        if(strpos($value, '_') === 0) {
            return substr($value, 1);
        }
        return $value;
    }

    protected function filterSetPrefix($value)
    {
        if(strpos($value, 'set') === 0) {
            return lcfirst(substr($value, 3));
        }
        return lcfirst($value);
    }
}
