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

namespace Sylius\Bundle\LocaleBundle\Context;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Locale context implementation based on Symfony Request's language negotiation (RFC 4647 based).
 *
 * @see Request::getPreferredLanguage()
 */
final class RequestHeaderBasedLocaleContext implements LocaleContextInterface
{
    public function __construct(private RequestStack $requestStack, private LocaleProviderInterface $localeProvider)
    {
    }

    public function getLocaleCode(): string
    {
        $request = $this->getMainRequest();

        if (null === $request) {
            throw new LocaleNotFoundException('No main request available.');
        }

        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();

        // Request::getPreferredLanguage() returns first available locale code if none matches. To allow detection of
        // this unwanted behavior, we will prepend special locale code to the list of available locale codes.
        $prependedAvailableLocalesCodes = array_merge(['FIRSTLOCALECODE'], $availableLocalesCodes);

        $bestLocaleCode = $request->getPreferredLanguage($prependedAvailableLocalesCodes);
        if ('FIRSTLOCALECODE' === $bestLocaleCode) {
            throw new LocaleNotFoundException(sprintf(
                'None of the available locales is acceptable: "%s".',
                implode('", "', $availableLocalesCodes),
            ));
        }

        return $bestLocaleCode;
    }

    private function getMainRequest(): ?Request
    {
        if (\method_exists($this->requestStack, 'getMainRequest')) {
            return $this->requestStack->getMainRequest();
        }

       return $this->requestStack->getMasterRequest();
    }
}
