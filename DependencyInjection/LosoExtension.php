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
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $configs[0];
        if (isset($config['service_scan'])) {
            foreach ($config['service_scan'] as $scanName => $scanConfig) {
                if (isset($scanConfig['dir']) && is_array($scanConfig['dir'])) {
                    $this->loadDirectory($scanConfig['dir'], $container);
                } else {
                    $this->loadBundle($scanName, $scanConfig, $container);
                }
            }
        }
    }

    private function loadDirectories(array $directories, ContainerBuilder $container)
    {
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $this->loadDir($dir, $container);
            } else {
                throw new \InvalidArgumentException(sprintf('Invalid scan directory "%s".', $dir));
            }
        }
    }

    private function loadBundle($bundle, array $config, ContainerBuilder $container)
    {
        $isValidBundle = false;
        foreach ($container->getParameter('kernel.bundles') as $bundleName => $bundleClass) {
            if ($bundle === $bundleName) {
                $bundle = new \ReflectionClass($bundleClass);
                $bundleDir = dirname($bundle->getFilename());
                if (isset($config['base_namespace']) && is_array($config['base_namespace'])) {
                    foreach ($config['base_namespace'] as $baseNamespace) {
                        $dir = $bundleDir.'/'.$baseNamespace;
                        if (is_dir($dir)) {
                            $this->loadDir($dir, $container);
                        } else {
                            throw new \InvalidArgumentException(sprintf('Invalid base namespace "%s" for bundle "%s".', $baseNamespace, $bundleName));
                        }
                    }
                } else {
                    $this->loadDir($bundleDir, $container);
                }
                $isValidBundle = true;
                break;
            }
        }

        if (!$isValidBundle) {
            throw new \InvalidArgumentException(sprintf('Invalid bundle "%s".', $bundle));
        }
    }

    private function loadDir($dir, ContainerBuilder $container)
    {
        $loader = new AnnotationLoader($container);
        return $loader->load($dir);
    }
}
