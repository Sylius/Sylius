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
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\Request;

final class LocaleContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface */
    private $decoratedLocaleBuilder;

    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(SerializerContextBuilderInterface $decoratedLocaleBuilder, LocaleContextInterface $localeContext)
    {
        $this->decoratedLocaleBuilder = $decoratedLocaleBuilder;
        $this->localeContext = $localeContext;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedLocaleBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        try {
            $context[ContextKeys::LOCALE_CODE] = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
        }

        return $context;
    }
}
