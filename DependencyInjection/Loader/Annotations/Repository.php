<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;
use LoSo\LosoBundle\DependencyInjection\Loader\DoctrineServicesUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class Repository extends Annotation
{
    public $entity;
    public $entityManager;
    public $name;
}
