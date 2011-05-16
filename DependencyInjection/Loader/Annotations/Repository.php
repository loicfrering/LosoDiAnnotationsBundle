<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class Repository extends Annotation
{
    public $entity;
    public $entityManager;
    public $name;

    public function define($reflClass, $definition)
    {
        $entity = $this->value ?: $this->entity;
        $entityManagerName = !empty($this->entityManager) ? $this->entityManager : 'default';
        $entityManager = 'doctrine.orm.entity_manager';
        if ($entityManagerName != 'default') {
            $entityManager = sprintf('doctrine.orm.%s_entity_manager', $this->entityManager);
        }

        if (null === $entity) {
            throw new \InvalidArgumentException(sprintf('Entity name must be setted in @Repository for class "%s".', $reflClass->getName()));
        }

        $id = $this->extractServiceName($reflClass);
        $definition->setArguments(array(new Reference($entityManager), new Reference('loso.doctrine.metadata.' . $entityManagerName . '.' . $entity)));
        $definition->addTag('loso.doctrine.repository', array(
            'entity' => $entity,
            'entityManager' => $entityManagerName
        ));

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
