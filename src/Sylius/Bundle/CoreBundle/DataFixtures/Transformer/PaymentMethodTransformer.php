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
use Sylius\Component\Core\Formatter\StringInflector;

final class PaymentMethodTransformer implements PaymentMethodTransformerInterface
{
    public function __construct(private ChannelFactoryInterface $channelFactory)
    {
    }

    public function transform(array $attributes): array
    {
        if (null === $attributes['code']) {
            $attributes['code'] = StringInflector::nameToCode($attributes['name']);
        }

        $channels = [];
        foreach ($attributes['channels'] as $channel) {
            if (\is_string($channel)) {
                $channel = $this->channelFactory::findOrCreate(['code' => $channel]);
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
