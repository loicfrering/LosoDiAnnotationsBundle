<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader\Annotation\DefinitionBuilder;

use Doctrine\Common\Annotations\AnnotationReader;
use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\ControllerDefinitionBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class ControllerDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
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
        $this->builder = new ControllerDefinitionBuilder($reader);
        $this->reader = $reader;
    }

    private function buildDefinition($class, $path)
    {
        if (!class_exists($class, false)) {
            require $this->fixturesPath . '/' . $path . $class . '.php';
        }
        return $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Controller'));
    }

    public function testControllerIdResolution()
    {
        $definitionHolder = $this->buildDefinition('FooController', 'annotations/controller/');
        $this->assertEquals('fooController', $definitionHolder['id']);

        $definitionHolder = $this->buildDefinition('BarController', 'annotations/controller/');
        $this->assertEquals('bar.controller', $definitionHolder['id']);
    }

    public function testDefinition()
    {
        $definitionHolder = $this->buildDefinition('FooController', 'annotations/controller/');
        $definition = $definitionHolder['definition'];

        $this->assertNotNull($definition);
        $this->assertEquals('FooController', $definition->getClass());

        $tag = $definition->getTag('loso.controller');
        $this->assertNotEmpty($tag);
        $this->assertEquals(1, count($tag));
        $this->assertEmpty($tag[0]);
    }
}
