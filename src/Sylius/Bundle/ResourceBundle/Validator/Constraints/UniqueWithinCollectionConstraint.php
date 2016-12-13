<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Validator\Constraints;

use Sylius\Bundle\ResourceBundle\Validator\UniqueWithinCollectionConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UniqueWithinCollectionConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This code must be unique within this collection.';

    /**
     * @var string
     */
    public $attributePath = 'code';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return UniqueWithinCollectionConstraintValidator::class;
    }
}
