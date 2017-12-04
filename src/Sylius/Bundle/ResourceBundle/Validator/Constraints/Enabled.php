<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Validator\Constraints;

use Sylius\Bundle\ResourceBundle\Validator\EnabledValidator;
use Symfony\Component\Validator\Constraint;

final class Enabled extends Constraint
{
    public $message = 'sylius.resource.not_enabled';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return EnabledValidator::class;
    }
}
