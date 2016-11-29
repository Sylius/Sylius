<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProviderBasedLocaleContext implements LocaleContextInterface
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        $localeCode = $this->localeProvider->getDefaultLocaleCode();

        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
