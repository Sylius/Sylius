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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

/** @experimental */
final class ChannelCodeAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    public function transform($object, string $to, array $context = [])
    {
        $channel = $this->channelContext->getChannel();

        $object->setChannelCode($channel->getCode());

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof ChannelCodeAwareInterface;
    }
}
