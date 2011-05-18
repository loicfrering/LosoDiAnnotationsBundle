<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use LoSo\LosoBundle\DependencyInjection\Loader\AnnotationLoader;

class AnnotationLoaderTest extends \PHPUnit_Framework_TestCase
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
        $loader = new AnnotationLoader($container);
        $loader->useDefaultAnnotationNamespace(true);
        $loader->load(self::$fixturesPath . '/annotations/' . $path);
        return $container->getDefinitions();
    }

    public function testServiceDefinition()
    {
        $services = self::loadServices('service');

        $this->assertTrue(isset($services['foo']), '->load() parses service elements');
        $this->assertEquals('Symfony\\Component\\DependencyInjection\\Definition', get_class($services['foo']), '->load() converts service element to Definition instances');
        $this->assertEquals('FooClass', $services['foo']->getClass(), '->load() parses the class attribute');

        $this->assertEquals('container', $services['scope.container']->getScope());
        $this->assertEquals('custom', $services['scope.custom']->getScope());
        $this->assertEquals('prototype', $services['scope.prototype']->getScope());

        $this->assertEquals('sc_configure', $services['configurator1']->getConfigurator(), '->load() parses the configurator tag');
        $this->assertEquals(array(new Reference('baz'), 'configure'), $services['configurator2']->getConfigurator(), '->load() parses the configurator tag');
        $this->assertEquals(array('BazClass', 'configureStatic'), $services['configurator3']->getConfigurator(), '->load() parses the configurator tag');

        $this->assertEquals('getInstance', $services['constructor']->getFactoryMethod(), '->load() parses the factory_method attribute');
        $this->assertEquals('foo', $services['factory.service']->getFactoryService());

        /*$this->fail('Not implemented yet.');
        $aliases = $container->getAliases();
        $this->assertTrue(isset($aliases['alias_for_foo']), '->load() parses aliases');
        $this->assertEquals('foo', (string) $aliases['alias_for_foo'], '->load() parses aliases');
        $this->assertTrue($aliases['alias_for_foo']->isPublic());
        $this->assertTrue(isset($aliases['another_alias_for_foo']));
        $this->assertEquals('foo', (string) $aliases['another_alias_for_foo']);
        $this->assertFalse($aliases['another_alias_for_foo']->isPublic());*/
    }

    public function testServiceNameResolution()
    {
        $services = self::loadServices('service');

        $this->assertTrue(isset($services[strtolower('barClass')]));
        $this->assertTrue(isset($services[strtolower('barClassOldNamespace')]));
        $this->assertTrue(isset($services[strtolower('barClassNamespace')]));
    }

    public function testExceptionCorrectlyThrownForInvalidInjection()
    {
        $container = new ContainerBuilder();
        $loader = new AnnotationLoader($container);
        $loader->useDefaultAnnotationNamespace(true);

        try {
            $loader->load(self::$fixturesPath . '/annotations/inject/invalid/invalid1');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalid1::setDependencies"', $e->getMessage());
        }

        try {
            $loader->load(self::$fixturesPath . '/annotations/inject/invalid/invalid2');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalid2::setDependencies"', $e->getMessage());
        }

        try {
            $loader->load(self::$fixturesPath . '/annotations/inject/invalid/invalid3');
            $this->fail('InvalidArgumentException not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertEquals('Annotation "@Inject" when specifying services id must have one id per method argument for "FooClassInvalid3::setDependencies"', $e->getMessage());
        }
    }

    public function testConstructorBasedInjection()
    {
        $services = self::loadServices('inject/valid');

        $this->assertEquals(array(new Reference('fooService'), new Reference('barService')), $services['constructor.injection']->getArguments());
        //$this->assertEquals(array('foo', new Reference('foo'), array(true, false)), $services['arguments']->getArguments(), '->load() parses the argument tags');
    }

    public function testSetterBasedInjection()
    {
        $services = self::loadServices('inject/valid');
        $methodCalls = $services['setter.injection']->getMethodCalls();

        $this->assertEquals(array('setFooService', array(new Reference('fooService'))), $methodCalls[0]);
        $this->assertEquals(array('setBarService', array(new Reference('bar'))), $methodCalls[1]);
        $this->assertEquals(array('setDependencies', array(new Reference('fooService'), new Reference('barService'))), $methodCalls[2]);
        $this->assertEquals(array('setNamedDependencies', array(new Reference('foo'), new Reference('bar'))), $methodCalls[3]);
    }

    public function testPropertyBasedInjection()
    {
        $services = self::loadServices('inject/valid');
        $methodCalls = $services['property.injection']->getMethodCalls();

        $this->assertEquals(array('setFooService', array(new Reference('fooService'))), $methodCalls[0]);
        $this->assertEquals(array('setBarService', array(new Reference('bar'))), $methodCalls[1]);
    }

    public function testSupports()
    {
        $loader = new AnnotationLoader(new ContainerBuilder());

        $this->assertTrue($loader->supports(self::$fixturesPath), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports(self::$fixturesPath . '/FooClass.php'), '->supports() returns false if the resource is not loadable');
    }

    /*public function testNonArrayTagThrowsException()
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
