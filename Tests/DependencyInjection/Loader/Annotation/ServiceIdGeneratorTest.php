<?php
namespace Loso\Bundle\DiAnnotationsBundle\Tests\DependencyInjection\Loader\Annotation;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\Annotation\ServiceIdGenerator;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class ServiceIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $serviceIdGenerator;
    private $reflClassStub;

    public function setUp()
    {
        $this->serviceIdGenerator = new ServiceIdGenerator();

        $this->reflClassStub = $this->getMockBuilder('\ReflectionClass')
                                    ->disableOriginalConstructor()
                                    ->getMock();
    }

    public function testGenerate()
    {
        $this->reflClassStub->expects($this->any())
                            ->method('getName')
                            ->will($this->returnValue('UserRepository'));

        $this->assertEquals('userRepository', $this->serviceIdGenerator->generate($this->reflClassStub));
    }

    public function testGenerateSupportsNamespaces()
    {
        $this->reflClassStub->expects($this->any())
                            ->method('getName')
                            ->will($this->returnValue('\Loso\Bundle\DiAnnotationsBundle\UserRepository'));

        $this->assertEquals('userRepository', $this->serviceIdGenerator->generate($this->reflClassStub));
    }

    public function testGenerateSupportsOldNamespaces()
    {
        $this->reflClassStub->expects($this->any())
                            ->method('getName')
                            ->will($this->returnValue('Loso_Bundle_DiAnnotationsBundle_UserRepository'));

        $this->assertEquals('userRepository', $this->serviceIdGenerator->generate($this->reflClassStub));
    }
}
