<?php

namespace Smile\Component\Scope\Doctrine\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * This annotation indicates the property where the scope object
 * must be loaded
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Scope
{
}