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
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CannotDisableLocale extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The base locale cannot be disabled';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_cannot_disable_locale';
    }
}
