<?php
namespace Loso\Bundle\DiAnnotationsBundle\Tests\Repository;

use Loso\Bundle\DiAnnotationsBundle\Repository\GenericRepository;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class GenericRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $emMock;
    private $cmMock;

    public function setUp()
    {
        $this->emMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('persist', 'merge', 'remove', 'flush'))
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->cmMock = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
                             ->disableOriginalConstructor()
                             ->getMock();
    }

    public function testCreate()
    {
        $entity = 'test';
        $this->emMock->expects($this->once())
                                ->method('persist')
                                ->with($this->equalTo($entity));

        $genericRepository = new GenericRepository($this->emMock, $this->cmMock);
        $genericRepository->create($entity);
    }

    public function testUpdate()
    {
        $entity = 'test';
        $this->emMock->expects($this->once())
                                ->method('merge')
                                ->with($this->equalTo($entity));

        $genericRepository = new GenericRepository($this->emMock, $this->cmMock);
        $genericRepository->update($entity);
    }

    public function testDelete()
    {
        $entity = 'test';
        $this->emMock->expects($this->once())
                                ->method('remove')
                                ->with($this->equalTo($entity));

        $genericRepository = new GenericRepository($this->emMock, $this->cmMock);
        $genericRepository->delete($entity);
    }

    public function testFlush()
    {
        $this->emMock->expects($this->once())
                                ->method('flush');

        $genericRepository = new GenericRepository($this->emMock, $this->cmMock);
        $genericRepository->flush();
    }
}
