<?php

namespace Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/** @Annotation */
final class Service extends Annotation
{
    public $name;
    public $scope = ContainerInterface::SCOPE_CONTAINER;
    public $public;
    public $configurator;
    public $factoryMethod;
    public $factoryService;
    public $tags = array();
}
