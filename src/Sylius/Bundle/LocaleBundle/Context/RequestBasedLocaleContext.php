<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Context;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RequestBasedLocaleContext implements LocaleContextInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param RequestStack $requestStack
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(RequestStack $requestStack, LocaleProviderInterface $localeProvider)
    {
        $this->requestStack = $requestStack;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            throw new LocaleNotFoundException('No master request available.');
        }

        $localeCode = $request->attributes->get('_locale');
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale attribute is set on the master request.');
        }

        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
