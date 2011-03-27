<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Symfony\Component\DependencyInjection\Reference;

final class Inject extends AbstractAnnotation
{
    public function defineFromConstructor($constructor, $definition)
    {
        $parameters = $constructor->getParameters();
        foreach($parameters as $parameter) {
            $serviceReference = new Reference($this->extractReferenceNameFromParameter($parameter));
            $definition->addArgument($serviceReference);
        }
    }

    public function defineFromProperty($property, $definition)
    {
        $propertyName = $this->filterUnderscore($property->getName());
        $serviceReference = new Reference($this->extractReferenceNameFromProperty($property));
        $definition->addMethodCall('set' . ucfirst($propertyName), array($serviceReference));
    }

    public function defineFromMethod($method, $definition)
    {
        $arguments = array();
        $parameters = $method->getParameters();
        if (null === $this->value) {
            foreach ($parameters as $parameter) {
                $arguments[] = new Reference($parameter->getName());
            }
        }
        else {
            if (!is_array($this->value)) {
                $this->value = array($this->value);
            }
            if (count($this->value) != $method->getNumberOfParameters()) {
                throw new \InvalidArgumentException(sprintf('Annotation "@Inject" when specifying services id must have one id per method argument for "%s::%s"', $method->getDeclaringClass()->getName(), $method->getName()));
            }
            foreach ($parameters as $index => $parameter) {
                $arguments[] = new Reference($this->value[$index]);
            }
        }
        $definition->addMethodCall($method->getName(), $arguments);
    }


    protected function extractReferenceNameFromProperty($property)
    {
        return $this->value ?: $this->filterUnderscore($property->getName());
    }

    protected function extractReferenceNameFromMethod($method)
    {
        return $this->value ?: $this->filterSetPrefix($method->getName());
    }

    protected function extractReferenceNameFromParameter($parameter)
    {
        return $parameter->getName();
    }
}
