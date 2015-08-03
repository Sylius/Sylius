<?php

namespace Sylius\Bundle\CoreBundle\Locale;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface as A2lixLocaleProviderInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface as SyliusLocaleProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface as SyliusLocaleContextInterface;

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
        return array($this->syliusLocaleContext->getDefaultLocale());
    }
}