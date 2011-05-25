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
        $definition = $this->buildDefinition('FooClassConstructorInjection1', 'annotations/inject/valid/');
        $this->assertEquals(array(new Reference('fooService')), $definition->getArguments());

        $definition = $this->buildDefinition('FooClassConstructorInjection2', 'annotations/inject/valid/');
        $this->assertEquals(array(new Reference('foo')), $definition->getArguments());

        $definition = $this->buildDefinition('FooClassConstructorInjection3', 'annotations/inject/valid/');
        $this->assertEquals(array(new Reference('fooService'), new Reference('barService')), $definition->getArguments());

        $definition = $this->buildDefinition('FooClassConstructorInjection4', 'annotations/inject/valid/');
        $this->assertEquals(array(new Reference('foo'), new Reference('bar')), $definition->getArguments());

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

    public function testExceptionCorrectlyThrownForInvalidConstructorInjection()
    {
        try {
            $this->buildDefinition('FooClassInvalidConstructorInjection1', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalidConstructorInjection1::__construct"', $e->getMessage());
        }

        try {
            $this->buildDefinition('FooClassInvalidConstructorInjection2', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalidConstructorInjection2::__construct"', $e->getMessage());
        }

        try {
            $this->buildDefinition('FooClassInvalidConstructorInjection3', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalidConstructorInjection3::__construct"', $e->getMessage());
        }
    }

    public function testExceptionCorrectlyThrownForInvalidPropertyInjection()
    {
        try {
            $this->buildDefinition('FooClassInvalidPropertyInjection', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id on property must have one string value for "FooClassInvalidPropertyInjection::fooService"', $e->getMessage());
        }
    }

    public function testExceptionCorrectlyThrownForInvalidSetterInjection()
    {
        try {
            $this->buildDefinition('FooClassInvalidSetterInjection1', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalidSetterInjection1::setDependencies"', $e->getMessage());
        }

        try {
            $this->buildDefinition('FooClassInvalidSetterInjection2', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalidSetterInjection2::setDependencies"', $e->getMessage());
        }

        try {
            $this->buildDefinition('FooClassInvalidSetterInjection3', 'annotations/inject/invalid/');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalidSetterInjection3::setDependencies"', $e->getMessage());
        }
    }
}
