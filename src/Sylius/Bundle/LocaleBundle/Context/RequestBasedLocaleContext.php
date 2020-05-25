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
use Sylius\Component\Locale\Provider\NewLocaleProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestBasedLocaleContext implements LocaleContextInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(RequestStack $requestStack, LocaleProviderInterface $localeProvider)
    {
        $this->requestStack = $requestStack;
        $this->localeProvider = $localeProvider;
        if ($localeProvider instanceof NewLocaleProviderInterface) {
            @trigger_error(
                sprintf('Not passing in an instance of %s is deprecated and will be removed in Sylius 2.0', NewLocaleProviderInterface::class),
                \E_USER_DEPRECATED
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            throw new LocaleNotFoundException('No master request available.');
        }

        $localeCode = $request->attributes->get('_locale');
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale attribute is set on the master request.');
        }

        if ($this->localeProvider instanceof NewLocaleProviderInterface) {
            $isLocaleCodeAvailable = $this->localeProvider->isLocaleCodeAvailable($localeCode);
        } else {
            $isLocaleCodeAvailable = in_array($localeCode, $this->localeProvider->getAvailableLocalesCodes(), true);
        }

        if (!$isLocaleCodeAvailable) {
            $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();

            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
