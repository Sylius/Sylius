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

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class ChannelContextBuilder implements SerializerContextBuilderInterface
{
    private SerializerContextBuilderInterface $decoratedContextBuilder;

    private ChannelContextInterface $channelContext;

    public function __construct(SerializerContextBuilderInterface $decoratedContextBuilder, ChannelContextInterface $channelContext)
    {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->channelContext = $channelContext;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        try {
            $context[ContextKeys::CHANNEL] = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
        }

        return $context;
    }
}
