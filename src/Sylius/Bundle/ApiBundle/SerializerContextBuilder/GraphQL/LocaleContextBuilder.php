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
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;

/** @experimental */
final class LocaleContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface */
    private $decoratedContextBuilder;

    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext
    ) {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->localeContext = $localeContext;
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
            $context[ContextKeys::LOCALE_CODE] = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
        }

        return $context;
    }
}
