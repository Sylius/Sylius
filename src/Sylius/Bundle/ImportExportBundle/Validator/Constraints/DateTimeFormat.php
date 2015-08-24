<?php

/*
     * This file is part of the Sylius package.
     *
     * (c) Paweł Jędrzejewski
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DateTimeFormat extends Constraint
{
    public $message = 'The format %format% is not a proper date time format. It is impossible to create date based on this format.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'datetime_format_validator';
    }
}
