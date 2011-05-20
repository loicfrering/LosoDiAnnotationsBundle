<?php

namespace LoSo\LosoBundle\DependencyInjection\Loader\Annotations;

use Doctrine\Common\Annotations\Annotation;

final class Repository extends Annotation
{
    public $entity;
    public $entityManager;
    public $name;
}
