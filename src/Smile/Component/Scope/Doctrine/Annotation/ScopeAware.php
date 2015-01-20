<?php

namespace Smile\Component\Scope\Doctrine\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * This annotation indicates the many-to-one relation
 * to the scoped values
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class ScopeAware
{
    /**
     * @var string
     * @Required
     */
    public $targetEntity;
}