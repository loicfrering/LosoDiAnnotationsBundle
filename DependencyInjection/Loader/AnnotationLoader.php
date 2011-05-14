<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * AnnotationLoader loads annotated class service definitions.
 *
 * @author Loïc Frering <loic.frering@gmail.com>
 */
class AnnotationLoader extends Loader
{
    private $container;
    private $reader;
    private $annotations = array();

    public function  __construct(ContainerBuilder $container)
    {
        $this->container = $container;
        $this->annotations = array(
            'LoSo\LosoBundle\DependencyInjection\Loader\Annotations\Service',
            'LoSo\LosoBundle\DependencyInjection\Loader\Annotations\Inject',
            'LoSo\LosoBundle\DependencyInjection\Loader\Annotations\Value'
        );
        $this->reader = new AnnotationReader();
        $this->reader->setDefaultAnnotationNamespace('LoSo\LosoBundle\DependencyInjection\Loader\Annotations\\');
        $this->reader->setAutoloadAnnotations(true);
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

        foreach ($this->annotations as $annotClass) {
            if ($annot = $this->reader->getClassAnnotation($reflClass, $annotClass)) {
                if (null === $definition) {
                    $definition = new Definition($reflClass->getName());
                }
                $id = $annot->define($reflClass, $definition);
            }
        }
        if (null !== $id) {
            $this->reflectProperties($reflClass, $definition);
            $this->reflectMethods($reflClass, $definition);
            $this->reflectConstructor($reflClass, $definition);

            $this->container->setDefinition($id, $definition);
        }
    }

    private function reflectProperties($reflClass, $definition)
    {
        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->annotations as $annotClass) {
                if ($annot = $this->reader->getPropertyAnnotation($property, $annotClass)) {
                    $annot->defineFromProperty($property, $definition);
                }
            }
        }
    }

    private function reflectMethods($reflClass, $definition)
    {
        foreach ($reflClass->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() == $reflClass->getName() && strpos($method->getName(), 'set') === 0) {
                foreach ($this->annotations as $annotClass) {
                    if ($annot = $this->reader->getMethodAnnotation($method, $annotClass)) {
                        $annot->defineFromMethod($method, $definition);
                    }
                }
            }
        }
    }

    private function reflectConstructor($reflClass, $definition)
    {
        try {
            $constructor = $reflClass->getMethod('__construct');
            foreach ($this->annotations as $annotClass) {
                if ($annot = $this->reader->getMethodAnnotation($constructor, $annotClass)) {
                    $annot->defineFromConstructor($constructor, $definition);
                }
            }
        } catch (\ReflectionException $e) {
            // No constructor
        }
    }
}
