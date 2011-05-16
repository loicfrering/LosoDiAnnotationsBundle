<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class Repository extends Annotation
{
    public $entity;
    public $name;

    public function define($reflClass, $definition)
    {
        $entity = $this->value ?: $this->entity;

        $id = $this->extractServiceName($reflClass);
        $definition->setArguments(array(new Reference('doctrine.orm.entity_manager'), new Reference('loso.doctrine.metadata.' . $entity)));
        $definition->addTag('loso.doctrine.repository', array('entity' => $entity));

        return $id;
    }

    private function extractServiceName($reflClass)
    {
        $serviceName = $this->name;

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
