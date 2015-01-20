<?php

namespace Smile\Component\Scope\Doctrine\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * This annotation indicates the one-to-many relation
 * to the scope aware object
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class ScopedValue
{
    /**
     * @var string
     * @Required
     */
    public $targetEntity;
}