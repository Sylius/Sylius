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
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateChannelTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateCountryTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateCustomerTrait;

final class OrderTransformer implements OrderTransformerInterface
{
    use RandomOrCreateChannelTrait;
    use RandomOrCreateCountryTrait;
    use RandomOrCreateCustomerTrait;
    use TransformChannelAttributeTrait;
    use TransformCustomerAttributeTrait;
    use TransformCountryAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        if (null === $attributes['channel']) {
            $attributes['channel'] = $this->randomOrCreateChannel($this->eventDispatcher);
        }

        if (null === $attributes['customer']) {
            $attributes['customer'] = $this->randomOrCreateCustomer($this->eventDispatcher);
        }

        if (null === $attributes['country']) {
            $attributes['country'] = $this->randomOrCreateCountry($this->eventDispatcher);
        }

        $attributes = $this->transformChannelAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformCustomerAttribute($this->eventDispatcher, $attributes);

        return $this->transformCountryAttribute($this->eventDispatcher, $attributes);
    }
}
