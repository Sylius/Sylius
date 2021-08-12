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

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;

/** @experimental */
final class ChannelContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface */
    private $decoratedContextBuilder;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext
    ) {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->channelContext = $channelContext;
    }

    public function create(
        string $resourceClass,
        string $operationName,
        array $resolverContext,
        bool $normalization
    ): array {
        $context = $this->decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        );

        try {
            $context[ContextKeys::CHANNEL] = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
        }

        return $context;
    }
}
