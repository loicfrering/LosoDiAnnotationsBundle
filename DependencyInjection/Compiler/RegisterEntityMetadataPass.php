<?php

namespace LoSo\LosoBundle\DependencyInjection\Compiler;

use LoSo\LosoBundle\DependencyInjection\Loader\DoctrineServicesUtils;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * CompilerPass which registers Doctrine entity metadatas necessary
 * for repositories into the container.
 *
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class RegisterEntityMetadataPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $doctrineServicesUtils = new DoctrineServicesUtils();

        foreach ($container->findTaggedServiceIds('loso.doctrine.repository') as $repositoryId => $tag) {
            $entity = $tag[0]['entity'];
            $entityManager = $tag[0]['entityManager'];

            $definition = $doctrineServicesUtils->getEntityMetadataDefinition($entity, $entityManager);
            $id = $doctrineServicesUtils->resolveEntityMetadataId($entity, $entityManager);
            $container->setDefinition($id, $definition);
        }
    }
}
