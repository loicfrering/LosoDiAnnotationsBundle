<?php
namespace LoSo\LosoBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LoSo\LosoBundle\DependencyInjection\Loader\AnnotationLoader;

/**
 * LosoExtension for LosoBundle.
 *
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class LosoExtension extends Extension
{
    private $loader;

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->setUpLoader($container);
        $bundles = $container->getParameter('kernel.bundles');

        $config = $configs[0];
        if (isset($config['service_scan'])) {
            foreach ($config['service_scan'] as $scanName => $scanConfig) {
                if (isset($scanConfig['dir'])) {
                    if (!is_array($scanConfig['dir'])) {
                        $scanConfig['dir'] = array($scanConfig['dir']);
                    }
                    $this->loadDirectories($scanConfig['dir']);
                } else if(isset($bundles[$scanName])) {
                    $this->loadBundle($scanName, $bundles[$scanName], $scanConfig, $container);
                } else {
                    throw new \UnexpectedValueException(sprintf('Invalid service_scan definition "%s", must be a valid Bundle or define a directory to scan.', $scanName));
                }
            }
        }
    }

    private function setUpLoader(ContainerBuilder $container)
    {
        $this->loader = new AnnotationLoader($container);
    }

    private function loadDirectories(array $directories)
    {
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $this->loadDir($dir);
            } else {
                throw new \InvalidArgumentException(sprintf('Invalid scan directory "%s".', $dir));
            }
        }
    }

    private function loadBundle($bundleName, $bundleClass, array $config)
    {
        $bundle = new \ReflectionClass($bundleClass);
        $bundleDir = dirname($bundle->getFilename());
        if (isset($config['base_namespace']) && is_array($config['base_namespace'])) {
            foreach ($config['base_namespace'] as $baseNamespace) {
                $dir = $bundleDir.'/'.$baseNamespace;
                if (is_dir($dir)) {
                    $this->loadDir($dir);
                } else {
                    throw new \InvalidArgumentException(sprintf('Invalid base namespace "%s" for bundle "%s".', $baseNamespace, $bundleName));
                }
            }
        } else {
            $this->loadDir($bundleDir);
        }
    }

    private function loadDir($dir)
    {
        return $this->loader->load($dir);
    }
}
