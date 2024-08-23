<?php

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class LocaleSwitcher
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public function __construct(
        private readonly LocaleContextInterface $localeContext,
        private readonly LocaleProviderInterface $localeProvider,
    ) {
    }

    #[ExposeInTemplate('active_locale')]
    public function activeLocale(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    /**
     * @return array<string>
     */
    #[ExposeInTemplate('available_locales')]
    public function availableLocales(): array
    {
        return $this->localeProvider->getAvailableLocalesCodes();
    }
}
