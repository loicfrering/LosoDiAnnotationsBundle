<?php
namespace Loso\Bundle\DiAnnotationsBundle\Test\DependencyInjection;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private $processor;

    public function setUp()
    {
        $this->processor = new Processor();
    }

    public function testConfiguration()
    {
        $bundles = array('TestBundle');
        $config = array(
            'service_scan' => array(
                'TestBundle' => array(
                    'base_namespace' => 'Service'
                )
            )
        );
        $configs = array($config);
        $configuration = new Configuration($bundles);
        $config = $this->processor->processConfiguration($configuration, $configs);

        $this->assertNotEmpty($config);
    }

    public function testIsBundle()
    {
        $bundles = array('FooBundle', 'BarBundle');
        $configs = array(array(
            'service_scan' => array(
                'FooBundle' => array(),
                'test' => array('dir' => 'test')
            )
        ));
        $configuration = new Configuration($bundles);
        $config = $this->processor->processConfiguration($configuration, $configs);

        $this->assertNotEmpty($config);

        $serviceScan = $config['service_scan'];
        $this->assertTrue($serviceScan['FooBundle']['is_bundle']);
        $this->assertFalse($serviceScan['test']['is_bundle']);
    }

    public function testDirMustNotBeSetForBundles()
    {
        $bundles = array('FooBundle');
        $configs = array(array(
            'service_scan' => array(
                'FooBundle' => array('dir' => 'test')
            )
        ));
        $configuration = new Configuration($bundles);
        try {
            $config = $this->processor->processConfiguration($configuration, $configs);
            $this->fail('Expect InvalidConfigurationException.');
        } catch (InvalidConfigurationException $e) {
            $this->assertEquals('Invalid configuration for path "loso.service_scan.FooBundle": "dir" must not be set for a bundle.', $e->getMessage());
        }
    }

    public function testBaseNamespaceMustOnlyBeSetForBundles()
    {
        $bundles = array('FooBundle');
        $configs = array(array(
            'service_scan' => array(
                'test' => array('dir' => 'test', 'base_namespace' => 'Service')
            )
        ));
        $configuration = new Configuration($bundles);
        try {
            $config = $this->processor->processConfiguration($configuration, $configs);
            $this->fail('Expect InvalidConfigurationException.');
        } catch (InvalidConfigurationException $e) {
            $this->assertEquals('Invalid configuration for path "loso.service_scan.test": "base_namespace" must only be set for a bundle.', $e->getMessage());
        }
    }

    public function testDirMustBeSetForArbitraryKeys()
    {
        $bundles = array('FooBundle');
        $configs = array(array(
            'service_scan' => array(
                'test' => array()
            )
        ));
        $configuration = new Configuration($bundles);
        try {
            $config = $this->processor->processConfiguration($configuration, $configs);
            $this->fail('Expect InvalidConfigurationException.');
        } catch (InvalidConfigurationException $e) {
            $this->assertEquals('Invalid configuration for path "loso.service_scan.test": "dir" must be set for arbitrary keys, define bundles otherwise.', $e->getMessage());
        }
    }

    public function testScalarBaseNamespaceIsConvertedToArray()
    {
        $bundles = array('FooBundle');
        $configs = array(array(
            'service_scan' => array(
                'FooBundle' => array('base_namespace' => 'Service')
            )
        ));
        $configuration = new Configuration($bundles);
        $config = $this->processor->processConfiguration($configuration, $configs);

        $baseNamespaces = $config['service_scan']['FooBundle']['base_namespace'];
        $this->assertTrue(is_array($baseNamespaces));
        $this->assertEquals(1, count($baseNamespaces));
        $this->assertEquals('Service', $baseNamespaces[0]);
    }

    public function testScalarDirIsConvertedToArray()
    {
        $bundles = array('FooBundle');
        $configs = array(array(
            'service_scan' => array(
                'test' => array('dir' => 'testdir')
            )
        ));
        $configuration = new Configuration($bundles);
        $config = $this->processor->processConfiguration($configuration, $configs);

        $baseNamespaces = $config['service_scan']['test']['dir'];
        $this->assertTrue(is_array($baseNamespaces));
        $this->assertEquals(1, count($baseNamespaces));
        $this->assertEquals('testdir', $baseNamespaces[0]);
    }
}
