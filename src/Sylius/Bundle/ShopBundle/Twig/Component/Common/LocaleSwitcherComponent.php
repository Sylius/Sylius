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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class LocaleSwitcherComponent
{
    use HookableComponentTrait;

    public function __construct(
        protected LocaleContextInterface $localeContext,
        protected LocaleProviderInterface $localeProvider,
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
