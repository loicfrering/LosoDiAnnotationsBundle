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
        $reader->setAutoloadAnnotations(true);
        $reader->setDefaultAnnotationNamespace('LoSo\LosoBundle\DependencyInjection\Annotations\\');
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
}
