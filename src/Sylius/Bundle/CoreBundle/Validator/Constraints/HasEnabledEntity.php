<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
final class HasEnabledEntity extends Constraint
{
    /**
     * @var string|null
     */
    public $objectManager = null;

    /**
     * @var string
     */
    public $message = 'Must have at least one enabled entity';

    /**
     * @var string
     */
    public $repositoryMethod = 'findBy';

    /**
     * @var string|null
     */
    public $errorPath = null;

    /**
     * @var string
     */
    public $enabledPath = 'enabled';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_has_enabled_entity';
    }
}
