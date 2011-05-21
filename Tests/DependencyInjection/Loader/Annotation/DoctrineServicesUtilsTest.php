<?php
namespace LoSo\LosoBundle\Tests\DependencyInjection\Loader\Annotation;

use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DoctrineServicesUtils;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class DoctrineServicesUtilsTest extends \PHPUnit_Framework_TestCase
{
    private $doctrineServicesUtils;

    public function setUp()
    {
        $this->doctrineServicesUtils = new DoctrineServicesUtils();
    }

    public function testResolveEntityManagerId()
    {
        $defaultEntityManagerId = $this->doctrineServicesUtils->resolveEntityManagerId('default');
        $this->assertEquals('doctrine.orm.entity_manager', $defaultEntityManagerId);
        $customEntityManagerId = $this->doctrineServicesUtils->resolveEntityManagerId('test');
        $this->assertEquals('doctrine.orm.test_entity_manager', $customEntityManagerId);
    }

    public function testGetEntityManagerReference()
    {
        $defaultEntityManagerRef = $this->doctrineServicesUtils->getEntityManagerReference('default');
        $this->assertEquals(new Reference('doctrine.orm.entity_manager'), $defaultEntityManagerRef);
        $customEntityManagerRef = $this->doctrineServicesUtils->getEntityManagerReference('test');
        $this->assertEquals(new Reference('doctrine.orm.test_entity_manager'), $customEntityManagerRef);
    }

    public function testResolveEntityMetadataId()
    {
        $simpleEntityMetadataId = $this->doctrineServicesUtils->resolveEntityMetadataId('User', 'default');
        $this->assertEquals('loso.doctrine.metadata.default.User', $simpleEntityMetadataId);
        $namespacedEntityMetadataId = $this->doctrineServicesUtils->resolveEntityMetadataId('LoSo\LosoBundle\Entity\User', 'test');
        $this->assertEquals('loso.doctrine.metadata.test.LoSo.LosoBundle.Entity.User', $namespacedEntityMetadataId);
        $aliasedEntityMetadataId = $this->doctrineServicesUtils->resolveEntityMetadataId('LosoBundle:User', 'test');
        $this->assertEquals('loso.doctrine.metadata.test.LosoBundle.User', $aliasedEntityMetadataId);
    }

    public function testGetEntityMetadataReference()
    {
        $simpleEntityMetadataRef = $this->doctrineServicesUtils->getEntityMetadataReference('User', 'default');
        $this->assertEquals(new Reference('loso.doctrine.metadata.default.User'), $simpleEntityMetadataRef);
        $namespacedEntityMetadataRef = $this->doctrineServicesUtils->getEntityMetadataReference('LoSo\LosoBundle\Entity\User', 'test');
        $this->assertEquals(new Reference('loso.doctrine.metadata.test.LoSo.LosoBundle.Entity.User'), $namespacedEntityMetadataRef);
        $aliasedEntityMetadataRef = $this->doctrineServicesUtils->getEntityMetadataReference('LosoBundle:User', 'test');
        $this->assertEquals(new Reference('loso.doctrine.metadata.test.LosoBundle.User'), $aliasedEntityMetadataRef);
    }

    public function testGetEntityMetadataDefinition()
    {
        $definition = $this->doctrineServicesUtils->getEntityMetadataDefinition('LosoBundle:User', 'default');

        $this->assertEquals('Doctrine\ORM\Mapping\ClassMetadata', $definition->getClass());
        $this->assertEquals('doctrine.orm.entity_manager', $definition->getFactoryService());
        $this->assertEquals('getClassMetadata', $definition->getFactoryMethod());
        $this->assertEquals(array('LosoBundle:User'), $definition->getArguments());
    }
}
