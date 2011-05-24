<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader\Annotation\DefinitionBuilder;

use Doctrine\Common\Annotations\AnnotationReader;
use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\RepositoryDefinitionBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class RepositoryDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
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
        $this->builder = new RepositoryDefinitionBuilder($reader);
        $this->reader = $reader;
    }

    private function buildDefinition($class, $path)
    {
        if (!class_exists($class, false)) {
            require $this->fixturesPath . '/' . $path . $class . '.php';
        }
        return $this->builder->build(new \ReflectionClass($class), $this->reader->getClassAnnotation(new \ReflectionClass($class), 'LoSo\LosoBundle\DependencyInjection\Annotations\Repository'));
    }

    public function testRepositoryIdResolution()
    {
        $definitionHolder = $this->buildDefinition('FooRepository', 'annotations/repository/valid/');
        $this->assertEquals('fooRepository', $definitionHolder['id']);

        $definitionHolder = $this->buildDefinition('FooRepositoryWithParticularEntityManager', 'annotations/repository/valid/');
        $this->assertEquals('test.foo.repository', $definitionHolder['id']);
    }

    public function testDefinition()
    {
        $definitionHolder = $this->buildDefinition('FooRepository', 'annotations/repository/valid/');
        $definition = $definitionHolder['definition'];

        $this->assertNotNull($definition);
        $this->assertEquals('FooRepository', $definition->getClass());

        $tag = $definition->getTag('loso.doctrine.repository');
        $this->assertNotEmpty($tag);
        $this->assertEquals(1, count($tag));
        $this->assertEquals('FooEntity', $tag[0]['entity']);
        $this->assertEquals('default', $tag[0]['entityManager']);
    }

    public function testParticularEntityManager()
    {
        $definitionHolder = $this->buildDefinition('FooRepositoryWithParticularEntityManager', 'annotations/repository/valid/');
        $definition = $definitionHolder['definition'];

        $this->assertNotNull($definition);
        $this->assertEquals('FooRepositoryWithParticularEntityManager', $definition->getClass());

        $tag = $definition->getTag('loso.doctrine.repository');
        $this->assertNotEmpty($tag);
        $this->assertEquals(1, count($tag));
        $this->assertEquals('FooEntity', $tag[0]['entity']);
        $this->assertEquals('test', $tag[0]['entityManager']);
    }

    public function testInvalidArgumentExceptionThrownForInvalidRepository()
    {
        try {
            $this->buildDefinition('InvalidFooRepository', 'annotations/repository/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Entity name must be setted in @Repository for class "InvalidFooRepository".', $e->getMessage());
        }
    }
}
