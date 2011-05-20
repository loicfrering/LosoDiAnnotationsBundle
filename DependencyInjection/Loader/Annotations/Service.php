<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
