<?php
namespace LoSo\LosoBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LoSo\LosoBundle\DependencyInjection\Loader\AnnotationLoader;

class LosoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        echo '<pre>';
        print_r($configs);
        echo '</pre>';
        $config = $configs[0];
        $paths = array();
        if (isset($config['service_scan'])) {
            foreach ($config['service_scan'] as $scanName => $scanConfig) {
                if (isset($scanConfig['dir']) && is_array($scanConfig['dir'])) {
                    foreach ($scanConfig['dir'] as $dir) {
                        if (is_dir($dir)) {
                            $paths[] = $dir;
                        } else {
                            throw new \InvalidArgumentException('Invalid scan directory "%s".');
                        }
                    }
                } else {
                    foreach ($container->getParameter('kernel.bundles') as $name => $class) {
                        if ($scanName === $name) {
                            $bundle = new \ReflectionClass($class);
                            $bundleDir = dirname($bundle->getFilename());
                            if (isset($scanConfig['base_namespace']) && is_array($scanConfig['base_namespace'])) {
                                foreach ($scanConfig['base_namespace'] as $baseNamespace) {
                                    $path = $bundleDir.'/'.$baseNamespace;
                                    if (is_dir($path)) {
                                        $paths[] = $path;
                                    } else {
                                        throw new \InvalidArgumentException('Invalid base namespace "%s" for bundle "%s".');
                                    }
                                }
                            } else {
                                $paths[] = $bundleDir;
                            }
                            break;
                        }
                    }
                }
            }
        }

        echo '<pre>';
        print_r($paths);
        echo '</pre>';

        foreach ($paths as $path) {
            $this->loadPath($path, $container);
        }
    }

    private function loadPath($path, ContainerBuilder $container)
    {
        $loader = new AnnotationLoader($container);
        return $loader->load($path);
    }
}
