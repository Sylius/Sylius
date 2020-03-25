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
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class LocaleContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $localeContext;
    private $translationLocaleProvider;

    public function __construct(SerializerContextBuilderInterface $decorated, LocaleContextInterface $localeContext, TranslationLocaleProviderInterface $translationLocaleProvider)
    {
        $this->decorated = $decorated;
        $this->localeContext = $localeContext;
        $this->translationLocaleProvider = $translationLocaleProvider;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        $context[ContextKeys::LOCALE_CODE] = $this->localeContext->getLocaleCode();
        $context[ContextKeys::FALLBACK_LOCALE_CODE] = $this->translationLocaleProvider->getDefaultLocaleCode();

        return $context;
    }
}
