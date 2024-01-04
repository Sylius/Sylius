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

namespace Sylius\Bundle\LocaleBundle\Context;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Locale context implementation based on Symfony Request's language negotiation (RFC 4647 based).
 *
 * @see Request::getPreferredLanguage()
 */
final class RequestHeaderBasedLocaleContext implements LocaleContextInterface
{
    private const NO_CODE_VALID_STUB = 'NO_CODE_VALID_STUB';

    /** @var array<array-key, string> */
    private array $availableLocalesCodes = [];

    public function __construct(private RequestStack $requestStack, private LocaleProviderInterface $localeProvider)
    {
    }

    public function getLocaleCode(): string
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            throw new LocaleNotFoundException('No main request available.');
        }

        if ([] === $this->availableLocalesCodes) {
            $this->availableLocalesCodes = array_unique(array_merge(
                [$this->localeProvider->getDefaultLocaleCode()],
                $this->localeProvider->getAvailableLocalesCodes(),
            ));
        }

        // Request::getPreferredLanguage() returns first available locale code if none matches. To allow detection of
        // this unwanted behavior, we will prepend special locale code to the list of available locale codes.
        $prependedAvailableLocalesCodes = array_merge([self::NO_CODE_VALID_STUB], $this->availableLocalesCodes);

        $bestLocaleCode = $request->getPreferredLanguage($prependedAvailableLocalesCodes);
        if (self::NO_CODE_VALID_STUB === $bestLocaleCode) {
            throw new LocaleNotFoundException(sprintf(
                'None of the available locales is acceptable: "%s".',
                implode('", "', $this->availableLocalesCodes),
            ));
        }

        return $bestLocaleCode;
    }
}
