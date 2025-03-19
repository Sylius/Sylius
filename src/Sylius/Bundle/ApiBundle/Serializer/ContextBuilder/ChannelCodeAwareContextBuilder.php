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

namespace Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelCodeAwareContextBuilder extends AbstractInputContextBuilder
{
    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        string $attributeClass,
        string $defaultConstructorArgumentName,
        private readonly ChannelContextInterface $channelContext,
    ) {
        parent::__construct($decoratedContextBuilder, $attributeClass, $defaultConstructorArgumentName);
    }

    protected function supports(Request $request, array $context, ?array $extractedAttributes): bool
    {
        return true;
    }

    protected function resolveValue(array $context, ?array $extractedAttributes): mixed
    {
        return $this->channelContext->getChannel()->getCode();
    }
}
