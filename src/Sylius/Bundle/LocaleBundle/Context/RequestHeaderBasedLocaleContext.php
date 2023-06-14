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

final class RequestHeaderBasedLocaleContext implements LocaleContextInterface
{
    public function __construct(private RequestStack $requestStack, private LocaleProviderInterface $localeProvider)
    {
    }

    public function getLocaleCode(): string
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            throw new LocaleNotFoundException('No main request available.');
        }

        $localeCode = $request->headers->get('Accept-Language');
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale is set on the master request\'s headers.');
        }

        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
