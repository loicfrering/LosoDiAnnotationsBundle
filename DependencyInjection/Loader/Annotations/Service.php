<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class Service extends Annotation
{
    public $name;
    public $scope = ContainerInterface::SCOPE_CONTAINER;
    public $public;
    public $configurator;
    public $factoryMethod;
    public $factoryService;
    public $tags = array();

    public function define($reflClass, $definition)
    {
        $id = $this->extractServiceName($reflClass);

        if (isset($this->scope)) {
            $definition->setScope($this->scope);
        }

        if (isset($this->public)) {
            $definition->setPublic($this->public);
        }

        if (isset($this->factoryMethod)) {
            $definition->setFactoryMethod($this->factoryMethod);
        }

        if (isset($this->factoryService)) {
            $definition->setFactoryService($this->factoryService);
        }

        if (isset($this->configurator)) {
            if (is_string($this->configurator)) {
                $definition->setConfigurator($this->configurator);
            } else {
                $definition->setConfigurator(array($this->resolveServices($this->configurator[0]), $this->configurator[1]));
            }
        }

        if (isset($this->tags)) {
            if (!is_array($this->tags)) {
                throw new \InvalidArgumentException(sprintf('Parameter "tags" must be an array for service "%s" in %s.', $id, $reflClass->getName()));
            }
            foreach ($this->tags as $tag) {
                if (!isset($tag['name'])) {
                    throw new \InvalidArgumentException(sprintf('A "tags" entry is missing a "name" key must be an array for service "%s" in %s.', $id, $reflClass->getName()));
                }
                $name = $tag['name'];
                unset($tag['name']);

                $definition->addTag($name, $tag);
            }
        }

        return $id;
    }

    private function extractServiceName($reflClass)
    {
        $serviceName = $this->value ?: $this->name;

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

    /**
     * Resolves services.
     *
     * @param string $value
     * @return void
     */
    private function resolveServices($value)
    {
        if (is_array($value)) {
            $value = array_map(array($this, 'resolveServices'), $value);
        } else if (is_string($value) && 0 === strpos($value, '@')) {
            if (0 === strpos($value, '@?')) {
                $value = substr($value, 2);
                $invalidBehavior = ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } else {
                $value = substr($value, 1);
                $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            }

            if ('=' === substr($value, -1)) {
                $value = substr($value, 0, -1);
                $strict = false;
            } else {
                $strict = true;
            }

            $value = new Reference($value, $invalidBehavior, $strict);
        }

        return $value;
    }
}
