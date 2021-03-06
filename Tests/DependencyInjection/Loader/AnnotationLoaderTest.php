<?php
namespace Loso\Bundle\DiAnnotationsBundle\Tests\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\AnnotationLoader;

class AnnotationLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $fixturesPath;

    public function setUp()
    {
        $this->fixturesPath = realpath(__DIR__ . '/../Fixtures/');

        $this->serviceBuilder = $this->getMockBuilder('\Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder')
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $this->repositoryBuilder = $this->getMockBuilder('\Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\RepositoryDefinitionBuilder')
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $this->controllerBuilder = $this->getMockBuilder('\Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\ControllerDefinitionBuilder')
                                        ->disableOriginalConstructor()
                                        ->getMock();
    }

    public function testLoadServices()
    {
        $container = new ContainerBuilder();

        $buildCallback = function ($reflClass, $annot) {
             return array('id' => lcfirst($reflClass->getName()), 'definition' => new Definition($reflClass->getName()));
         };

        $this->serviceBuilder->expects($this->exactly(27))
                             ->method('build')
                             ->will($this->returnCallback($buildCallback));

        $this->repositoryBuilder->expects($this->exactly(3))
                             ->method('build')
                             ->will($this->returnCallback($buildCallback));

        $this->controllerBuilder->expects($this->exactly(2))
                             ->method('build')
                             ->will($this->returnCallback($buildCallback));

        $builders = array(
            'Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Service' => $this->serviceBuilder,
            'Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Repository' => $this->repositoryBuilder,
            'Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations\Controller' => $this->controllerBuilder
        );
        $loader = new AnnotationLoader($container, $builders);

        $loader->load($this->fixturesPath . '/annotations');

        $definitions = $container->getDefinitions();
        $this->assertEquals(32, count($definitions));
        $this->assertArrayHasKey(strtolower('fooClass'), $definitions);
        $this->assertEquals('FooClass', $definitions[strtolower('fooclass')]->getClass());
    }

    public function testSupports()
    {
        $loader = new AnnotationLoader(new ContainerBuilder());

        $this->assertTrue($loader->supports($this->fixturesPath), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports($this->fixturesPath . '/FooClass.php'), '->supports() returns false if the resource is not loadable');
    }
}
