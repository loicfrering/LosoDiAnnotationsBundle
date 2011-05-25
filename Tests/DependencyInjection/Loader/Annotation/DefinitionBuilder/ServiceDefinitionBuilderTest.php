<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader\Annotation\DefinitionBuilder;

use Doctrine\Common\Annotations\AnnotationReader;
use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class ServiceDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $fixturesPath;
    private $reader;
    private $builder;

    public function setUp()
    {
        $this->fixturesPath = realpath(__DIR__ . '/../../../Fixtures/');
        $reader = new AnnotationReader();
        $this->builder = new ServiceDefinitionBuilder($reader);
        $this->reader = $reader;
    }

    private function buildDefinition($class, $path)
    {
        if (!class_exists($class, false)) {
            require $this->fixturesPath . '/' . $path . $class . '.php';
        }
        $definitionHolder = $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Service'));
        return $definitionHolder['definition'];
    }

    public function testServiceIdResolution()
    {
        $class = 'FooClass';
        if (!class_exists($class, false)) {
            require $this->fixturesPath . '/annotations/service/' . $class . '.php';
        }
        $definitionHolder = $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Service'));
        $this->assertEquals('foo', $definitionHolder['id']);

        $class = 'BarClass';
        require $this->fixturesPath . '/annotations/service/' . $class . '.php';
        $definitionHolder = $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Service'));
        $this->assertEquals('barClass', $definitionHolder['id']);

        $class = 'Old_Namespace_BarClassOldNamespace';
        require $this->fixturesPath . '/annotations/service/BarClassOldNamespace.php';
        $definitionHolder = $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Service'));
        $this->assertEquals('barClassOldNamespace', $definitionHolder['id']);

        $class = '\My\Foo\Bar\BarClassNamespace';
        require $this->fixturesPath . '/annotations/service/BarClassNamespace.php';
        $definitionHolder = $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Service'));
        $this->assertEquals('barClassNamespace', $definitionHolder['id']);
    }

    public function testDefinition()
    {
        $definition = $this->buildDefinition('FooClass', 'annotations/service/');

        $this->assertNotNull($definition);
        $this->assertEquals('FooClass', $definition->getClass());
    }

    public function testScope()
    {
        $definition = $this->buildDefinition('FooClassScopeContainer', 'annotations/service/');
        $this->assertEquals('container', $definition->getScope());

        $definition = $this->buildDefinition('FooClassScopeCustom', 'annotations/service/');
        $this->assertEquals('custom', $definition->getScope());

        $definition = $this->buildDefinition('FooClassScopePrototype', 'annotations/service/');
        $this->assertEquals('prototype', $definition->getScope());
    }

    public function testConfigurator()
    {
        $definition = $this->buildDefinition('FooClassConfigurator1', 'annotations/service/');
        $this->assertEquals('sc_configure', $definition->getConfigurator());

        $definition = $this->buildDefinition('FooClassConfigurator2', 'annotations/service/');
        $this->assertEquals(array(new Reference('baz'), 'configure'), $definition->getConfigurator());

        $definition = $this->buildDefinition('FooClassConfigurator3', 'annotations/service/');
        $this->assertEquals(array('BazClass', 'configureStatic'), $definition->getConfigurator());
    }

    public function testFactory()
    {
        $definition = $this->buildDefinition('FooClassConstructor', 'annotations/service/');
        $this->assertEquals('getInstance', $definition->getFactoryMethod());

        $definition = $this->buildDefinition('FooClassFactoryService', 'annotations/service/');
        $this->assertEquals('foo', $definition->getFactoryService());
    }

    /*public function testAliases()
    {
        $this->fail('Not implemented yet.');
        $aliases = $container->getAliases();
        $this->assertTrue(isset($aliases['alias_for_foo']), '->load() parses aliases');
        $this->assertEquals('foo', (string) $aliases['alias_for_foo'], '->load() parses aliases');
        $this->assertTrue($aliases['alias_for_foo']->isPublic());
        $this->assertTrue(isset($aliases['another_alias_for_foo']));
        $this->assertEquals('foo', (string) $aliases['another_alias_for_foo']);
        $this->assertFalse($aliases['another_alias_for_foo']->isPublic());
    }

    public function testNonArrayTagThrowsException()
    {
        $loader = new YamlFileLoader(new ContainerBuilder(), new FileLocator(self::$fixturesPath.'/yaml'));
        try {
            $loader->load('badtag1.yml');
            $this->fail('->load() should throw an exception when the tags key of a service is not an array');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->load() throws an InvalidArgumentException if the tags key is not an array');
            $this->assertStringStartsWith('Parameter "tags" must be an array for service', $e->getMessage(), '->load() throws an InvalidArgumentException if the tags key is not an array');
        }
    }

    public function testTagWithoutNameThrowsException()
    {
        $loader = new YamlFileLoader(new ContainerBuilder(), new FileLocator(self::$fixturesPath.'/yaml'));
        try {
            $loader->load('badtag2.yml');
            $this->fail('->load() should throw an exception when a tag is missing the name key');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->load() throws an InvalidArgumentException if a tag is missing the name key');
            $this->assertStringStartsWith('A "tags" entry is missing a "name" key must be an array for service ', $e->getMessage(), '->load() throws an InvalidArgumentException if a tag is missing the name key');
        }
    }*/
}
