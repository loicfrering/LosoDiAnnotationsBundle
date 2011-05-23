<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader\Annotation\DefinitionBuilder;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class AbstractServiceDefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $fixturesPath;
    private $builder;

    public function setUp()
    {
        $this->fixturesPath = realpath(__DIR__ . '/../../../Fixtures/');
        $reader = new AnnotationReader();
        $reader->setAutoloadAnnotations(true);
        $reader->setDefaultAnnotationNamespace('LoSo\LosoBundle\DependencyInjection\Annotations\\');
        $this->builder = $this->getMockBuilder('\LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\AbstractServiceDefinitionBuilder')
                              ->setConstructorArgs(array($reader))
                              ->getMockForAbstractClass();
    }

    private function buildDefinition($class, $path)
    {
        require $this->fixturesPath . '/' . $path . $class . '.php';
        $definitionHolder = $this->builder->build(new \ReflectionClass($class), null);
        $definition = $definitionHolder['definition'];
        return $definition;
    }

    public function testDefinition()
    {
        $definition = $this->buildDefinition('FooClass', 'annotations/service/');

        $this->assertNotNull($definition);
        $this->assertEquals('FooClass', $definition->getClass());
    }

    public function testConstructorInjection()
    {
        $definition = $this->buildDefinition('FooClassConstructorInjection', 'annotations/inject/valid/');

        $this->assertEquals(array(new Reference('fooService'), new Reference('barService')), $definition->getArguments());
        //$this->assertEquals(array('foo', new Reference('foo'), array(true, false)), $definition->getArguments());
    }

    public function testPropertyInjection()
    {
        $definition = $this->buildDefinition('FooClassPropertyInjection', 'annotations/inject/valid/');
        $methodCalls = $definition->getMethodCalls();

        $this->assertEquals(array('setFooService', array(new Reference('fooService'))), $methodCalls[0]);
        $this->assertEquals(array('setBarService', array(new Reference('bar'))), $methodCalls[1]);
    }

    public function testSettetInjection()
    {
        $definition = $this->buildDefinition('FooClassSetterInjection', 'annotations/inject/valid/');
        $methodCalls = $definition->getMethodCalls();

        $this->assertEquals(array('setFooService', array(new Reference('fooService'))), $methodCalls[0]);
        $this->assertEquals(array('setBarService', array(new Reference('bar'))), $methodCalls[1]);
        $this->assertEquals(array('setDependencies', array(new Reference('fooService'), new Reference('barService'))), $methodCalls[2]);
        $this->assertEquals(array('setNamedDependencies', array(new Reference('foo'), new Reference('bar'))), $methodCalls[3]);
    }

    public function testExceptionCorrectlyThrownForInvalidSetterInjection()
    {
        try {
            $this->buildDefinition('FooClassInvalid1', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalid1::setDependencies"', $e->getMessage());
        }

        try {
            $this->buildDefinition('FooClassInvalid2', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalid2::setDependencies"', $e->getMessage());
        }

        try {
            $this->buildDefinition('FooClassInvalid3', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalid3::setDependencies"', $e->getMessage());
        }
    }
}
