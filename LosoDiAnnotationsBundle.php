<?php
namespace Loso\Bundle\DiAnnotationsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Compiler\RepositoryDefinitionPass;

class LosoDiAnnotationsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RepositoryDefinitionPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
