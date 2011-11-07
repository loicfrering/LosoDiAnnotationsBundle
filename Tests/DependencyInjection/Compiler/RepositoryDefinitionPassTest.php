<?php
namespace Loso\Bundle\DiAnnotationsBundle\Tests\DepedencyInjection\Compiler;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Compiler\RepositoryDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class RepositoryDefinitionPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new ContainerBuilder();

        $definition = new Definition('FooRepository');
        $definition->addTag('loso.doctrine.repository', array(
            'entity' => 'FooEntity',
            'entityManager' => 'default'
        ));
        $this->container->setDefinition('foo.repository', $definition);

        $definition = new Definition('BarRepository');
        $definition->addTag('loso.doctrine.repository', array(
            'entity' => 'LosoDiAnnotationsBundle:BarEntity',
            'entityManager' => 'test'
        ));
        $this->container->setDefinition('bar.repository', $definition);

        $definition = new Definition('BazRepository');
        $definition->addTag('loso.doctrine.repository', array(
            'entity' => 'Loso\Bundle\DiAnnotationsBundle\Entity\BazEntity',
            'entityManager' => 'test'
        ));
        $this->container->setDefinition('baz.repository', $definition);

        $this->container->setDefinition('doctrine.orm.entity_manager', new Definition('EntityManager'));
        $this->container->setDefinition('doctrine.orm.test_entity_manager', new Definition('EntityManager'));

        $this->container->addCompilerPass(new RepositoryDefinitionPass());
    }

    public function testRepositoryConstructorArguments()
    {
        $definition = $this->container->getDefinition('foo.repository');
        $this->assertEmpty($definition->getArguments());

        $this->container->compile();

        $arguments = $definition->getArguments();
        $this->assertEquals(2, count($arguments));
        $this->assertEquals(new Reference('doctrine.orm.entity_manager'), $arguments[0]);
        $this->assertEquals(new Reference('loso.doctrine.metadata.default.FooEntity'), $arguments[1]);
    }

    public function testEntityMetadataDefinition()
    {
        $this->assertFalse($this->container->hasDefinition('loso.doctrine.metadata.default.FooEntity'));

        $this->container->compile();

        $this->assertTrue($this->container->hasDefinition('loso.doctrine.metadata.default.FooEntity'));
        $definition = $this->container->getDefinition('loso.doctrine.metadata.default.FooEntity');
        $this->assertEquals('Doctrine\ORM\Mapping\ClassMetadata', $definition->getClass());
        $this->assertEquals('doctrine.orm.entity_manager', $definition->getFactoryService());
        $this->assertEquals('getClassMetadata', $definition->getFactoryMethod());
        $this->assertEquals(array('FooEntity'), $definition->getArguments());
    }

    public function testParticularEntityManager()
    {
        $this->container->compile();

        $definition = $this->container->getDefinition('bar.repository');
        $arguments = $definition->getArguments();
        $this->assertEquals(new Reference('doctrine.orm.test_entity_manager'), $arguments[0]);

        $this->assertTrue($this->container->hasDefinition('loso.doctrine.metadata.test.LosoDiAnnotationsBundle.BarEntity'));
        $definition = $this->container->getDefinition('loso.doctrine.metadata.test.LosoDiAnnotationsBundle.BarEntity');
        $this->assertEquals('doctrine.orm.test_entity_manager', $definition->getFactoryService());
    }

    public function testEntityWithAlias()
    {
        $this->container->compile();

        $definition = $this->container->getDefinition('bar.repository');
        $arguments = $definition->getArguments();
        $this->assertEquals(new Reference('loso.doctrine.metadata.test.LosoDiAnnotationsBundle.BarEntity'), $arguments[1]);

        $this->assertTrue($this->container->hasDefinition('loso.doctrine.metadata.test.LosoDiAnnotationsBundle.BarEntity'));
        $definition = $this->container->getDefinition('loso.doctrine.metadata.test.LosoDiAnnotationsBundle.BarEntity');
        $this->assertEquals(array('LosoDiAnnotationsBundle:BarEntity'), $definition->getArguments());
    }

    public function testEntityWithNamespace()
    {
        $this->container->compile();

        $definition = $this->container->getDefinition('baz.repository');
        $arguments = $definition->getArguments();
        $this->assertEquals(new Reference('loso.doctrine.metadata.test.Loso.Bundle.DiAnnotationsBundle.Entity.BazEntity'), $arguments[1]);

        $this->assertTrue($this->container->hasDefinition('loso.doctrine.metadata.test.Loso.Bundle.DiAnnotationsBundle.Entity.BazEntity'));
        $definition = $this->container->getDefinition('loso.doctrine.metadata.test.Loso.Bundle.DiAnnotationsBundle.Entity.BazEntity');
        $this->assertEquals(array('Loso\Bundle\DiAnnotationsBundle\Entity\BazEntity'), $definition->getArguments());
    }
}
