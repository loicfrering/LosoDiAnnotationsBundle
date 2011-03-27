<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
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
        $definition = new Definition($reflClass->getName());

        if ($annot = $this->reader->getClassAnnotation($reflClass, 'LoSo\LosoBundle\DependencyInjection\Loader\Annotations\Service')) {
            $id = $this->extractServiceName($reflClass, $annot);

            if (isset($annot->scope)) {
                $definition->setScope($annot->scope);
            }

            if (isset($annot->public)) {
                $definition->setPublic($annot->public);
            }

            if (isset($annot->factoryMethod)) {
                $definition->setFactoryMethod($annot->factoryMethod);
            }

            if (isset($annot->factoryService)) {
                $definition->setFactoryService($annot->factoryService);
            }

            if (isset($annot->configurator)) {
                if (is_string($annot->configurator)) {
                    $definition->setConfigurator($annot->configurator);
                } else {
                    $definition->setConfigurator(array($this->resolveServices($annot->configurator[0]), $annot->configurator[1]));
                }
            }

            if (isset($annot->tags)) {
                if (!is_array($annot->tags)) {
                    throw new \InvalidArgumentException(sprintf('Parameter "tags" must be an array for service "%s" in %s.', $id, $reflClass->getName()));
                }
                foreach ($annot->tags as $tag) {
                    if (!isset($tag['name'])) {
                        throw new \InvalidArgumentException(sprintf('A "tags" entry is missing a "name" key must be an array for service "%s" in %s.', $id, $reflClass->getName()));
                    }
                    $name = $tag['name'];
                    unset($tag['name']);

                    $definition->addTag($name, $tag);
                }
            }

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

    private function extractServiceName($reflClass, $annot)
    {
        $serviceName = $annot->value ?: $annot->name;

        if (null === $serviceName) {
            $className = $reflClass->getName();
            if (false !== ($pos = strrpos($className, '_'))) {
                $serviceName = lcfirst(substr($className, $pos + 1));
            } else if (false !== ($pos = strrpos($className, '\\'))) {
                $serviceName = lcfirst(substr($className, $pos + 1));
            } else {
                $serviceName = lcfirst($className);
            }
        }

        return $serviceName;
    }

    /**
     * Resolves services.
     *
     * @param string $value
     * @return void
     */
    private function resolveServices($value)
    {
        if (is_array($value)) {
            $value = array_map(array($this, 'resolveServices'), $value);
        } else if (is_string($value) && 0 === strpos($value, '@')) {
            if (0 === strpos($value, '@?')) {
                $value = substr($value, 2);
                $invalidBehavior = ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } else {
                $value = substr($value, 1);
                $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            }

            if ('=' === substr($value, -1)) {
                $value = substr($value, 0, -1);
                $strict = false;
            } else {
                $strict = true;
            }

            $value = new Reference($value, $invalidBehavior, $strict);
        }

        return $value;
    }
}
