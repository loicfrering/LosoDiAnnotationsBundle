<?php
namespace LoSo\LosoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use LoSo\LosoBundle\DependencyInjection\Compiler\RegisterEntityMetadataPass;

class LosoBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterEntityMetadataPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
