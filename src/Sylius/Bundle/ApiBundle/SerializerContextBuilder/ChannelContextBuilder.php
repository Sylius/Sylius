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
use Symfony\Component\HttpFoundation\Request;

final class ChannelContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $channelContext;

    public function __construct(SerializerContextBuilderInterface $decorated, ChannelContextInterface $channelContext)
    {
        $this->decorated = $decorated;
        $this->channelContext = $channelContext;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        $context[ContextKeys::CHANNEL] = $this->channelContext->getChannel();

        return $context;
    }
}
