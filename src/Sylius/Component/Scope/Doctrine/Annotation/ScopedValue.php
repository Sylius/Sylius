<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Scope\Doctrine\Annotation;

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