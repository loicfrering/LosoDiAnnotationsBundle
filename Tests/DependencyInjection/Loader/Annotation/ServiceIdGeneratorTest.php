<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader\Annotation;

use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\ServiceIdGenerator;

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
                            ->will($this->returnValue('\LoSo\LosoBundle\UserRepository'));

        $this->assertEquals('userRepository', $this->serviceIdGenerator->generate($this->reflClassStub));
    }

    public function testGenerateSupportsOldNamespaces()
    {
        $this->reflClassStub->expects($this->any())
                            ->method('getName')
                            ->will($this->returnValue('LoSo_LosoBundle_UserRepository'));

        $this->assertEquals('userRepository', $this->serviceIdGenerator->generate($this->reflClassStub));
    }
}
