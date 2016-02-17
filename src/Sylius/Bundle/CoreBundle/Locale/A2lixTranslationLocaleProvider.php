<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Locale;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface as A2lixLocaleProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface as SyliusLocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface as SyliusLocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class A2lixTranslationLocaleProvider implements A2lixLocaleProviderInterface
{
    /**
     * @var SyliusLocaleProviderInterface
     */
    protected $syliusLocaleProvider;

    /**
     * @var SyliusLocaleContextInterface
     */
    protected $syliusLocaleContext;

    /**
     * @param SyliusLocaleProviderInterface $syliusLocaleProvider
     * @param SyliusLocaleContextInterface $syliusLocaleContext
     */
    public function __construct(
        SyliusLocaleProviderInterface $syliusLocaleProvider,
        SyliusLocaleContextInterface $syliusLocaleContext
    ) {
        $this->syliusLocaleProvider = $syliusLocaleProvider;
        $this->syliusLocaleContext = $syliusLocaleContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        return $this->syliusLocaleProvider->getAvailableLocales();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->syliusLocaleContext->getDefaultLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredLocales()
    {
        return [$this->syliusLocaleContext->getDefaultLocale()];
    }
}
