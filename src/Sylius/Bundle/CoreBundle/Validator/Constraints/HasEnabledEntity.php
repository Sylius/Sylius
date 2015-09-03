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

/*
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class HasEnabledEntity extends Constraint
{
    public $entityManager = null;
    public $message = 'Must have at least one enabled entity';
    public $repositoryMethod = 'findBy';
    public $errorPath = null;
    public $enabledPath = 'enabled';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'sylius_has_enabled';
    }
}
