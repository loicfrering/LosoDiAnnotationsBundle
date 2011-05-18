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

        $config = $configs[0];
        if (isset($config['service_scan'])) {
            foreach ($config['service_scan'] as $scanName => $scanConfig) {
                if (isset($scanConfig['dir'])) {
                    if (!is_array($scanConfig['dir'])) {
                        $scanConfig['dir'] = array($scanConfig['dir']);
                    }
                    $this->loadDirectories($scanConfig['dir']);
                } else {
                    $this->loadBundle($scanName, $scanConfig, $container);
                }
            }
        }
    }

    private function setUpLoader(ContainerBuilder $container)
    {
        $this->loader = new AnnotationLoader($container);
        $this->loader->setAnnotationNamespaceAlias('loso');
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
                            $this->loadDir($dir);
                        } else {
                            throw new \InvalidArgumentException(sprintf('Invalid base namespace "%s" for bundle "%s".', $baseNamespace, $bundleName));
                        }
                    }
                } else {
                    $this->loadDir($bundleDir);
                }
                $isValidBundle = true;
                break;
            }
        }

        if (!$isValidBundle) {
            throw new \InvalidArgumentException(sprintf('Invalid bundle "%s".', $bundle));
        }
    }

    private function loadDir($dir)
    {
        return $this->loader->load($dir);
    }
}
