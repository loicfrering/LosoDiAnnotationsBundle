<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;
use LoSo\LosoBundle\DependencyInjection\Loader\DoctrineServicesUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class Repository extends Annotation
{
    public $entity;
    public $entityManager;
    public $name;

    public function define($reflClass, $definition)
    {
        $doctrineServicesUtils = new DoctrineServicesUtils();

        $entity = $this->value ?: $this->entity;
        $entityManager = !empty($this->entityManager) ? $this->entityManager : 'default';

        if (null === $entity) {
            throw new \InvalidArgumentException(sprintf('Entity name must be setted in @Repository for class "%s".', $reflClass->getName()));
        }

        $id = $this->extractServiceName($reflClass);
        $entityManagerRef = $doctrineServicesUtils->getEntityManagerReference($entityManager);
        $entityMetadataRef = $doctrineServicesUtils->getEntityMetadataReference($entity, $entityManager);
        $definition->setArguments(array($entityManagerRef, $entityMetadataRef));
        $definition->addTag('loso.doctrine.repository', array(
            'entity' => $entity,
            'entityManager' => $entityManager
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
