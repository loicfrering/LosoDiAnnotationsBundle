<?php
namespace LoSo\LosoBundle\Test\DependencyInjection;

use LoSo\LosoBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

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
}
