<?php

namespace Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations;

use Doctrine\Common\Annotations\Annotation;

/** @Annotation */
final class Controller extends Annotation
{
    public $name;
}
