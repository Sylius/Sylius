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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class HasEnabledEntity extends Constraint
{
    /**
     * @var string|null
     */
    public $objectManager;

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
    public $errorPath;

    /**
     * @var string
     */
    public $enabledPath = 'enabled';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'sylius_has_enabled_entity';
    }
}
