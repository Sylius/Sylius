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

/**
 * Class ActiveRedirectRoutes
 *
 * Constraint for checking a active redirects
 */
final class ActiveRedirectRoutes extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.url_redirects.multiple_active_routes';

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
        return 'sylius_url_active_route_validator';
    }
}
