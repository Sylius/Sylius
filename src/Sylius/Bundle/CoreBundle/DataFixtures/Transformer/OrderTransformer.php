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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;

final class OrderTransformer implements OrderTransformerInterface
{
    use TransformChannelAttributeTrait;
    use TransformCustomerAttributeTrait;
    use TransformCountryAttributeTrait;

    public function __construct(
        private ChannelFactoryInterface $channelFactory,
        private CustomerFactoryInterface $customerFactory,
        private CountryFactoryInterface $countryFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformChannelAttribute($attributes);
        $attributes = $this->transformCustomerAttribute($attributes);
        $attributes = $this->transformCountryAttribute($attributes);

        return $attributes;
    }
}
