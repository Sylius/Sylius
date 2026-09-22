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
use Sylius\Bundle\CoreBundle\Command\Account\ResendVerificationEmail;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final readonly class ResendVerificationEmailContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decoratedContextBuilder,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        if (($context['input']['class'] ?? null) !== ResendVerificationEmail::class) {
            return $context;
        }

        $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][ResendVerificationEmail::class] = array_merge(
            $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][ResendVerificationEmail::class] ?? [],
            [
                'channelCode' => $this->channelContext->getChannel()->getCode(),
                'localeCode' => $this->localeContext->getLocaleCode(),
            ],
        );

        return $context;
    }
}
