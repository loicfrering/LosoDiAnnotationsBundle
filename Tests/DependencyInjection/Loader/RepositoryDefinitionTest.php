<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use LoSo\LosoBundle\DependencyInjection\Loader\AnnotationLoader;
use LoSo\LosoBundle\DependencyInjection\Compiler\RepositoryDefinitionPass;

class RepositoryDefinitionTest extends \PHPUnit_Framework_TestCase
{
    static private $fixturesPath;
    static private $services;

    static public function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/../Fixtures/');
    }

    static private function loadServices($path)
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new RepositoryDefinitionPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
        $loader = new AnnotationLoader($container);
        $loader->useDefaultAnnotationNamespace(true);
        $loader->load(self::$fixturesPath . '/annotations/' . $path);
        $container->setDefinition('doctrine.orm.entity_manager', new Definition('Doctrine\ORM\EntityManager'));
        $container->setDefinition('doctrine.orm.test_entity_manager', new Definition('Doctrine\ORM\EntityManager'));
        $container->compile();
        return $container->getDefinitions();
    }

    public function testRepositoryDefinition()
    {
        $services = self::loadServices('repository/valid');

        $this->assertTrue(isset($services[strtolower('loso.doctrine.metadata.default.FooEntity')]), 'CompilerPass registers metadata service for repository\'s entity');
        $metadataDefinition = $services[strtolower('loso.doctrine.metadata.default.FooEntity')];
        $this->assertEquals('doctrine.orm.entity_manager', $metadataDefinition->getFactoryService());
        $this->assertEquals('getClassMetadata', $metadataDefinition->getFactoryMethod());
        $this->assertEquals(array('FooEntity'), $metadataDefinition->getArguments());

        $this->assertTrue(isset($services[strtolower('fooRepository')]), '->load() parses repository classes');
        $repositoryDefinition = $services[strtolower('fooRepository')];
        $this->assertEquals('FooRepository', $repositoryDefinition->getClass(), '->load() parses the class attribute');
        $this->assertEquals(array(new Reference('doctrine.orm.entity_manager'), new Reference('loso.doctrine.metadata.default.FooEntity')), $repositoryDefinition->getArguments());
    }

    public function testParticularEntityManager()
    {
        $services = self::loadServices('repository/valid');

        $this->assertTrue(isset($services[strtolower('fooRepositoryWithParticularEntityManager')]), '->load() parses repository classes');
        $repositoryDefinition = $services[strtolower('fooRepositoryWithParticularEntityManager')];
        $this->assertEquals('FooRepositoryWithParticularEntityManager', $repositoryDefinition->getClass(), '->load() parses the class attribute');
        $this->assertEquals(array(new Reference('doctrine.orm.test_entity_manager'), new Reference('loso.doctrine.metadata.test.FooEntity')), $repositoryDefinition->getArguments());
    }

    public function testInvalidArgumentExceptionThrownForInvalidRepository()
    {
        $container = new ContainerBuilder();
        $loader = new AnnotationLoader($container);
        $loader->useDefaultAnnotationNamespace(true);

        try {
            $loader->load(self::$fixturesPath . '/annotations/repository/invalid');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Entity name must be setted in @Repository for class "InvalidFooRepository".', $e->getMessage());
        }
    }
}
