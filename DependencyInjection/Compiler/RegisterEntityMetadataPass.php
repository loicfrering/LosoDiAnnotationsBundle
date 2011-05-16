<?php

namespace LoSo\LosoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterEntityMetadataPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('loso.doctrine.repository') as $repositoryId => $tag) {
            $definition = new Definition('Doctrine\ORM\Mapping\ClassMetadata');
            $entity = $tag[0]['entity'];
            $entityManagerName = $tag[0]['entityManager'];
            $entityManager = 'doctrine.orm.entity_manager';
            if ($entityManagerName != 'default') {
                $entityManager = sprintf('doctrine.orm.%s_entity_manager', $this->entityManager);
            }

            $id = 'loso.doctrine.metadata.' . $entityManagerName . '.' . $entity;

            $definition->setFactoryService($entityManager);
            $definition->setFactoryMethod('getClassMetadata');
            $definition->setArguments(array($entity));

            $container->setDefinition($id, $definition);
        }
    }
}
