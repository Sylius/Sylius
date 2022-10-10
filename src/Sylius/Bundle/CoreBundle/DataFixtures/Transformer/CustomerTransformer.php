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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateCustomerGroupTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateCustomerGroupTrait;

final class CustomerTransformer implements CustomerTransformerInterface
{
    use FindOrCreateCustomerGroupTrait;
    use RandomOrCreateCustomerGroupTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        if ('' === $attributes['customer_group']) {
            $attributes['customer_group'] = $this->randomOrCreateCustomerGroup($this->eventDispatcher);
        }

        if (\is_string($attributes['customer_group'])) {
            $attributes['customer_group'] = $this->findOrCreateCustomerGroup($this->eventDispatcher, ['code' => $attributes['customer_group']]);
        }

        return $attributes;
    }
}
