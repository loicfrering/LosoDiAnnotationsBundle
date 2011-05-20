<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\ServiceDefinitionBuilder;
use LoSo\LosoBundle\DependencyInjection\Loader\Annotation\DefinitionBuilder\RepositoryDefinitionBuilder;

/**
 * AnnotationLoader loads annotated class service definitions.
 *
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class AnnotationLoader extends Loader
{
    private $container;
    private $reader;
    private $builders = array();

    public function  __construct(ContainerBuilder $container)
    {
        $this->container = $container;
        $this->reader = new AnnotationReader();
        $this->reader->setAutoloadAnnotations(true);

        $this->builders = array(
            'LoSo\LosoBundle\DependencyInjection\Annotations\Service' => new ServiceDefinitionBuilder($this->reader),
            'LoSo\LosoBundle\DependencyInjection\Annotations\Repository' => new RepositoryDefinitionBuilder($this->reader)
        );
    }

    public function setAnnotationNamespaceAlias($alias)
    {
        $this->reader->setAnnotationNamespaceAlias('LoSo\LosoBundle\DependencyInjection\Annotations\\', $alias);
        return $this;
    }

    public function useDefaultAnnotationNamespace($useDefaultAnnotationNamespace)
    {
        if ($useDefaultAnnotationNamespace) {
            $this->reader->setDefaultAnnotationNamespace('LoSo\LosoBundle\DependencyInjection\Annotations\\');
        } else {
            $this->reader->setDefaultAnnotationNamespace('');
        }
        return $this;
    }

    public function load($path, $type = null)
    {
        try {
            $includedFiles = array();
            $directoryIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach($directoryIterator as $fileInfo) {
                if($fileInfo->isFile()) {
                    $suffix = strtolower(pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION));
                    if($suffix == 'php') {
                        $sourceFile = realpath($fileInfo->getPathName());
                        require_once $sourceFile;
                        $includedFiles[] = $sourceFile;
                    }
                }
            }

            $declaredClasses = get_declared_classes();
            foreach($declaredClasses as $className) {
                $reflClass = new \ReflectionClass($className);
                if(in_array($reflClass->getFileName(), $includedFiles)) {
                    $this->reflectDefinition($reflClass);
                }
            }
        }
        catch(UnexpectedValueException $e) {

        }
    }

    public function supports($resource, $type = null)
    {
        return is_dir($resource);
    }

    private function reflectDefinition($reflClass)
    {
        $id = null;
        $definition = null;

        foreach ($this->builders as $annotClass => $builder) {
            if ($annot = $this->reader->getClassAnnotation($reflClass, $annotClass)) {
                $definitionHolder = $builder->build($reflClass, $annot);
                $id = $definitionHolder['id'];
                $definition = $definitionHolder['definition'];
                $this->container->setDefinition($id, $definition);
            }
        }
    }
}
