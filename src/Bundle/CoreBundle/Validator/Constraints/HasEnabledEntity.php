<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class HasEnabledEntity extends Constraint
{
    public ?string $objectManager = null;

    public string $message = 'Must have at least one enabled entity';

    public string $repositoryMethod = 'findBy';

    public ?string $errorPath = null;

    public string $enabledPath = 'enabled';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'sylius_has_enabled_entity';
    }
}
