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

use Sylius\Bundle\ResourceBundle\Validator\WithinCollectionUniqueCodeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class WithinCollectionUniqueCode extends Constraint
{
    public $message = 'This code must be unique within this collection.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return WithinCollectionUniqueCodeValidator::class;
    }
}
